---
title: Custom Operators
description: How to limit filter operators or create your own custom SQL filter operators.
---

By default, each filter type in Kinetics comes with a pre-defined set of logical operators (like `equals`, `contains`, `>`, etc.). However, Kinetics gives you full control to modify these or create entirely new ones.

## 1. Restricting or Renaming Operators

Sometimes you don't want the user to see all available operators. You can restrict the list of operators for a specific filter instance using the `operators()` method.

```php
use Kinetics\Filters\TextFilter;

TextFilter::make('email')
    // Only allow these two operators
    ->operators(['equals', 'contains'])
```

You can also use an associative array to change the frontend label of the operators!

```php
TextFilter::make('email')
    ->operators([
        'equals' => 'Exact Match',
        'contains' => 'Partial Match'
    ])
```

## 2. Creating Global Custom Operators

If the built-in operators (`equals`, `in`, `between`, etc.) are not enough for your specific SQL needs, you can register entirely new global operators. 

You do this by calling the static `resolveOperator()` method on the base `Filter` class, typically inside your `AppServiceProvider`'s `boot()` method.

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Kinetics\Filters\Filter;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register a custom operator called 'json_contains'
        Filter::resolveOperator('json_contains', function (Builder $query, string $column, mixed $value) {
            $query->whereJsonContains($column, $value);
        });
    }
}
```

Once registered globally, you can use your custom operator in any filter across your application:

```php
TextFilter::make('settings')
    ->operators([
        'json_contains' => 'Has Setting',
        'equals' => 'Exact Settings'
    ])
```
