# Architecture — Internal Design

This document explains the internal architecture of Kinetics: how the pipeline works, how context is shared between pipes, and how the response is built.

> This document is intended for contributors or developers who need a deep understanding of Kinetics internals.

---

## Table of Contents

1. [Class Diagram](#1-class-diagram)
2. [Request Lifecycle](#2-request-lifecycle)
3. [Pipeline Internals](#3-pipeline-internals)
4. [TableContext — Shared State](#4-tablecontext--shared-state)
5. [TableConfig — Immutable Configuration](#5-tableconfig--immutable-configuration)
6. [TableResult — Response Builder](#6-tableresult--response-builder)
7. [Column Resolution Flow](#7-column-resolution-flow)
8. [Exception Hierarchy](#8-exception-hierarchy)

---

## 1. Class Diagram

```
Kinetics\Table                          ← Entry point (factory pattern)
│
├── Support\TableConfig                 ← Immutable config (readonly PHP 8.2)
├── Support\TableContext                ← Shared state injected onto the model
│
├── Columns\Column (abstract)           ← Base class for all columns
│   ├── Columns\TextColumn              ← Text column + badge + date formatters
│   └── Columns\ActionColumn            ← Action column (resolved per-row)
│
├── Actions\Action                      ← A single action button/link
└── Actions\ActionGroup                 ← Dropdown of grouped actions
│
├── Pipes\SortPipe                      ← Default: ORDER BY
├── Pipes\SearchPipe                    ← Default: LIKE search
├── Pipes\FilterPipe                    ← Default: WHERE / whereIn
├── Pipes\PaginatePipe                  ← Default: terminal, paginate()
├── Pipes\CursorPaginatePipe            ← Optional: cursor paginate
├── Pipes\DateRangeFilterPipe           ← Optional: date range filter
└── Pipes\RelationSortPipe              ← Optional: JOIN-based sort via relation
│
├── Contracts\PipeInterface             ← Required interface for all pipes
├── Contracts\ColumnInterface           ← Required interface for all columns
│
├── Resources\TableResult               ← Wraps paginator + context → array/JSON
│
├── Exceptions\TableException           ← Base exception
├── Exceptions\InvalidPipeException     ← Pipe does not implement PipeInterface
└── Exceptions\InvalidColumnException   ← Column key is not registered

Console\Commands\MakePipe               ← Artisan: kinetics:pipe
Console\stubs\pipe.stub                 ← Template for new pipe files
```

---

## 2. Request Lifecycle

```
HTTP Request
     │
Table::model(User::class)           // Create a private instance
     │
->columns([...])                    // Store column definitions
->pipes([...])                      // Append to extraPipes
->withoutPipes([...])               // Append to removedPipes
->perPage(), ->defaultSort()        // Update TableConfig (immutable copy)
     │
->make() / ->get() / ->paginate()   // Trigger execution
     │
     ├── buildContext()             // Create TableContext
     │     └── inject onto model:  // $model->datatableContext = $context
     │
     └── runPipeline()             // Assemble + run the pipeline
           │
           ├── assemblePipes()     // Build pipe list: defaults - removed + custom + paginate
           │
           └── Pipeline::send($query)->through($pipes)->thenReturn()
                 │
                 ├── SortPipe::handle()
                 ├── SearchPipe::handle()
                 ├── FilterPipe::handle()
                 ├── [CustomPipes]::handle()
                 └── PaginatePipe::handle()  ← returns LengthAwarePaginator
                       │
               TableResult::__construct($paginator, $context, $columns)
                       │
               TableResult::toArray()  →  { data, columns, meta, state }
```

---

## 3. Pipeline Internals

### How `assemblePipes()` Works

```php
private function assemblePipes(): array
{
    // 1. Take default pipes, excluding PaginatePipe and any removed pipes
    $before = array_filter(
        $this->defaultPipes,                         // [Sort, Search, Filter, Paginate]
        fn($p) => $p !== PaginatePipe::class         // separate Paginate
               && !in_array($p, $this->removedPipes) // drop removed ones
    );

    // 2. Check whether PaginatePipe is still active
    $hasPaginate = !in_array(PaginatePipe::class, $this->removedPipes);

    // 3. Assemble: [Sort, Search, Filter] + [Custom Pipes] + [Paginate?]
    $pipes = [...$before, ...$this->extraPipes];
    if ($hasPaginate) $pipes[] = PaginatePipe::class;

    // 4. Resolve class-strings via the container (supports DI)
    return array_map(fn($p) => is_string($p) ? app($p) : $p, $pipes);
}
```

**Why are custom pipes always inserted before paginate?**

So that custom pipes can still add `WHERE` clauses before the query is executed. Once `PaginatePipe` runs, the query has already been executed and the result is a `LengthAwarePaginator`.

---

### How Context Is Shared with Pipes

Kinetics uses a unique technique: the context is stored as a **dynamic property on the Eloquent model**.

```php
// In Table::buildContext()
$this->query->getModel()->datatableContext = $context;

// In each pipe
$ctx = $query->getModel()->datatableContext ?? null;
```

**Why not use `Pipeline::send($context)`?**

Laravel's Pipeline is designed to `send` a single object. Kinetics needs pipes to receive a `Builder` (so `$next($query)` works), not the context. Storing context on the model is the cleanest approach without changing the pipe signature.

> **PHP 8.2+ note:** Dynamic properties on classes that do not declare them are deprecated. This works because Eloquent's `Model` class explicitly implements `__get`/`__set` to allow dynamic attributes.

---

## 4. TableContext — Shared State

`TableContext` is the "carrier bag" that holds all state needed by the pipes:

```php
class TableContext
{
    public Builder $query;
    public readonly Request $request;
    public readonly TableConfig $config;

    private array $columns;
    private array $meta = [];
}
```

### Key Methods

**Column helpers — filter from column definitions:**
```php
$ctx->getSortableKeys()    // Keys of columns with ->sortable()
$ctx->getSearchableKeys()  // Keys of columns with ->searchable()
$ctx->getFilterableKeys()  // Keys of columns with ->filterable()
```

**Request helpers — parse the query string:**
```php
$ctx->getSortColumn()    // ?string — from ?sort=column
$ctx->getSortDirection() // string  — from ?direction=asc|desc, defaults to 'asc'
$ctx->getSearch()        // ?string — from ?search=, null if < 2 chars
$ctx->getFilters()       // array   — from ?filters[key]=value
$ctx->getPerPage()       // int     — from ?per_page=, capped at maxPerPage
```

**Meta bag — for pipe → response communication:**
```php
$ctx->setMeta('key', $value);  // A pipe stores data
$ctx->getMeta('key');           // The next pipe / TableResult reads it
$ctx->allMeta();                // All meta (merged into the 'meta' response key)
```

---

## 5. TableConfig — Immutable Configuration

`TableConfig` uses PHP 8.2 `readonly` properties and an **immutable update pattern**:

```php
class TableConfig
{
    public function __construct(
        public readonly int $defaultPerPage = 15,
        public readonly int $maxPerPage = 100,
        public readonly string $defaultSort = 'id',
        public readonly string $defaultDirection = 'desc',
        public readonly bool $preserveKeys = false,
        public readonly array $optionsPerPage = [10, 15, 25, 50, 100],
    ) {}

    // Return a new instance with the given fields overridden
    public function with(?int $defaultPerPage = null, ...): static
    {
        return new static(
            defaultPerPage: $defaultPerPage ?? $this->defaultPerPage,
            // ... rest unchanged
        );
    }
}
```

Every call to `Table::perPage()` or `Table::defaultSort()` creates a new `TableConfig` instance — so there are no side effects between separate call chains.

---

## 6. TableResult — Response Builder

`TableResult` is responsible for converting a `LengthAwarePaginator + TableContext` into a structure ready for frontend consumption.

```php
class TableResult implements \JsonSerializable
{
    public function toArray(): array
    {
        return [
            'data'    => $this->getData(),     // rows + formatters + actions
            'columns' => $this->getColumns(),  // column definitions
            'meta'    => $this->getMeta(),     // pagination info
            'state'   => $this->getState(),    // current request state
        ];
    }
}
```

### `transformData()` — How Rows Are Processed

```
Paginator Items (Model[])
     │
     ├── $model->toArray()                    // Convert model to array
     │
     ├── Apply formatters                     // Columns with ->formatUsing()
     │   sourceKey = $column->getSourceKey()  // read from this field in the row
     │   outputKey = $column->getKey()        // write result to this field
     │
     │   $row[$outputKey] = $formatter($row[$sourceKey], $row, $model)
     │
     │   ↳ sourceKey and outputKey are the same unless ->as() was called
     │
     └── Resolve action columns               // Each ActionColumn
         $row['__actions'] = $actionCol->resolveForRow($row)
               │
               ├── Action::resolve($row)       // Evaluate closures, resolve href
               └── ActionGroup::resolve($row)  // Map all Actions inside the group
```

**Why `getSourceKey()` vs `getKey()`?**

When `->as('alias')` is called on a column, the two keys diverge:
- `getSourceKey()` — the original key passed to `make()`, e.g. `created_at`. Used to **look up the value** in the raw row array.
- `getKey()` — the `outputKey`, e.g. `created_at_formatted`. Used to **write the result** back to the row.

This allows multiple columns to read from the same DB field without overwriting each other:

```php
TextColumn::make('created_at')->sortable(),              // sourceKey=created_at, outputKey=created_at
TextColumn::make('created_at')->as('created_at_fmt')->date('d M Y'), // sourceKey=created_at, outputKey=created_at_fmt
```

Both columns read `$row['created_at']`, but write to different keys — `created_at` (unchanged) and `created_at_fmt` (formatted).

### `buildState()` — Request Mirror

The state always reflects **what was requested**, not what was applied. This lets the frontend sync filter/search UI with the server state.

```php
private function buildState(): array
{
    return [
        'sort'      => $request->get('sort'),
        'direction' => $request->get('direction', 'asc'),
        'search'    => $request->get('search', ''),
        'filters'   => (array) $request->get('filters', []),
        'per_page'  => $this->context->getPerPage(),
    ];
}
```

---

## 7. Column Resolution Flow

### TextColumn

```
Column::make('status')                   // sourceKey = 'status', outputKey = 'status'
    ->badge()
    ->color(['active' => 'default', 'inactive' => 'secondary'])
    ->filterable(['active', 'inactive'])
     │
Column::toArray() → {
    key: 'status',          ← outputKey
    label: 'Status',
    sortable: false,
    searchable: false,
    filterable: true,
    filterOptions: ['active', 'inactive'],
    visible: true,
    type: 'badge',
    meta: { color: { active: 'default', inactive: 'secondary' } }
}

// With ->as() — source and output keys differ
Column::make('created_at')               // sourceKey = 'created_at'
    ->as('created_at_formatted')         // outputKey = 'created_at_formatted'
    ->date('d M Y')
     │
Column::toArray() → {
    key: 'created_at_formatted',         ← outputKey used as the column key for frontend
    label: 'Created At',
    ...
}

// In transformData():
$row['created_at_formatted'] = $formatter($row['created_at'], $row, $model)
//    ↑ outputKey (write)                       ↑ sourceKey (read)
```

### ActionColumn

```
ActionColumn::make()
    ->actions([Action::edit('users.edit'), Action::delete('users.destroy')])
     │
Per row:
ActionColumn::resolveForRow($row)
     │
     ├── Action::resolve($row)
     │   ├── Evaluate visibleWhen($row)  → skip if false
     │   ├── Evaluate disabledWhen($row) → set disabled: true
     │   └── resolveHref($row)          → route('users.edit', ['user' => $row['id']])
     │
     └── ActionGroup::resolve($row)
         └── Map all Actions inside the group
```

---

## 8. Exception Hierarchy

```
\RuntimeException
  └── Kinetics\Exceptions\TableException
        ├── InvalidPipeException    → Pipe does not implement PipeInterface
        └── InvalidColumnException  → Column key is not registered in the table
```

### `InvalidPipeException`

Thrown when a pipe passed to `->pipes()` does not implement `PipeInterface`.

```php
InvalidPipeException::doesNotImplementInterface(string $class): static
// "Pipe [App\SomePipe] must implement Kinetics\Contracts\PipeInterface"
```

### `InvalidColumnException`

Currently defined but not actively used. Reserved for future column validation.

```php
InvalidColumnException::notDefined(string $key): static
// "Column [column_key] is not defined in the table."
```

---

## Contracts

### `PipeInterface`

```php
interface PipeInterface
{
    public function handle(Builder $query, Closure $next): mixed;
}
```

All pipes — built-in and custom — must implement this interface.

### `ColumnInterface`

```php
interface ColumnInterface
{
    public function toArray(): array;
    public function getKey(): string;
}
```

Implemented by all columns through the `Column` abstract class.
