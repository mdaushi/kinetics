---
title: Text Filter
description: Using the TextFilter component.
---

`TextFilter` is the simplest filter. It renders a standard text input field and allows users to search for exact strings, partial matches, or use basic comparison operators.

## Basic Usage

Pass the column name to the static `make()` method.

```php
use Kinetics\Filters\TextFilter;

TextFilter::make('email')
```

## Available Operators

By default, the `TextFilter` supports a wide array of string and numeric operators out of the box:
- `equals` (Exact match)
- `not_equals`
- `contains` (Like `%value%`)
- `starts_with`
- `ends_with`
- `<`, `<=`, `>`, `>=` (Useful if the text field contains numbers or for alphabetical sorting logic)

The frontend UI will present these options in a dropdown next to the text input so the user can easily switch operators.

## API Reference

| Method | Description |
|--------|-------------|
| `make(string $key)` | Creates a new filter instance. |
| `label(string $label)` | Sets the filter label. |
| `column(string $column)`| Specifies the database column name if different from the key. |
| `relation(string $relation, string $relationKey)`| Explicitly sets the relationship to query. |
| `operators(array $operators)`| Overrides the allowed operators for this filter. |
