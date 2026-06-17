---
title: Sorting
description: Sorting data based on table columns.
---

Sorting is managed automatically by `SortPipe`. This allows users to click the header on the table interface to sort the data (Ascending or Descending).

## Enabling Sorting

Similar to the search feature, you must explicitly allow columns to be sorted using the `sortable()` method.

```php
use Kinetics\Table;
use Kinetics\Columns\TextColumn;

$table = Table::model(Product::class)
    ->columns([
        TextColumn::make('name')->sortable(),
        TextColumn::make('price')->sortable(),
        TextColumn::make('created_at')->label('Created Date')->sortable(),
    ])
    ->make();
```

When you enable `sortable()`, the `<Table />` frontend component will render that header as an interactive clickable element.

## How SortPipe Works

1. `SortPipe` will check the request parameters to see if there is a sorting request (e.g., `?sort=price&direction=desc`).
2. It then validates whether the `price` column is actually declared with `sortable()`. If not, the sort request is ignored (preventing client-side query manipulation).
3. If valid, it adds an `ORDER BY price DESC` clause into your Eloquent query builder.

This ensures that all sorted and paginated data is always processed on the database side.
