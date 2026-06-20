---
title: Table Actions Overview
description: Introduction to table-level actions (Toolbar and Bulk actions) in Kinetics.
---

While row-level actions (like Edit or Delete for a specific record) are handled individually inside an `ActionColumn`, Kinetics also provides powerful **table-level actions**. These are actions that apply to the entire table collectively, or to multiple selected rows at once.

In this section, we will discuss the two types of table-level actions provided by Kinetics:

## 1. Toolbar Actions

Global buttons that apply to the entire page or dataset, such as "Create New" or "Import CSV". They do not rely on row selection and are rendered constantly at the top of the table in the **Toolbar**.

You register toolbar actions by calling the `actions()` method directly on your `Table` instance.

```php
use Kinetics\Table;
use Kinetics\Actions\ToolbarAction;

$table = Table::model(User::class)
    ->bulkActions([
        ToolbarAction::make('create')
            ->label('Create User')
            ->href(route('users.create'))
    ])
    ->columns([
        // ...
    ])
    ->make();
```

To learn more about configuration and presets, read the [Toolbar Actions](/actions/toolbar) documentation.

## 2. Bulk Actions

Batch operations that apply to multiple selected rows at once, such as "Delete Selected" or "Export Selected". They automatically appear in a floating bar (or within the toolbar) whenever the user selects checkboxes in the table.

Bulk actions are also registered in the same `actions()` method on the `Table` instance.

```php
use Kinetics\Table;
use Kinetics\Actions\BulkAction;

$table = Table::model(User::class)
    ->bulkActions([
        BulkAction::delete('users.bulk-delete')
    ])
    ->columns([
        // ...
    ])
    ->make();
```

To learn more about processing payloads and selection states, read the [Bulk Actions](/actions/bulk) documentation.

---
> [!NOTE]
> Looking for row-specific actions (like an individual "Edit" button next to a record)? Please refer to the [Action Column](/columns/action) documentation.
