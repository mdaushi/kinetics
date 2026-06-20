---
title: Bulk Actions
description: Operations on multiple selected rows.
---

Bulk Actions allow users to select multiple rows in the table via checkboxes and perform a batch operation on them.

When a bulk action is executed, Kinetics sends a payload to the server containing:
- `ids` (array of strings/integers): The primary keys of the selected rows.
- `select_all` (boolean): `true` if the user selected all records across all pages.

## Basic Usage

Use the `BulkAction` class.

```php
use Kinetics\Actions\BulkAction;

Table::model(User::class)
    ->bulkActions([
        BulkAction::make('archive')
            ->label('Archive Selected')
            ->icon('archive')
            ->href(route('users.bulk-archive'))
            ->method('post')
    ])
```

## UI Positions

By default, Bulk Actions appear in a **floating** action bar at the bottom center of the viewport whenever one or more rows are selected.

However, you can configure them to appear directly inside the **toolbar** instead by using the `->toolbar()` method:

```php
BulkAction::export(route('users.bulk-export'))->toolbar()
```
*Note: When positioned in the toolbar, the bulk actions will replace the regular Toolbar Actions while rows are selected.*

## Preset Actions (Shortcuts)

Kinetics includes built-in presets for common batch operations. These presets automatically assign appropriate icons, labels, colors (variants), and confirmation dialogues (where applicable):

```php
BulkAction::delete(route('users.bulk-delete'));
BulkAction::export(route('users.bulk-export'));
BulkAction::archive(route('users.bulk-archive'));
BulkAction::restore(route('users.bulk-restore'));
```

## Dynamic Visibility (Conditional)

Just like regular actions, you can restrict when a bulk action is available.

```php
BulkAction::delete('users.bulk-delete')
    ->visibleWhen(fn () => auth()->user()->isAdmin())
```

## Appearance Customization

You can change the variant (color) and icon of the bulk action. Note that floating bulk actions are `iconOnly` by default to save space, but you can override this.

```php
BulkAction::make('mark_paid')
    ->label('Mark as Paid')
    ->icon('check-circle')
    ->variant('default')
    ->textOnly() // Force text display even in the floating bar
```

## Advanced Interactivity

### Confirmation Dialogs (`confirm`)

Destructive bulk operations should always require confirmation. The `BulkAction::delete()` preset includes this automatically, but you can add it to any custom action:

```php
BulkAction::make('ban')
    ->label('Ban Users')
    ->variant('destructive')
    ->href(route('users.bulk-ban'))
    ->method('post')
    ->confirm('Ban selected users?', 'They will not be able to log in anymore.')
```

### HTTP Methods (`method`)

Because bulk actions typically mutate data, they should almost always use the `post`, `put`, or `delete` method.

```php
BulkAction::make('approve')->method('post')
```

## API Reference

| Method | Description |
|--------|-------------|
| `make(string $key)` | Creates a new bulk action instance. |
| `delete(?string $route)` | **[Preset]** Creates a 'Delete' button with trash icon and confirmation dialog. |
| `export(?string $route)` | **[Preset]** Creates an 'Export' button. |
| `archive(?string $route)`| **[Preset]** Creates an 'Archive' button with confirmation dialog. |
| `restore(?string $route)`| **[Preset]** Creates a 'Restore' button. |
| `label(string $label)` | Sets the button label. |
| `icon(string $icon)` | Sets the button icon. |
| `variant(ActionVariant|string $variant)` | Sets the button variant (e.g., default, outline, destructive). |
| `href(string\|\Closure $href)` | Sets the target URL or route name. |
| `method(string $method)`| Sets the HTTP method (get, post, put, delete, etc.). |
| `confirm(...)` | Attaches a confirmation dialog to the action. |
| `visibleWhen(bool\|\Closure $condition)`| Dynamically shows or hides the action. |
| `disabledWhen(bool\|\Closure $condition)`| Dynamically enables or disables the action. |
| `position(string $position)`| Defines where the action appears (`toolbar` or `floating`). |
| `toolbar()`| Short method to place the action in the toolbar instead of a floating bar. |
| `floating()`| Short method to explicitly place the action in a floating bar. |
| `iconOnly(bool $value = true)` | Hides the text and only shows the icon. |
| `textOnly(bool $value = true)` | Hides the icon and only shows the text. |
| `iconOnlyOnMobile(bool $value = true)` | Collapses text on mobile screens. |
| `meta(array $meta)` | Sends arbitrary metadata to the frontend. |
