---
title: Searching
description: How global search works in the table.
---

The search feature in Kinetics is managed by a component called `SearchPipe`. This feature allows users to search for keywords across all table columns that have been set as `searchable`.

## Enabling Search

By default, columns will not be included in the global search to avoid excessively heavy database queries. You must explicitly enable it on each column using the `searchable()` method.

```php
use Kinetics\Table;
use Kinetics\Columns\TextColumn;

$table = Table::model(User::class)
    ->columns([
        // This column will be ignored by the search feature
        TextColumn::make('id'),
        
        // This column will be searched by SearchPipe
        TextColumn::make('name')->searchable(),
        TextColumn::make('email')->searchable(),
    ])
    ->make();
```

## How SearchPipe Works

When a request from the frontend sends a search parameter (e.g., `?search=john`), `SearchPipe` will:
1. Get a list of all columns that have the `searchable` status.
2. Combine them into the Eloquent query builder using the `LIKE` clause (or equivalent, depending on the database driver).
3. For regular columns, a query like `WHERE name LIKE '%john%' OR email LIKE '%john%'` will be formed.
4. For relationship columns (e.g., `company.name`), a `whereHas` or automatic `join` mechanism will be formed so that the search can penetrate relationship boundaries.

> **Performance Note:** The more columns you set as `searchable()`, the more complex the resulting query will be. Enable this feature only on text columns that are truly relevant.
