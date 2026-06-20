---
title: Toolbar Action Groups
description: Grouping multiple Toolbar Actions into dropdown menus.
---

When you have too many actions available at the table level, your UI can quickly become cluttered. Kinetics solves this by providing **Action Groups**, which allow you to consolidate multiple `ToolbarAction`s into a single, clean dropdown menu.

A `ToolbarActionGroup` wraps multiple `ToolbarAction` components into a dropdown trigger button rendered in the table's top toolbar.

## Basic Usage

You pass an array of `ToolbarAction` instances into the `actions()` method of the group.

```php
use Kinetics\Actions\ToolbarActionGroup;
use Kinetics\Actions\ToolbarAction;

Table::model(User::class)
    ->actions([
        ToolbarActionGroup::make('export_options')
            ->label('Export Data')
            ->icon('download')
            ->actions([
                ToolbarAction::make('csv')
                    ->label('Export to CSV')
                    ->href(route('export.csv')),
                    
                ToolbarAction::make('pdf')
                    ->label('Export to PDF')
                    ->href(route('export.pdf')),
            ])
    ])
```

## Customizing the Trigger Button

The group class inherits from `BaseActionGroup`, meaning the trigger button itself supports dynamic visibility and display modes!

### Display Modes

To save even more space, you can hide the text of the dropdown trigger button so that only the icon is visible (a classic "kebab menu" style).

```php
ToolbarActionGroup::make('more')
    ->icon('more-vertical')
    ->iconOnly()
    ->actions([...])
```

### Dynamic Visibility

You can completely hide the entire dropdown group if the user doesn't have permission to see any of the actions inside it.

```php
ToolbarActionGroup::make('danger_zone')
    ->visibleWhen(fn () => auth()->user()->isSuperAdmin())
    ->actions([...])
```

---

## API Reference

| Method | Description |
|--------|-------------|
| `make(string $key)` | Creates a new toolbar action group instance. |
| `actions(array $actions)` | Registers the child `ToolbarAction` objects. |
| `label(string $label)` | Sets the dropdown trigger button label. |
| `icon(string $icon)` | Sets the dropdown trigger button icon. |
| `variant(ActionVariant\|string $variant)`| Sets the trigger button variant. |
| `iconOnly(bool $value = true)` | Shows only the icon for the trigger button. |
| `textOnly(bool $value = true)` | Shows only the text for the trigger button. |
| `iconOnlyOnMobile(bool $value = true)` | Collapses text on mobile screens. |
| `visibleWhen(bool\|\Closure $condition)`| Dynamically shows or hides the entire group. |
| `disabledWhen(bool\|\Closure $condition)`| Dynamically disables the trigger button. |
| `meta(array $meta)` | Sends arbitrary metadata to the frontend. |