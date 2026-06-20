---
title: Bulk Action Groups
description: Grouping multiple Bulk Actions into dropdown menus.
---

When you have too many bulk actions available, your floating action bar can quickly become cluttered. Kinetics solves this by providing **Action Groups**, which allow you to consolidate multiple `BulkAction`s into a single, clean dropdown menu.

A `BulkActionGroup` wraps multiple `BulkAction` components into a dropdown trigger button rendered in the floating action bar (or toolbar) when rows are selected.

## Basic Usage

You pass an array of `BulkAction` instances into the `actions()` method of the group.

```php
use Kinetics\Actions\BulkActionGroup;
use Kinetics\Actions\BulkAction;

Table::model(User::class)
    ->bulkActions([
        BulkActionGroup::make('more_options')
            ->label('More')
            ->icon('ellipsis-horizontal')
            ->bulkActions([
                BulkAction::archive(route('users.archive')),
                BulkAction::restore(route('users.restore')),
                BulkAction::make('ban')
                    ->label('Ban Selected')
                    ->variant('destructive')
                    ->method('post'),
            ])
    ])
```

## UI Positions

Just like individual bulk actions, a `BulkActionGroup` defaults to the `floating` position. You can change this to `toolbar` if you prefer the dropdown to appear in the top toolbar when rows are selected.

```php
BulkActionGroup::make('more')->toolbar()->bulkActions([...])
```

## Customizing the Trigger Button

The group class inherits from `BaseActionGroup`, meaning the trigger button itself supports dynamic visibility and display modes!

### Display Modes

To save even more space, you can hide the text of the dropdown trigger button so that only the icon is visible (a classic "kebab menu" style).

```php
BulkActionGroup::make('more')
    ->icon('more-vertical')
    ->iconOnly()
    ->bulkActions([...])
```

### Dynamic Visibility

You can completely hide the entire dropdown group if the user doesn't have permission to see any of the actions inside it.

```php
BulkActionGroup::make('danger_zone')
    ->visibleWhen(fn () => auth()->user()->isSuperAdmin())
    ->bulkActions([...])
```

---

## API Reference

| Method | Description |
|--------|-------------|
| `make(string $key)` | Creates a new bulk action group instance. |
| `actions(array $actions)` | Registers the child `BulkAction` objects. |
| `label(string $label)` | Sets the dropdown trigger button label. |
| `icon(string $icon)` | Sets the dropdown trigger button icon. |
| `variant(ActionVariant\|string $variant)`| Sets the trigger button variant. |
| `position(string $position)`| Defines where the group appears (`toolbar` or `floating`). |
| `toolbar()`| Places the group in the toolbar. |
| `floating()`| Places the group in a floating bar. |
| `iconOnly(bool $value = true)` | Shows only the icon for the trigger button. |
| `textOnly(bool $value = true)` | Shows only the text for the trigger button. |
| `iconOnlyOnMobile(bool $value = true)` | Collapses text on mobile screens. |
| `visibleWhen(bool\|\Closure $condition)`| Dynamically shows or hides the entire group. |
| `disabledWhen(bool\|\Closure $condition)`| Dynamically disables the trigger button. |
| `meta(array $meta)` | Sends arbitrary metadata to the frontend. |