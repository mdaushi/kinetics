---
title: Filters Overview
description: Introduction to the powerful filtering system in Kinetics.
---

Filters allow your users to narrow down the dataset based on specific criteria. Unlike the global search (which blindly searches text using `LIKE`), filters are targeted to specific columns and can use various logical operators (such as exact matches, ranges, or comparisons).

## Registering Filters

You define filters on your table by passing an array of filter objects into the `filters()` method.

```php
use Kinetics\Table;
use Kinetics\Filters\TextFilter;
use Kinetics\Filters\SelectFilter;
use Kinetics\Filters\DateFilter;

$table = Table::model(User::class)
    ->filters([
        TextFilter::make('name'),
        SelectFilter::make('status')->options(['active' => 'Active', 'banned' => 'Banned']),
        DateFilter::make('created_at')->range(),
    ])
    ->columns([
        // ...
    ])
    ->make();
```

## How It Works

Behind the scenes, the `FilterPipe` automatically reads the incoming HTTP request payload. If it detects filter parameters that match the registered filter keys, it automatically applies the correct `WHERE` clauses to the Eloquent query.

Kinetics automatically generates the appropriate frontend UI components based on the filter type (e.g., a dropdown for `SelectFilter`, a date picker for `DateFilter`).

## Relationships (Dot Notation)

Just like Columns, Filters fully support Eloquent relationships via dot notation! If you want to filter records based on a related model, simply use the relationship name.

Kinetics is smart enough to automatically wrap your condition inside a `whereHas` query block.

```php
// Automatically creates a whereHas('company') query!
TextFilter::make('company.name')->label('Company Name')
```

On the next pages, we will dive into each specific filter type.
