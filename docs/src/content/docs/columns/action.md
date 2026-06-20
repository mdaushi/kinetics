---
title: Action Column
description: A dedicated column to hold action buttons for each row.
---

Unlike regular columns that display text or data from your database, `ActionColumn` is a special column designed exclusively to hold interactive buttons (Actions) for a specific row.

## Basic Concept

Because row actions (like "Edit", "Delete", or "View") apply to a specific record, they must be rendered alongside the data in the table. Therefore, Kinetics treats them as a column.

### Differences from Regular Columns
`ActionColumn` has a few unique characteristics:
- It does not have a header label by default.
- It cannot be sorted (`sortable()`) or searched (`searchable()`).
- Its primary purpose is to act as a container for `Action` or `ActionGroup` components.

## Usage

You place `ActionColumn` inside your `columns()` array, usually at the very end so the buttons appear on the far right of the table.

```php
use Kinetics\Table;
use Kinetics\Columns\TextColumn;
use Kinetics\Columns\ActionColumn;
use Kinetics\Actions\Action;

$table = Table::model(User::class)
    ->columns([
        TextColumn::make('name'),
        TextColumn::make('email'),
        
        // The Action Column container
        ActionColumn::make()
            ->actions([
                // You put your Action objects here
                Action::edit('users.edit'),
                Action::delete('users.destroy'),
            ])
    ])
    ->make();
```

## Adding Actions

The `->actions([...])` method of the `ActionColumn` expects an array of Action objects. 
To learn how to create and configure these Action objects (including buttons, colors, icons, and groups), please read the comprehensive guide in the [Action Buttons](/columns/action-buttons) section.

## API Reference

| Method | Description |
|--------|-------------|
| `make(string $key = '__actions')` | Creates a new action column instance. |
| `actions(array $actions)` | Registers the action objects for this column. |
| `hidden(bool $value = true)` | Hides the column from the table. |
