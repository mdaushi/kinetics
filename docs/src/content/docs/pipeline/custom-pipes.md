---
title: Custom Pipes
description: Create and insert your own pipes into the table pipeline.
---

The Kinetics architecture allows you to create your own process stages (pipes) and insert them into the query builder flow.

## When Do You Need Custom Pipes?

You might need custom pipes when:
- You want to execute complex query logic that isn't handled by the built-in Search, Sort, or Filter.
- You need to inject specific scopes based on user roles (e.g., tenant isolation).
- You need to calculate temporary aggregates.

## Creating a Custom Pipe

You can generate a new custom pipe easily using the artisan command provided by Kinetics:

```bash
php artisan kinetics:pipe ActiveUsersOnlyPipe
```

This will create a new invokable class in your `app/Pipes` directory. Alternatively, you can write the class manually. Since the architecture utilizes Laravel's built-in `Illuminate\Pipeline\Pipeline`, the class just needs a `handle` method:

```php
namespace App\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class ActiveUsersOnlyPipe
{
    public function handle(Builder $query, Closure $next)
    {
        // Modify the query to only show active users
        $query->where('is_active', true);
        
        // Proceed to the next pipe in the chain
        return $next($query);
    }
}
```

## Inserting Pipes into `Table`

To insert your custom pipes into the query execution flow, pass them to the `pipes()` method on your `Table` instance. They will run alongside the default Kinetics pipes.

```php
use Kinetics\Table;
use App\Pipes\ActiveUsersOnlyPipe;

$table = Table::model(User::class)
    ->pipes([
        new ActiveUsersOnlyPipe(),
    ])
    ->columns([
        // ...
    ])
    ->make();
```
