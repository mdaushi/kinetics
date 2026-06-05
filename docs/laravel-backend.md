# Backend (Laravel) — Full Reference

This document covers all API and concepts of the `mdaushi/kinetics` backend package.

---

## Table of Contents

1. [Installation](#1-installation)
2. [Configuration](#2-configuration)
3. [Entry Points — `Table`](#3-entry-points--table)
4. [Columns](#4-columns)
   - [TextColumn](#textcolumn)
   - [ActionColumn](#actioncolumn)
5. [Actions](#5-actions)
   - [Action](#action)
   - [ActionGroup](#actiongroup)
6. [Pipeline & Pipes](#6-pipeline--pipes)
   - [Default Pipeline Order](#default-pipeline-order)
   - [Built-in Pipes](#built-in-pipes)
   - [Custom Pipes](#custom-pipes)
7. [Output Methods](#7-output-methods)
8. [Response Shape](#8-response-shape)
9. [Artisan Commands](#9-artisan-commands)
10. [Advanced Usage](#10-advanced-usage)

---

## 1. Installation

```bash
composer require mdaushi/kinetics
```

The package is auto-discovered by Laravel. To publish the configuration file:

```bash
php artisan vendor:publish --tag=kinetics-config
```

**Requirements:**
- PHP `^8.2`
- Laravel `^12.0` or `^13.0`

---

## 2. Configuration

File: `config/kinetics.php`

```php
return [
    // Default number of items per page
    'default_per_page'  => 15,

    // Maximum per-page limit a user can request
    'max_per_page'      => 100,

    // Default sort column
    'default_sort'      => 'id',

    // Default sort direction ('asc' or 'desc')
    'default_direction' => 'desc',

    // Per-page options sent to the frontend
    'options_per_page'  => [10, 15, 25, 50, 100],

    // Output directory when running `kinetics:pipe` (relative to app/)
    'pipe_path'         => 'Pipes',
];
```

> **Note:** Config values are only used as defaults. Values set directly via methods (like `->perPage()`) always take precedence.

---

## 3. Entry Points — `Table`

The `Table` class is the main entry point. It uses a factory pattern and cannot be instantiated directly.

### `Table::model(string $modelClass)`

Use this when you want Kinetics to build a fresh query from an Eloquent model.

```php
use Kinetics\Table;
use App\Models\User;

Table::model(User::class)
    ->columns([...])
    ->make();
```

### `Table::query(Builder $query)`

Use this when you already have a pre-built query (e.g. with eager loading or specific scopes).

```php
$query = User::with('department')->whereNotNull('verified_at');

Table::query($query)
    ->columns([...])
    ->make();
```

---

### Fluent API — Modifier Methods

All methods below are chainable and return `static`:

#### `->columns(array $columns)`

Define the table columns.

```php
->columns([
    TextColumn::make('name')->sortable(),
    TextColumn::make('email'),
    ActionColumn::make()->actions([...]),
])
```

#### `->perPage(int $default, int $max = 100)`

Set the default and maximum items per page. The `$max` value prevents users from requesting too much data at once.

```php
->perPage(15, 100)
```

#### `->defaultSort(string $column, string $direction = 'desc')`

Set the default sort when no sort request comes from the user.

```php
->defaultSort('created_at', 'desc')
->defaultSort('name', 'asc')
```

#### `->pipes(array $pipes)`

Add custom pipes. Custom pipes are always inserted **after** sort/search/filter and **before** paginate.

```php
->pipes([
    TenantScopePipe::class,
    new MyCustomFilterPipe(),
])
```

#### `->withoutPipes(array $pipes)`

Remove default pipes from the pipeline. Useful for replacing the paginator with a custom implementation.

```php
use Kinetics\Pipes\PaginatePipe;
use Kinetics\Pipes\CursorPaginatePipe;

->withoutPipes([PaginatePipe::class])
->pipes([CursorPaginatePipe::class])
```

#### `->tap(Closure $callback)`

Apply additional constraints directly to the Eloquent query without creating a new pipe. Useful for simple scopes.

```php
->tap(fn($q) => $q->where('tenant_id', auth()->user()->tenant_id))
->tap(fn($q) => $q->whereNull('deleted_at'))
```

#### `->withRequest(Request $request)`

Override the request object. Very useful in tests.

```php
->withRequest(new Request(['sort' => 'name', 'direction' => 'asc']))
```

---

## 4. Columns

### `TextColumn`

A generic text column. Extends the `Column` abstract class.

```php
use Kinetics\Columns\TextColumn;

TextColumn::make('column_key')
```

**Auto-label:** If not set manually, the label is derived from the key (e.g. `created_at` → `Created At`).

#### Available Methods

| Method | Description | Example |
|--------|-------------|---------|
| `->label(string)` | Override the column label | `->label('Full Name')` |
| `->as(string)` | Set an alias for the output key in row data | `->as('created_at_formatted')` |
| `->sortable(bool = true)` | Enable sorting | `->sortable()` |
| `->searchable(bool = true)` | Include in global search | `->searchable()` |
| `->filterable(array = [], bool = true)` | Enable filtering with optional predefined options | `->filterable(['admin', 'user'])` |
| `->hidden(bool = true)` | Hide the column from the frontend | `->hidden()` |
| `->meta(string\|array, mixed)` | Attach custom metadata | `->meta('align', 'right')` |
| `->relation(string, string)` | Mark as a relation column | `->relation('department', 'name')` |
| `->formatUsing(Closure)` | Transform the value after fetching | `->formatUsing(fn($v) => strtoupper($v))` |
| `->badge(bool = true)` | Render as a badge | `->badge()` |
| `->color(string\|array)` | Set badge color | `->color('default')` |
| `->date(string = 'Y-m-d')` | Format as a date | `->date('d/m/Y')` |
| `->time(string = 'H:i:s')` | Format as a time | `->time('H:i')` |
| `->dateTime(string)` | Format as a datetime | `->dateTime('d M Y H:i')` |

#### Full Example

```php
TextColumn::make('name')
    ->label('Full Name')
    ->sortable()
    ->searchable(),

TextColumn::make('status')
    ->filterable(['active', 'inactive', 'suspended'])
    ->badge()
    ->color([
        'active'    => 'default',
        'inactive'  => 'secondary',
        'suspended' => 'destructive',
    ]),

TextColumn::make('amount')
    ->sortable()
    ->formatUsing(fn($val) => '$' . number_format($val, 2)),

TextColumn::make('created_at')
    ->label('Registered At')
    ->sortable()
    ->dateTime('d M Y H:i'),

TextColumn::make('department_name')
    ->label('Department')
    ->relation('department', 'name')
    ->sortable()
    ->searchable(),
```

---

#### `->as(string $outputKey)` — Output Key Alias

By default, a column's **source key** (the DB field it reads from) and its **output key** (the key in the response row) are the same. Use `->as()` to decouple them.

This is essential when you want **multiple columns reading from the same DB field** — without `->as()`, the second column would overwrite the first in the row data.

```php
// Two columns, same source field — different output keys
TextColumn::make('created_at')              // reads 'created_at', outputs as 'created_at'
    ->label('Created At (raw)')
    ->sortable(),

TextColumn::make('created_at')              // reads 'created_at'
    ->as('created_at_formatted')            // outputs as 'created_at_formatted'
    ->label('Created At')
    ->date('d M Y'),
```

Resulting row data:
```json
{
  "created_at": "2024-01-15 09:30:00",
  "created_at_formatted": "15 Jan 2024"
}
```

**How it works internally:**
- `getSourceKey()` → the original key passed to `make()` — used to **read** from the row
- `getKey()` → the `outputKey` (defaults to source key, overridden by `->as()`) — used to **write** back to the row and as the key in the `columns` definition sent to the frontend

> **Without `->as()`**, declaring two columns with the same key (e.g. `created_at`) would cause the second formatter's output to overwrite the first in the row array. `->as()` avoids this by writing to a distinct key.

**Another use case — computed display field from a raw value:**
```php
TextColumn::make('price')                   // keep raw price in data
    ->label('Price (raw)')
    ->hidden(),                             // hidden from frontend columns list

TextColumn::make('price')                   // same DB field
    ->as('price_display')                   // separate output key
    ->label('Price')
    ->formatUsing(fn($val) => '$' . number_format($val, 2)),
```

---

#### Filter with Value Map

```php
// Simple options — value doubles as label
TextColumn::make('role')->filterable(['admin', 'user', 'moderator'])

// Options with distinct labels
TextColumn::make('role')->filterable([
    'admin'  => 'Administrator',
    'user'   => 'Regular User',
    'mod'    => 'Moderator',
])
```

---

#### Relation Columns

For columns that come from an Eloquent relation (e.g. department name from a `department` relation):

```php
TextColumn::make('department_name')
    ->relation('department', 'name')  // ('relationName', 'foreignKey on relation')
    ->sortable()   // requires RelationSortPipe for JOIN-based sorting to work
    ->searchable()
```

> **Note:** For **sorting** relation columns, add `RelationSortPipe` to the pipeline. See [Built-in Pipes](#built-in-pipes).

---

### `ActionColumn`

A dedicated column for per-row action buttons.

```php
use Kinetics\Columns\ActionColumn;
use Kinetics\Actions\Action;
use Kinetics\Actions\ActionGroup;

ActionColumn::make()
    ->actions([
        Action::view('users.show'),
        Action::edit('users.edit'),
        ActionGroup::make()
            ->actions([
                Action::make('approve')->label('Approve'),
                Action::delete('users.destroy'),
            ]),
    ])
```

**Characteristics:**
- Not sortable, searchable, or filterable
- Resolved action data is injected directly into each row under the `__actions` key
- Supports a mix of `Action` and `ActionGroup`

---

## 5. Actions

### `Action`

Represents a single button or link in the action column.

```php
use Kinetics\Actions\Action;
```

#### Preset Constructors

```php
Action::edit('users.edit')      // label='Edit', icon='pencil', variant='outline'
Action::view('users.show')      // label='View', icon='eye', variant='outline'
Action::delete('users.destroy') // label='Delete', icon='trash', variant='destructive', auto-confirm
```

#### Building a Custom Action

```php
Action::make('approve')
    ->label('Approve')
    ->icon('check')
    ->variant('default')
    ->href('users.approve')
    ->method('post')
```

#### Fluent API — `Action`

| Method | Description | Example |
|--------|-------------|---------|
| `->label(string)` | Button text | `->label('Approve')` |
| `->icon(string)` | Icon name | `->icon('check-circle')` |
| `->variant(string)` | Visual style | `->variant('destructive')` |
| `->href(string\|Closure)` | Route name or URL | `->href('users.edit')` |
| `->method(string)` | HTTP method | `->method('delete')` |
| `->modal(bool)` | Open in a modal | `->modal()` |
| `->visibleWhen(Closure)` | Per-row visibility condition | see example |
| `->disabledWhen(Closure)` | Per-row disabled condition | see example |
| `->confirm(string, string)` | Confirmation dialog | `->confirm('Sure?', 'This cannot be undone.')` |
| `->meta(array)` | Custom metadata sent to frontend | `->meta(['color' => 'red'])` |

**Allowed `variant` values:** `default`, `destructive`, `ghost`, `outline`

**Allowed `method` values:** `get`, `post`, `put`, `patch`, `delete`

#### Per-row Conditions

```php
Action::make('publish')
    ->label('Publish')
    ->href('posts.publish')
    ->method('post')
    ->visibleWhen(fn($row) => $row['status'] === 'draft'),

Action::make('edit')
    ->label('Edit')
    ->href('posts.edit')
    ->disabledWhen(fn($row) => !auth()->user()->can('edit', $row)),
```

#### Automatic `href` Resolution

When `href` is a route name (string), Kinetics will automatically:
1. Detect the route parameters required
2. Pull matching values from the row data
3. Fall back to `id` if the parameter name is not found in the row

```php
// Route: users/{user}/edit  →  route('users.edit', ['user' => $row['id']])
Action::edit('users.edit')

// Full control via closure
Action::make('impersonate')
    ->href(fn($row) => route('admin.impersonate', $row['id']))
```

---

### `ActionGroup`

Groups multiple actions into a dropdown menu. Useful when a row has many actions and you want to avoid cluttering the column.

```php
use Kinetics\Actions\ActionGroup;

ActionGroup::make('More')
    ->icon('ellipsis-vertical')
    ->actions([
        Action::make('approve')->label('Approve'),
        Action::make('reject')->label('Reject'),
        Action::make('archive')->label('Archive'),
    ])
```

| Method | Description |
|--------|-------------|
| `::make(string $label = 'Actions')` | Create a new group |
| `->icon(string)` | Override the trigger icon |
| `->actions(array)` | List of `Action[]` items |

---

## 6. Pipeline & Pipes

Kinetics uses `Illuminate\Pipeline\Pipeline` to run the query through a series of pipes. Each pipe receives a `Builder`, modifies it, and passes it to the next pipe.

### Default Pipeline Order

```
Builder (query)
  ↓
SortPipe        — ORDER BY based on ?sort=&direction=
  ↓
SearchPipe      — WHERE LIKE on searchable columns
  ↓
FilterPipe      — WHERE exact / whereIn on filterable columns
  ↓
[Custom Pipes]  — Additional pipes registered via ->pipes()
  ↓
PaginatePipe    — Execute query + paginate (terminal pipe)
  ↓
LengthAwarePaginator
```

> **Important:** This order is intentional. Custom pipes always run after sort/search/filter but before paginate, so they can add further constraints without interfering with the core mechanics.

---

### Built-in Pipes

#### `SortPipe` (default)

Applies `ORDER BY` based on the `?sort=column&direction=asc|desc` query parameters.

- Only processes columns marked as `->sortable()`
- Falls back to `defaultSort` if no sort request is present

#### `SearchPipe` (default)

Global search via `?search=keyword`.

- Only searches columns marked as `->searchable()`
- Uses `LIKE %keyword%` (case-insensitive in MySQL)
- **Minimum 2 characters** — search requests shorter than 2 characters are ignored
- Supports searching through relations (`orWhereHas`)

#### `FilterPipe` (default)

Per-column filtering via `?filters[column]=value`.

- Only processes columns marked as `->filterable()`
- **Array value** → `whereIn`
- **String/int value** → exact `where` match
- Null or empty values are ignored

```
?filters[status]=active
?filters[role][]=admin&filters[role][]=moderator
```

#### `PaginatePipe` (default, terminal)

Executes the query and returns a `LengthAwarePaginator`.

- This pipe **does not call `$next()`** — it is the terminal pipe
- Per-page is taken from `?per_page=N`, capped by `maxPerPage`

#### `CursorPaginatePipe` (optional)

An alternative to `PaginatePipe` for very large datasets. Uses cursor-based pagination, which avoids `COUNT(*)`.

```php
use Kinetics\Pipes\PaginatePipe;
use Kinetics\Pipes\CursorPaginatePipe;

Table::model(Log::class)
    ->withoutPipes([PaginatePipe::class])
    ->pipes([CursorPaginatePipe::class])
    ->columns([...])
    ->make();
```

**Trade-off:** Does not support jumping to a specific page number.

#### `DateRangeFilterPipe` (optional)

Filters by a date range using dedicated query parameters.

```
?date_from=2024-01-01&date_to=2024-12-31
```

```php
use Kinetics\Pipes\DateRangeFilterPipe;

Table::model(Order::class)
    ->pipes([
        new DateRangeFilterPipe(
            column: 'created_at',   // column to filter (default: 'created_at')
            fromParam: 'date_from', // query param name (default: 'date_from')
            toParam: 'date_to',     // query param name (default: 'date_to')
        ),
    ])
    ->columns([...])
    ->make();
```

- Supports `date_from` only, `date_to` only, or both
- Automatically wraps values in `startOfDay()` and `endOfDay()`

#### `RelationSortPipe` (optional)

Handles sorting for columns derived from Eloquent relations. Uses a JOIN to avoid N+1.

```php
use Kinetics\Pipes\RelationSortPipe;

Table::model(User::class)
    ->pipes([RelationSortPipe::class])
    ->columns([
        TextColumn::make('department_name')
            ->relation('department', 'name')
            ->sortable(),
    ])
    ->make();
```

**How it works:**
1. Detects if the sort request targets a relation column
2. Performs a `LEFT JOIN` to the related table (if not already joined)
3. `ORDER BY related_table.column`

---

### Custom Pipes

Create a new custom pipe using the Artisan command:

```bash
php artisan kinetics:pipe TenantScope
# → app/Pipes/TenantScopePipe.php

php artisan kinetics:pipe TenantScope --path=Http/Table/Pipes
# → app/Http/Table/Pipes/TenantScopePipe.php
```

The `Pipe` suffix is automatically appended to the class name if not already present.

#### Pipe Structure

```php
<?php

namespace App\Pipes;

use Closure;
use Kinetics\Contracts\PipeInterface;
use Kinetics\Support\TableContext;
use Illuminate\Database\Eloquent\Builder;

class TenantScopePipe implements PipeInterface
{
    public function handle(Builder $query, Closure $next): mixed
    {
        // Retrieve context (contains columns, config, request)
        $ctx = $query->getModel()->datatableContext ?? null;

        if (! $ctx instanceof TableContext) {
            return $next($query);
        }

        // Add a custom constraint
        $query->where('tenant_id', auth()->user()->tenant_id);

        // Continue the pipeline
        return $next($query);
    }
}
```

#### Registering a Pipe

```php
// Class string (resolved via Laravel container → supports constructor DI)
Table::model(User::class)->pipes([TenantScopePipe::class])

// Instance (manual construction)
Table::model(User::class)->pipes([new TenantScopePipe()])

// Anonymous class for one-offs
Table::model(User::class)->pipes([
    new class implements PipeInterface {
        public function handle(Builder $query, Closure $next): mixed
        {
            $query->where('active', true);
            return $next($query);
        }
    }
])
```

#### `TableContext` — Data Available Inside a Pipe

| Method | Return | Description |
|--------|--------|-------------|
| `getColumns()` | `Column[]` | All column definitions |
| `getSortableKeys()` | `string[]` | Keys of sortable columns |
| `getSearchableKeys()` | `string[]` | Keys of searchable columns |
| `getFilterableKeys()` | `string[]` | Keys of filterable columns |
| `getSortColumn()` | `?string` | Sort column from the request |
| `getSortDirection()` | `string` | Sort direction (`asc`/`desc`) |
| `getSearch()` | `?string` | Search keyword (null if < 2 chars) |
| `getFilters()` | `array` | Filters array from the request |
| `getPerPage()` | `int` | Per-page value (already capped to max) |
| `setMeta(string, mixed)` | `void` | Store data in the meta bag |
| `getMeta(string, mixed)` | `mixed` | Retrieve data from the meta bag |
| `allMeta()` | `array` | All meta data |

```php
// Example: a pipe that injects extra data into the response
public function handle(Builder $query, Closure $next): mixed
{
    $ctx = $query->getModel()->datatableContext ?? null;

    $stats = DB::table('users')->selectRaw('COUNT(*) as total')->first();

    $ctx?->setMeta('stats', $stats);

    return $next($query);
}
```

#### Customising the Stub

You can override the default stub by creating a file at:

```
stubs/kinetics/pipe.stub
```

Available variables in the stub: `{{ namespace }}`, `{{ class }}`

---

## 7. Output Methods

### `->make()` → `array`

Run the pipeline and return a plain array. Use this for Inertia or JSON API responses.

```php
// Inertia
return Inertia::render('Users/Index', [
    'table' => Table::model(User::class)->columns([...])->make(),
]);

// JSON API
return response()->json(
    Table::model(User::class)->columns([...])->make()
);
```

### `->get()` → `TableResult`

Run the pipeline and return a `TableResult` instance. Use this when you need to access specific parts of the result — ideal for testing or advanced transformations.

```php
$result = Table::model(User::class)->columns([...])->get();

$result->getData();        // array  — transformed rows
$result->getTotal();       // int    — total row count
$result->getMeta();        // array  — pagination meta
$result->getState();       // array  — current request state
$result->getColumns();     // array  — column definitions
$result->getPaginator();   // LengthAwarePaginator — raw paginator
$result->getCurrentPage(); // int
$result->getLastPage();    // int
$result->getPerPage();     // int

// JsonSerializable — works directly with response()->json()
return response()->json($result);
```

### `->paginate()` → `LengthAwarePaginator`

Run the pipeline and return the raw `LengthAwarePaginator`, bypassing `TableResult` formatting entirely. Use when you need full control over the response shape.

```php
$paginator = Table::model(User::class)->columns([...])->paginate();
return response()->json([
    'items'      => $paginator->items(),
    'total'      => $paginator->total(),
    'custom_key' => 'custom_value',
]);
```

---

## 8. Response Shape

All output methods produce the same structure:

```json
{
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "status": "ACTIVE",
      "__actions": [
        {
          "type": "action",
          "key": "edit",
          "label": "Edit",
          "icon": "pencil",
          "variant": "outline",
          "href": "/users/1/edit",
          "method": "GET",
          "modal": false,
          "disabled": false,
          "confirm": null,
          "meta": {}
        }
      ]
    }
  ],
  "columns": [
    {
      "key": "name",
      "label": "Name",
      "sortable": true,
      "searchable": true,
      "filterable": false,
      "filterOptions": [],
      "visible": true,
      "type": "text",
      "meta": {}
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 73,
    "from": 1,
    "to": 15
  },
  "state": {
    "sort": "name",
    "direction": "asc",
    "search": "",
    "filters": {},
    "per_page": 15
  }
}
```

### Key Descriptions

| Key | Description |
|-----|-------------|
| `data` | Array of rows. Formatters and action columns are already resolved. |
| `columns` | Column definitions for the frontend (used to render headers, sorting, etc.). |
| `meta` | Pagination info: current page, total, etc. |
| `state` | Mirror of the current request state. Used by the frontend to sync filter/search UI. |

---

## 9. Artisan Commands

### `kinetics:pipe`

Scaffold a new custom pipe file.

```bash
# Creates app/Pipes/TenantScopePipe.php
php artisan kinetics:pipe TenantScope

# With a custom path
php artisan kinetics:pipe StatusFilter --path=Http/Datatable/Pipes
# → app/Http/Datatable/Pipes/StatusFilterPipe.php
```

**Naming:** The `Pipe` suffix is automatically appended if the given name does not already end with it.

**Namespace** is automatically resolved from the output path (PSR-4).

---

## 10. Advanced Usage

### Multi-tenant Scope

```php
Table::model(Order::class)
    ->tap(fn($q) => $q->where('tenant_id', auth()->user()->tenant_id))
    ->columns([...])
    ->make();
```

### Eager Loading + Custom Query

```php
$query = User::with(['department', 'roles'])
    ->withCount('orders')
    ->whereHas('roles', fn($q) => $q->where('name', 'staff'));

Table::query($query)
    ->columns([...])
    ->make();
```

### Combining Date Range + Column Filters

```php
Table::model(Order::class)
    ->pipes([
        new DateRangeFilterPipe('created_at', 'from', 'to'),
    ])
    ->columns([
        TextColumn::make('status')->filterable(['pending', 'done']),
        TextColumn::make('created_at')->date(),
    ])
    ->make();
```

### Relation Sorting

```php
Table::model(User::class)
    ->pipes([RelationSortPipe::class])
    ->columns([
        TextColumn::make('name')->sortable(),
        TextColumn::make('department_name')
            ->relation('department', 'name')
            ->sortable(),
    ])
    ->make();
```

### Custom Value Formatting

```php
TextColumn::make('price')
    ->formatUsing(fn($value, $row, $model) => [
        'raw'       => $value,
        'formatted' => '$' . number_format($value, 2),
        'currency'  => 'USD',
    ]),
```

> The formatter receives 3 arguments: `$value` (the column value), `$row` (the row as an array), and `$model` (the original Eloquent model instance).

### Testing

```php
use Kinetics\Table;
use Kinetics\Columns\TextColumn;
use Illuminate\Http\Request;

// Use ->withRequest() to inject a request in tests
$result = Table::model(User::class)
    ->columns([
        TextColumn::make('name')->sortable()->searchable(),
        TextColumn::make('role')->filterable(['admin', 'user']),
    ])
    ->withRequest(new Request([
        'sort'    => 'name',
        'search'  => 'john',
        'filters' => ['role' => 'admin'],
    ]))
    ->get();

$this->assertCount(1, $result->getData());
$this->assertEquals(1, $result->getTotal());
```
