# Backend (Laravel)

The `mdaushi/kinetics-laravel` package handles all server-side logic: reading request parameters, running the query pipeline (sort, search, filter, paginate), and returning the result ready to be consumed by Inertia.

## Installation

```bash
composer require mdaushi/kinetics-laravel
```

The package is automatically registered via Laravel Package Auto-Discovery.

### Publish Config (Optional)

```bash
php artisan vendor:publish --tag=kinetics-config
```

This will create `config/kinetics.php`:

```php
return [
    'default_per_page'  => 15,
    'max_per_page'      => 100,
    'default_sort'      => 'id',
    'default_direction' => 'desc',
    'options_per_page'  => [10, 15, 25, 50, 100],
];
```

---

## Basic Usage

In your controller, use `Table::model()` or `Table::query()` to build your datatable, then return the result of `.make()` to Inertia.

```php
use Kinetics\Table;
use Kinetics\Columns\Column;
use Kinetics\Columns\ActionColumn;
use Kinetics\Actions\Action;
use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $table = Table::model(User::class)
            ->columns([
                Column::make('name')
                    ->sortable()
                    ->searchable(),

                Column::make('email')
                    ->sortable()
                    ->searchable(),

                Column::make('status')
                    ->filterable(['active' => 'Active', 'inactive' => 'Inactive']),

                Column::make('created_at')
                    ->label('Joined')
                    ->sortable(),

                ActionColumn::make()
                    ->actions([
                        Action::view('/users/:id'),
                        Action::edit('/users/:id/edit'),
                        Action::delete('/users/:id'),
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->perPage(15, 100)
            ->make();

        return Inertia::render('Users/Index', [
            'table' => $table,
        ]);
    }
}
```

---

## Entry Points

### `Table::model(string $modelClass)`

Create a Table instance from an Eloquent Model class. A base query is created automatically.

```php
Table::model(User::class)
```

### `Table::query(Builder $query)`

Use this when you already have a customised query — for example with joins, scopes, or sub-queries.

```php
$query = User::query()
    ->with('department')
    ->whereNotNull('email_verified_at');

Table::query($query)
```

---

## Table Configuration

### `->columns(array $columns)`

Define the columns to display. See the [Column](#column) section below.

### `->defaultSort(string $column, string $direction = 'desc')`

Set the default sort column and direction when no sort request comes from the user.

```php
->defaultSort('created_at', 'desc')
```

### `->perPage(int $default, int $max = 100)`

Set the default and maximum number of items per page.

```php
->perPage(15, 100)
```

### `->tap(Closure $callback)`

Apply additional constraints to the base query. Useful for multi-tenancy or extra scopes without building a separate query.

```php
->tap(fn($q) => $q->where('tenant_id', auth()->user()->tenant_id))
```

### `->pipes(array $pipes)`

Add custom pipeline stages after filters and before pagination. See [Custom Pipes](#custom-pipes).

### `->withoutPipes(array $pipes)`

Remove default pipes that are not needed.

```php
use Kinetics\Pipes\SortPipe;

->withoutPipes([SortPipe::class])
```

### `->make()`

Run the pipeline and return an `array` ready to use in an Inertia response.

### `->get()`

Same as `make()` but returns a `TableResult` instance (useful for testing or JSON APIs).

---

## Column

`Column::make(string $key)` is the entry point for defining a single column. `$key` must match the database column name (or a query alias).

### Available Methods

| Method | Description |
|---|---|
| `->label(string $label)` | Override the column label (default: key converted to Title Case) |
| `->sortable()` | Enable sorting for this column |
| `->searchable()` | Include this column in global search |
| `->filterable(array $options = [])` | Enable filtering. Can be a list of values or `key => label` pairs |
| `->hidden()` | Hide the column from the table (still present in the data) |
| `->type(string $type)` | Set the column type (`text`, `date`, etc.) |
| `->relation(string $relation, string $foreignKey)` | For columns derived from an Eloquent relation (cross-table sorting) |
| `->formatUsing(Closure $callback)` | Format the value before sending it to the frontend |

### Examples

```php
// Basic column
Column::make('name')->sortable()->searchable(),

// Custom label
Column::make('created_at')->label('Joined')->sortable(),

// Filterable with static options
Column::make('status')->filterable([
    'active'   => 'Active',
    'inactive' => 'Inactive',
    'banned'   => 'Banned',
]),

// Relation column (sort by department.name via JOIN)
Column::make('department_name')
    ->label('Department')
    ->relation('department', 'name')
    ->sortable(),

// Custom value formatter
Column::make('salary')
    ->formatUsing(fn($val) => '$' . number_format($val, 2)),
```

---

## ActionColumn & Action

Use `ActionColumn` for columns that contain per-row action buttons.

```php
use Kinetics\Columns\ActionColumn;
use Kinetics\Actions\Action;
use Kinetics\Actions\ActionGroup;

ActionColumn::make()
    ->actions([
        Action::view('/users/:id'),
        Action::edit('/users/:id/edit'),
        Action::delete('/users/:id'),
    ]),
```

### Preset Actions

| Method | Description |
|---|---|
| `Action::view(string $route)` | View button (icon: eye, variant: outline) |
| `Action::edit(string $route)` | Edit button (icon: pencil, variant: outline) |
| `Action::delete(string $route)` | Delete button (icon: trash, variant: destructive, with confirmation) |

### Custom Action

```php
Action::make('approve')
    ->label('Approve')
    ->icon('check')
    ->variant('default')
    ->href('/orders/:id/approve')
    ->method('post')
    ->confirm('Are you sure you want to approve this order?')
    ->visibleWhen(fn($row) => $row['status'] === 'pending'),
```

### `Action` Fluent API

| Method | Description |
|---|---|
| `->label(string)` | Button text |
| `->icon(string)` | Lucide icon name |
| `->variant(string)` | `default`, `destructive`, `ghost`, `outline` |
| `->href(string)` | URL with `:column_key` placeholder, e.g. `/users/:id/edit` |
| `->method(string)` | HTTP method: `get`, `post`, `put`, `patch`, `delete` |
| `->modal()` | Open in a modal instead of navigating |
| `->confirm(string $message)` | Show a confirmation dialog before the action runs |
| `->visibleWhen(Closure)` | Show the action only when the per-row condition is met |
| `->disabledWhen(Closure)` | Disable the action based on a per-row condition |

### ActionGroup (Dropdown)

Group multiple actions into a single dropdown menu:

```php
ActionGroup::make('More')
    ->actions([
        Action::make('approve')
            ->label('Approve')
            ->visibleWhen(fn($row) => $row['status'] === 'pending'),
        Action::make('reject')
            ->label('Reject')
            ->visibleWhen(fn($row) => $row['status'] === 'pending'),
        Action::delete('/orders/:id'),
    ]),
```

---

## Custom Pipes

All query logic runs through a Laravel pipeline. The default order is:

```
SortPipe → SearchPipe → FilterPipe → [Custom Pipes] → PaginatePipe
```

To add custom logic (e.g. filtering by a date range), create a class that implements `PipeInterface`:

```php
use Kinetics\Contracts\PipeInterface;
use Illuminate\Database\Eloquent\Builder;

class ActiveUsersOnlyPipe implements PipeInterface
{
    public function handle(Builder $query, \Closure $next): mixed
    {
        $query->where('is_active', true);
        return $next($query);
    }
}
```

Then register it with the table:

```php
Table::model(User::class)
    ->pipes([ActiveUsersOnlyPipe::class])
    ->columns([...])
    ->make();
```

### `DateRangeFilterPipe` (Built-in)

A built-in pipe for date range filtering via `?date_from=...&date_to=...` query parameters:

```php
use Kinetics\Pipes\DateRangeFilterPipe;

Table::model(Order::class)
    ->pipes([
        new DateRangeFilterPipe(
            column:    'created_at',
            fromParam: 'date_from',
            toParam:   'date_to',
        ),
    ])
    ->columns([...])
    ->make();
```

### `RelationSortPipe` (Built-in)

Used automatically when a column is defined with `->relation()`. You do not need to register this manually.
