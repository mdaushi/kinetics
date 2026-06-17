---
title: Number Filter
description: Using the NumberFilter component for numeric data and ranges.
---

`NumberFilter` is specifically designed for numeric columns like prices, quantities, ages, or IDs. It renders a number input on the frontend and supports mathematical comparisons.

## Basic Usage

By default, the `NumberFilter` provides mathematical operators to let users find exact or relative numeric values.

```php
use Kinetics\Filters\NumberFilter;

NumberFilter::make('price')
```

The available default operators are:
- `equals` (Exact match)
- `not_equals`
- `>` (Greater than)
- `>=` (Greater than or equal to)
- `<` (Less than)
- `<=` (Less than or equal to)

## Number Range (Min & Max)

Just like the `DateFilter`, the `NumberFilter` also supports a powerful `range()` method. 

This allows users to search for numbers between a minimum and maximum value simultaneously (for example, finding products priced between $50 and $100).

```php
use Kinetics\Filters\NumberFilter;

NumberFilter::make('price')->range()
```

When `range()` is enabled:
1. The frontend UI transforms to accept two numeric inputs (Minimum and Maximum).
2. The operator is forcefully set to `between`.
3. The SQL query generated will use the `WHERE ... BETWEEN` logic.
