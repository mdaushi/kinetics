---
title: Action Groups
description: Grouping multiple actions into a dropdown menu.
---

When your table has too many actions for a single row (e.g., View, Edit, Delete, Duplicate, Archive), displaying them all inline will make the table look cluttered and break the layout. `ActionGroup` solves this problem by combining these actions into a Dropdown Menu.

## Usage

Use `ActionGroup::make()` to set its label (defaults to 'Actions'), and then provide an array of `Action` objects into its `actions()` method.

```php
use Kinetics\Actions\ActionGroup;
use Kinetics\Actions\Action;

ActionGroup::make('More Options')
    ->actions([
        Action::make('view')
            ->label('View')
            ->href(fn ($record) => route('users.show', $record)),
            
        Action::make('edit')
            ->label('Edit')
            ->href(fn ($record) => route('users.edit', $record)),
            
        Action::make('delete')
            ->label('Delete')
            ->href(fn ($record) => route('users.destroy', $record))
    ])
```

## Frontend Result

Kinetics will convert the `ActionGroup` above into a Dropdown component (utilizing the DropdownMenu component from `shadcn/ui`). Users will see a button with a three-dot icon (ellipsis or more), and when clicked, the registered options will appear in a popover menu.

## Conditional Evaluation in Groups

If you use conditional functions like `visibleWhen()` on one of the `Action`s inside the `ActionGroup`, Kinetics will still evaluate it correctly.

If a user doesn't have access to "Delete" an item (because the `visibleWhen()` evaluation returns `false`), then only the "View" and "Edit" options will appear in the dropdown.

```php
ActionGroup::make('More')
    ->actions([
        // ...
        Action::make('delete')
            ->label('Delete')
            ->href(fn ($record) => route('users.destroy', $record))
            // The delete option will disappear from the dropdown if this row is locked
            ->visibleWhen(fn ($record) => !$record->is_locked) 
    ])
```
