---
title: Actions Overview
description: Introduction to the Action component and Global Actions in Kinetics.
---

Actions are interactive buttons that allow users to perform operations, such as navigating to a creation page, editing a row, or deleting data. In Kinetics, actions are fully defined and controlled from the server (Laravel).

The core component that drives all of this is the `Action` class.

## Where to place Actions?

There are two distinct placements for Actions in a Kinetics table:

### 1. Global Actions (General Actions)
Global actions are buttons that apply to the entire table, not to a specific row. They are typically used for "Create New" buttons, "Export" buttons, etc., and are rendered at the top of the table in the **Toolbar**.

You register global actions by calling the `actions()` method directly on the `Table` instance.

```php
use Kinetics\Table;
use Kinetics\Actions\Action;

$table = Table::model(User::class)
    ->actions([
        // This is a Global Action
        Action::make('create')
            ->label('Create User')
            ->href(route('users.create'))
    ])
    ->columns([
        // ... column definitions
    ])
    ->make();
```

### 2. Row Actions (Action Column)
Row actions are buttons that apply to a specific record (row), such as "Edit" or "Delete". To add these, you must place your `Action` components inside an `ActionColumn`.

*(To learn how to set up the container for row actions, please read the [Action Column](/columns/action) documentation).*

## Configuring Actions

Whether you are placing an Action globally in the toolbar, or specifically in a row inside an `ActionColumn`, the way you configure the `Action` component remains exactly the same!

On the next pages, we will discuss all the powerful features of the `Action` class, such as preset shortcuts, dynamic visibility, and dropdown groups.
