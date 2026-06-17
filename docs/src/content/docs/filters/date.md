---
title: Date Filter
description: Using the DateFilter component for exact dates or date ranges.
---

`DateFilter` renders a date picker in the UI and automatically applies the correct timestamp querying logic to your database.

## Basic Usage

By default, the `DateFilter` acts as a single-date selector. It comes with three default operators:
- `equals` (Exact date)
- `<` (Before this date)
- `>` (After this date)

```php
use Kinetics\Filters\DateFilter;

DateFilter::make('created_at')
```

## Date Range Selection

Instead of picking a single date, you can configure the filter to accept a **Start Date** and an **End Date**. 

To enable this, call the `range()` method.

```php
use Kinetics\Filters\DateFilter;

DateFilter::make('created_at')->range()
```

When `range()` is enabled:
1. The frontend UI transforms into a dual date-picker (start and end).
2. The available operator is forcefully set to `between`.
3. The SQL query generated will use the `WHERE ... BETWEEN` logic.
