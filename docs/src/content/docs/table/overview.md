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

The `Table` class is where you register the three core visual components of your datatable: Columns, Filters, and Actions.

```php
$table = Table::model(User::class)
    ->columns([
        // Define your TextColumn, ActionColumn, etc.
    ])
    ->filters([
        // Define your TextFilter, SelectFilter, etc.
    ])
    ->actions([
        // Define global table Actions (e.g., Export)
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

### Disabling Default Pipes (`withoutPipes`)

If you want to completely disable built-in features (for instance, you handle sorting manually), you can remove default pipes.

```php
use Kinetics\Pipes\SortPipe;

Table::model(User::class)
    ->withoutPipes([SortPipe::class])
    ->make();
```

## Output Formatting (`get` vs `make`)

Throughout the documentation, we use `->make()` which immediately processes the pipeline and returns a pure Array. This is perfect for Inertia.js.

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
