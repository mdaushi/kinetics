# Custom Pipes — Full Guide

This guide explains how to create, register, and test custom pipes in Kinetics.

---

## Table of Contents

1. [Core Concept](#1-core-concept)
2. [Creating a New Pipe](#2-creating-a-new-pipe)
3. [Accessing Context Inside a Pipe](#3-accessing-context-inside-a-pipe)
4. [Registering a Pipe](#4-registering-a-pipe)
5. [Practical Recipes](#5-practical-recipes)
6. [Built-in Pipes Reference](#6-built-in-pipes-reference)
7. [Testing Custom Pipes](#7-testing-custom-pipes)

---

## 1. Core Concept

A pipe is a class that receives a `Builder`, modifies the query, and passes it to the next pipe. This is identical to Laravel's Pipeline / Middleware concept.

```
Query → Pipe A → Pipe B → Pipe C → LengthAwarePaginator
                ↑
          Each pipe can:
          - Add WHERE, JOIN, ORDER BY
          - Return early without calling $next
          - Store data in the meta bag
```

**Ground rules:**
- Always call `return $next($query)` at the end (unless the pipe is terminal, like `PaginatePipe`)
- Implement `PipeInterface` — do not extend another class
- Pipes are resolved via the Laravel container → constructor injection is supported

---

## 2. Creating a New Pipe

### Via Artisan (Recommended)

```bash
# Output default: app/Pipes/MyCustomPipe.php
php artisan kinetics:pipe MyCustom

# With a custom path
php artisan kinetics:pipe TenantScope --path=Http/Table/Pipes
```

The `Pipe` suffix is automatically appended if the class name does not already end with it (`MyCustom` → `MyCustomPipe`).

### Manually

```php
<?php

namespace App\Pipes;

use Closure;
use Kinetics\Contracts\PipeInterface;
use Kinetics\Support\TableContext;
use Illuminate\Database\Eloquent\Builder;

class MyCustomPipe implements PipeInterface
{
    public function handle(Builder $query, Closure $next): mixed
    {
        $ctx = $query->getModel()->datatableContext ?? null;

        if (! $ctx instanceof TableContext) {
            return $next($query);
        }

        // ← Your custom logic here

        return $next($query);
    }
}
```

---

## 3. Accessing Context Inside a Pipe

`TableContext` is available via `$query->getModel()->datatableContext`.

```php
$ctx = $query->getModel()->datatableContext ?? null;

if (! $ctx instanceof TableContext) {
    return $next($query);  // No context — skip
}
```

### Available Data

```php
// Column definitions
$ctx->getColumns();           // Column[] — all columns
$ctx->getSortableKeys();      // ['name', 'email']
$ctx->getSearchableKeys();    // ['name', 'email']
$ctx->getFilterableKeys();    // ['status', 'role']

// Current request
$ctx->getSortColumn();        // 'name' | null
$ctx->getSortDirection();     // 'asc' | 'desc'
$ctx->getSearch();            // 'keyword' | null (null if < 2 chars)
$ctx->getFilters();           // ['status' => 'active']
$ctx->getPerPage();           // 15 (already capped to max)

// Config
$ctx->config->defaultPerPage   // 15
$ctx->config->maxPerPage       // 100
$ctx->config->defaultSort      // 'id'
$ctx->config->defaultDirection // 'desc'

// Meta bag — store data to include in the response
$ctx->setMeta('my_key', $value);
$ctx->getMeta('my_key');
$ctx->allMeta();
```

---

## 4. Registering a Pipe

### As a Class String (Recommended)

Resolved via the Laravel container — supports constructor dependency injection.

```php
Table::model(User::class)
    ->pipes([TenantScopePipe::class, StatusFilterPipe::class])
    ->columns([...])
    ->make();
```

### As an Instance

Manual construction, useful when you need to pass parameters.

```php
Table::model(User::class)
    ->pipes([
        new DateRangeFilterPipe('created_at', 'from', 'to'),
    ])
    ->make();
```

### As an Anonymous Class

For one-off pipes that do not need a dedicated file.

```php
Table::model(User::class)
    ->pipes([
        new class implements PipeInterface {
            public function handle(Builder $query, Closure $next): mixed
            {
                $query->where('active', true);
                return $next($query);
            }
        }
    ])
    ->make();
```

---

## 5. Practical Recipes

### Tenant Scope — Filter by the Authenticated User

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
        $ctx = $query->getModel()->datatableContext ?? null;

        if (! $ctx instanceof TableContext) {
            return $next($query);
        }

        $query->where('tenant_id', auth()->user()->tenant_id);

        return $next($query);
    }
}
```

```php
// Usage
Table::model(Invoice::class)
    ->pipes([TenantScopePipe::class])
    ->columns([...])
    ->make();
```

---

### Status Filter — Enforce a Default Status

```php
<?php

namespace App\Pipes;

use Closure;
use Kinetics\Contracts\PipeInterface;
use Illuminate\Database\Eloquent\Builder;

class ActiveOnlyPipe implements PipeInterface
{
    public function __construct(
        private readonly string $column = 'status',
        private readonly string $value = 'active',
    ) {}

    public function handle(Builder $query, Closure $next): mixed
    {
        $query->where($this->column, $this->value);
        return $next($query);
    }
}
```

```php
Table::model(User::class)
    ->pipes([new ActiveOnlyPipe('status', 'active')])
    ->make();
```

---

### Pipe with Repository / Service (Dependency Injection)

```php
<?php

namespace App\Pipes;

use Closure;
use App\Services\PermissionService;
use Kinetics\Contracts\PipeInterface;
use Illuminate\Database\Eloquent\Builder;

class PermissionScopePipe implements PipeInterface
{
    public function __construct(
        private readonly PermissionService $permissions
    ) {}

    public function handle(Builder $query, Closure $next): mixed
    {
        $allowedIds = $this->permissions->getAllowedUserIds(auth()->id());

        $query->whereIn('user_id', $allowedIds);

        return $next($query);
    }
}
```

```php
// Automatically resolved via the container — PermissionService is injected
Table::model(Ticket::class)
    ->pipes([PermissionScopePipe::class])
    ->make();
```

---

### Pipe That Injects Data into the Response Meta

```php
<?php

namespace App\Pipes;

use Closure;
use Kinetics\Contracts\PipeInterface;
use Kinetics\Support\TableContext;
use Illuminate\Database\Eloquent\Builder;

class SummaryPipe implements PipeInterface
{
    public function handle(Builder $query, Closure $next): mixed
    {
        $ctx = $query->getModel()->datatableContext ?? null;

        if ($ctx instanceof TableContext) {
            // Calculate summary from the same query (BEFORE paginate runs)
            $summary = (clone $query)->selectRaw('
                COUNT(*) as total,
                SUM(amount) as total_amount,
                AVG(amount) as avg_amount
            ')->first();

            $ctx->setMeta('summary', $summary);
        }

        return $next($query);
    }
}
```

The summary data will be available in the response under `meta`:

```json
{
  "meta": {
    "current_page": 1,
    "total": 100,
    "summary": {
      "total": 100,
      "total_amount": "1500000",
      "avg_amount": "15000"
    }
  }
}
```

---

### Replacing the Paginator (Cursor Pagination)

For very large datasets, cursor pagination is more efficient because it does not use `COUNT(*)`.

```php
use Kinetics\Pipes\PaginatePipe;
use Kinetics\Pipes\CursorPaginatePipe;

Table::model(ActivityLog::class)
    ->withoutPipes([PaginatePipe::class])
    ->pipes([CursorPaginatePipe::class])
    ->columns([
        TextColumn::make('action'),
        TextColumn::make('created_at')->dateTime(),
    ])
    ->make();
```

> **Note:** Cursor pagination does not support jumping to a specific page number (no `?page=N`).

---

### Customising the Stub

Create a `stubs/kinetics/pipe.stub` file in the root of your Laravel project to override the default template:

```php
<?php

namespace {{ namespace }};

use Closure;
use Kinetics\Contracts\PipeInterface;
use Kinetics\Support\TableContext;
use Illuminate\Database\Eloquent\Builder;

/**
 * Custom pipe.
 */
class {{ class }} implements PipeInterface
{
    public function handle(Builder $query, Closure $next): mixed
    {
        $ctx = $query->getModel()->datatableContext ?? null;

        if (! $ctx instanceof TableContext) {
            return $next($query);
        }

        // TODO: Implement your pipe logic here.

        return $next($query);
    }
}
```

---

## 6. Built-in Pipes Reference

| Pipe | Default? | Description | Query Param |
|------|----------|-------------|-------------|
| `SortPipe` | ✅ | ORDER BY sortable columns | `?sort=name&direction=asc` |
| `SearchPipe` | ✅ | LIKE search on searchable columns | `?search=keyword` |
| `FilterPipe` | ✅ | WHERE exact / whereIn on filterable columns | `?filters[col]=val` |
| `PaginatePipe` | ✅ (terminal) | Execute query, return paginator | `?page=1&per_page=15` |
| `CursorPaginatePipe` | ❌ | Cursor pagination (no COUNT) | `?cursor=xxx&per_page=15` |
| `DateRangeFilterPipe` | ❌ | Filter by date range | `?date_from=&date_to=` |
| `RelationSortPipe` | ❌ | JOIN + ORDER BY for relation columns | `?sort=relation_col` |

### Removing a Default Pipe

```php
use Kinetics\Pipes\SearchPipe;
use Kinetics\Pipes\FilterPipe;

// Remove both search and filter at once
Table::model(User::class)
    ->withoutPipes([SearchPipe::class, FilterPipe::class])
    ->columns([...])
    ->make();
```

---

## 7. Testing Custom Pipes

### Integration Test

```php
use Illuminate\Http\Request;
use Kinetics\Table;
use Kinetics\Columns\TextColumn;
use App\Pipes\TenantScopePipe;

public function test_tenant_scope_pipe_filters_by_tenant(): void
{
    $tenant1 = Tenant::create(['name' => 'Tenant A']);
    $tenant2 = Tenant::create(['name' => 'Tenant B']);

    User::create(['name' => 'User A', 'tenant_id' => $tenant1->id]);
    User::create(['name' => 'User B', 'tenant_id' => $tenant2->id]);

    $this->actingAs(User::factory()->create(['tenant_id' => $tenant1->id]));

    $result = Table::model(User::class)
        ->pipes([TenantScopePipe::class])
        ->columns([TextColumn::make('name')])
        ->withRequest(new Request())
        ->make();

    $this->assertCount(1, $result['data']);
    $this->assertEquals('User A', $result['data'][0]['name']);
}
```

### Isolated Pipe Test

```php
use Illuminate\Database\Eloquent\Builder;

public function test_active_only_pipe_adds_where_clause(): void
{
    $pipe = new ActiveOnlyPipe('status', 'active');

    $query = User::query();
    $next = fn($q) => $q; // no-op

    $result = $pipe->handle($query, $next);

    $sql = $result->toSql();
    $this->assertStringContainsString('"status" = ?', $sql);
}
```
