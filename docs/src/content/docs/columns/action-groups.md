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
            ->variant('default')
            ->href(fn ($record) => route('users.show', $record)),
            
        Action::make('edit')
            ->label('Edit')
            ->variant('default')
            ->href(fn ($record) => route('users.edit', $record)),
            
        Action::delete()
            ->href(fn ($record) => route('users.destroy', $record))
    ])
```

## Allowed Variants

By default, an `Action` created via `Action::make()` uses the `outline` variant. However, `ActionGroup` renders its children inside a dropdown menu. Because of this UI constraint, **an `ActionGroup` strictly requires its child `Action`s to have a variant of either `default` or `destructive`**. Any other variant will throw an `InvalidArgumentException`.

If you are using preset actions like `Action::delete()`, the variant is already set to `destructive`. But if you use `Action::make()`, always remember to explicitly set the variant:

```php
Action::make('duplicate')
    ->label('Duplicate')
    ->variant('default') // Required inside ActionGroup
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
        Action::delete()
            ->href(fn ($record) => route('users.destroy', $record))
            // The delete option will disappear from the dropdown if this row is locked
            ->visibleWhen(fn ($record) => !$record->is_locked) 
    ])
```

## API Reference

| Method | Description |
|--------|-------------|
| `make(string $key)` | Creates a new action group instance. |
| `label(string $label)` | Sets the dropdown trigger label. |
| `icon(string $icon)` | Sets the dropdown trigger icon. |
| `actions(array $actions)` | Registers the child Action objects inside the dropdown. |
| `variant(ActionVariant\|string $variant)`| Sets the trigger button variant. |
| `iconOnly(bool $value = true)` | Shows only the icon for the trigger button. |
| `textOnly(bool $value = true)` | Shows only the text for the trigger button. |
| `iconOnlyOnMobile(bool $value = true)` | Collapses text on mobile screens. |
