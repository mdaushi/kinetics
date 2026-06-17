---
title: Columns Overview
description: Introduction to the column system in Kinetics.
---

Columns are the core foundation in defining what is displayed in your table. In Kinetics, all column configurations are done exclusively on the backend (Laravel).

## How Columns Work

Kinetics uses specific column classes to determine how data is rendered. When you register a column using the `Table` class, Kinetics will automatically:

1. Extract data from the database based on the column name.
2. Apply specific formatting if needed (e.g., displaying as a badge).
3. Signal the pipeline whether the column is sortable or searchable.

## Defining Columns

Columns are defined by calling the `columns()` method on the `Table` class instance.

```php
use Kinetics\Table;
use Kinetics\Columns\TextColumn;

$table = Table::model(User::class)
    ->columns([
        TextColumn::make('name'),
        TextColumn::make('email'),
    ])
    ->make();
```

## Global Column Features

Almost all column types in Kinetics support the following chaining methods:

### Setting the Header Label
By default, Kinetics will generate a header label based on the column name (e.g., `first_name` becomes "First Name"). You can override this using `label()`.

```php
TextColumn::make('name')->label('Full Name')
```

### Enabling Sorting
Use the `sortable()` method to allow users to sort the table by this column.

```php
TextColumn::make('created_at')->sortable()
```

### Enabling Global Search
Use the `searchable()` method to include the value of this column when the user types something in the global search box.

```php
TextColumn::make('email')->searchable()
```

### Relationships

Kinetics provides two ways to fetch and display data from Eloquent relationships.

#### 1. Dot Notation (Automatic)
The easiest way is to use dot notation directly in the `make()` method. Kinetics will automatically format the output JSON key (e.g. `company_name`) and resolve the relationship.

```php
// Displaying the name from the 'company' relationship
TextColumn::make('company.name')->label('Company')
```

#### 2. The `relation()` Method (Explicit)
If you need the output JSON key to be different from the relationship path, you can use the `relation()` method. This is useful when you want to name the column specifically for the frontend.

```php
// The frontend will receive this as 'author_name', 
// but it fetches from the 'user' relation and 'name' column.
TextColumn::make('author_name')
    ->relation('user', 'name')
    ->label('Author')
```

### Server-Side Formatting (`formatUsing`)
You can transform data before it is sent to the frontend using a closure. The closure receives three arguments: the cell value, the entire row array, and the raw Eloquent model instance.

```php
TextColumn::make('price')->formatUsing(function ($value, $row, $model) {
    return 'Rp ' . number_format($value, 0, ',', '.');
})
```

### Aliasing Columns (`as`)
Sometimes you need to pull the same database column twice but display it differently (e.g., one raw date, one formatted date). Use `as()` to change the JSON output key.

```php
TextColumn::make('created_at')->date('Y-m-d'),
TextColumn::make('created_at')
    ->as('created_at_human')
    ->formatUsing(fn($val) => \Carbon\Carbon::parse($val)->diffForHumans()),
```

### Hiding Columns (`hidden`)
If you want to query a column but not display it by default, use `hidden()`. The data will be sent to the frontend but TanStack Table will hide the column.

```php
TextColumn::make('secret_id')->hidden()
```

### Injecting Metadata (`meta`)
You can pass custom arbitrary data directly to the frontend table component. This is extremely useful for passing CSS classes or conditional formatting flags to your React component.

```php
TextColumn::make('status')->meta(['className' => 'text-red-500 font-bold'])
```

Learn more about specific column types on the next pages.
