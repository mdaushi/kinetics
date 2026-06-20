---
title: Table Overview
description: Comprehensive guide to the Table class, initialization, and configuration.
---

The `Table` class is the central orchestrator of Kinetics. It acts as a bridge between your Eloquent models, the data processing pipeline, and the frontend React component.

## Initialization

There are two ways to initialize a `Table` instance depending on your needs.

### Using a Model

If you are querying a model directly, use the `model()` static method.

```php
use Kinetics\Table;
use App\Models\User;

$table = Table::model(User::class)->make();
```

### Using a Query Builder

If you already have a complex base query (with eager loading, specific constraints, or scopes applied), you can pass the query builder instance directly to the `query()` static method.

```php
$query = User::with('company')->where('is_active', true);

$table = Table::query($query)->make();
```

## Registering Components

The `Table` class is where you register the visual components of your datatable: Columns, Filters, and Actions.

```php
$table = Table::model(User::class)
    ->columns([
        // Define your TextColumn, ActionColumn, etc.
    ])
    ->filters([
        // Define your TextFilter, SelectFilter, etc.
    ])
    ->actions([
        // Define ToolbarAction instances
    ])
    ->bulkActions([
        // Define BulkAction instances (active when rows are selected)
    ])
    ->make();
```

## Advanced Configurations

The `Table` class provides several powerful chaining methods that allow you to deeply configure the query execution and pagination behaviors.

### Query Injection (`tap`)

The `tap()` method allows you to apply base Eloquent scopes or query constraints before any pipelines (search, sort, filter) are executed. This is highly useful for multi-tenancy.

```php
Table::model(User::class)
    ->tap(function ($query) {
        $query->where('tenant_id', auth()->user()->tenant_id);
    })
    ->make();
```

### Overriding the Request (`withRequest`)

By default, the Table uses the global HTTP request. If you are testing or need to inject a mock request, use `withRequest()`.

```php
Table::model(User::class)->withRequest($customRequest)->make();
```

### Pagination Settings (`perPage`)

You can override the default pagination limits specifically for this table using the `perPage()` method.

```php
// Sets the default page size to 50, and allows the user to request up to 500 rows.
Table::model(Post::class)
    ->perPage(default: 50, max: 500)
    ->make();
```

### Default Sorting (`defaultSort`)

Set the column that should be sorted automatically when the page first loads.

```php
Table::model(Post::class)
    ->defaultSort('created_at', 'desc')
    ->make();
```

### Debouncing Requests (`debounce`)

By default, the Kinetics frontend waits 500 milliseconds after the user stops typing in a search or filter input before sending the HTTP request. You can override this delay for heavier tables.

```php
Table::model(Log::class)
    ->debounce(1000) // Wait 1 second
    ->make();
```

### Customizing Pipelines (`pipes` and `withoutPipes`)

Kinetics runs your query through a series of "pipes" (Search, Filter, Sort, Paginate). You can inject your own custom pipes, or disable the default ones.

```php
use Kinetics\Pipes\SortPipe;
use App\Pipes\MyCustomExportPipe;

Table::model(User::class)
    ->withoutPipes([SortPipe::class]) // Disable built-in sorting
    ->pipes([MyCustomExportPipe::class]) // Inject a custom pipe before pagination
    ->make();
```

## Output Formatting (`make` vs `get` vs `paginate`)

Throughout the documentation, we use `->make()` which immediately processes the pipeline and returns a pure Array. This is perfect for Inertia.js.

```php
return Inertia::render('Users/Index', [
    'table' => Table::model(User::class)->make()
]);
```

However, if you are building an API or writing Unit Tests, you might want to inspect the generated result. Use the `get()` method to retrieve a `TableResult` object.

```php
$result = Table::model(User::class)->get();

// You can now inspect internal states
$rawData = $result->getData();
$paginationMeta = $result->getMeta();
$paginator = $result->getPaginator(); // Raw Laravel LengthAwarePaginator

// You can still return it as JSON natively!
return response()->json($result);
```

If you only want the raw Laravel Paginator and want to bypass Kinetics' formatting entirely, use `paginate()`:

```php
$paginator = Table::model(User::class)->paginate();
```

---

## API Reference

| Method | Description |
|--------|-------------|
| `model(string $modelClass)` | **[Static]** Initializes the table from an Eloquent Model class. |
| `query(Builder $query)` | **[Static]** Initializes the table from an existing Query Builder instance. |
| `columns(array $columns)` | Registers the column definitions. |
| `filters(array $filters)` | Registers the filter definitions. |
| `actions(array $actions)` | Registers global toolbar actions. |
| `bulkActions(array $actions)` | Registers bulk actions (active when rows are selected). |
| `perPage(int $default, int $max)`| Sets the default and maximum allowed items per page. |
| `debounce(int $ms)` | Sets the frontend debounce delay for search/filter inputs. |
| `defaultSort(string $col, string $dir)`| Sets the default sorting column and direction. |
| `pipes(array $pipes)` | Injects custom pipes into the query processing pipeline. |
| `withoutPipes(array $pipes)` | Removes default pipes (e.g. to disable default sorting). |
| `tap(\Closure $callback)` | Applies additional constraints to the base query. |
| `withRequest(Request $request)`| Overrides the HTTP request object used by the table. |
| `make()` | Executes the pipeline and returns a formatted Array (ideal for Inertia). |
| `get()` | Executes the pipeline and returns a `TableResult` object. |
| `paginate()` | Executes the pipeline and returns the raw `LengthAwarePaginator`. |