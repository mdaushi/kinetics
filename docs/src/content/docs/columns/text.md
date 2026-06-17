---
title: Text Column
description: Display and format text data, dates, and badges in the table.
---

`TextColumn` is the most versatile and commonly used column type in Kinetics. It is used to display strings, numbers, dates, or even transform text into visual badges.

## Basic Usage

Use the static `make()` method with the attribute name from your database.

```php
use Kinetics\Columns\TextColumn;

TextColumn::make('title')
```

## Relationships

You can access properties from Eloquent relationships using dot notation. For example, if your `Post` model has an `author()` relationship, and you want to display the author's name:

```php
TextColumn::make('author.name')->label('Author')
```

## Searching and Sorting

`TextColumn` is a prime candidate for the search (`searchable()`) and sort (`sortable()`) features.

```php
TextColumn::make('title')
    ->sortable()
    ->searchable()
```

---

## Formatting Dates & Times

Kinetics provides built-in methods to easily format your `created_at`, `updated_at`, or any date columns without needing a custom format closure. It automatically uses Carbon behind the scenes.

### `date()`
Formats the column as a date. Default format is `Y-m-d`.

```php
TextColumn::make('created_at')->date('d M Y') // Output: 17 Jun 2026
```

### `time()`
Formats the column as a time. Default format is `H:i:s`.

```php
TextColumn::make('created_at')->time('H:i') // Output: 13:00
```

### `dateTime()`
Formats the column as both date and time. Default format is `Y-m-d H:i:s`.

```php
TextColumn::make('created_at')->dateTime('d M Y H:i') 
```

---

## Badges and Colors

Instead of creating a separate column type for statuses, you can transform any `TextColumn` into a Badge component natively.

### Enabling Badge Mode
Use the `badge()` method. You can also pass a boolean condition to dynamically decide if it should be rendered as a badge.

```php
TextColumn::make('status')
    ->label('Order Status')
    ->badge()
```

### Dynamic Colors

The true power of badges lies in the `color()` method. It allows you to assign specific `shadcn/ui` color variants based on the row's exact value. 

Supported color variants: `'default'`, `'secondary'`, `'destructive'`, `'outline'`, `'ghost'`, `'link'`.

#### 1. Static Color
Apply a single color to all badges in this column.

```php
TextColumn::make('role')
    ->badge()
    ->color('secondary')
```

#### 2. Value-based Color Mapping
Pass an array to map specific column values to specific color variants. This is perfect for status columns!

```php
TextColumn::make('status')
    ->badge()
    ->color([
        'published' => 'default',
        'draft'     => 'secondary',
        'deleted'   => 'destructive',
    ])
```
