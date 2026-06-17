---
title: Select Filter
description: Using the SelectFilter component.
---

`SelectFilter` renders a dropdown menu (select input) on the frontend, which is ideal for columns with a discrete set of possible values, such as "status" or "roles".

## Basic Usage

You define a `SelectFilter` and pass an array of options to the `options()` method.

```php
use Kinetics\Filters\SelectFilter;

SelectFilter::make('status')
    ->options([
        'active' => 'Active',
        'inactive' => 'Inactive',
        'banned' => 'Banned',
    ])
```

## Option Formats

The `options()` method is highly flexible and accepts arrays in various formats:

### 1. Associative Array
Maps the actual database value (key) to the UI Label (value).
```php
->options([
    1 => 'Published',
    0 => 'Draft'
])
```

### 2. Simple List
If the value and the label are identical, you can pass a flat array.
```php
->options(['Admin', 'Editor', 'User'])
```

### 3. Formatted Array
You can directly pass an array of objects/arrays with `value` and `label` keys, which matches the exact format TanStack uses.
```php
->options([
    ['value' => 'active', 'label' => 'Active Account'],
    ['value' => 'banned', 'label' => 'Banned Account'],
])
```

## Operators

The SelectFilter automatically restricts the query operator to either `is` (exact match) or `is_not` (exclude match).
