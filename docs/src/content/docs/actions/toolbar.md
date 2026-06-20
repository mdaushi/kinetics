---
title: Toolbar Actions
description: Global actions displayed in the table toolbar.
---

Toolbar Actions are global buttons that appear in the top toolbar of your table. They do not have a row context and are typically used for page-level actions such as creating a new record, importing data, or exporting the entire table.

## Basic Usage

Use the `ToolbarAction` class. You can assign labels, icons, routes, and visibility rules exactly like you would with regular row actions.

```php
use Kinetics\Actions\ToolbarAction;

Table::model(User::class)
    ->actions([
        ToolbarAction::make('create')
            ->label('Create User')
            ->icon('plus')
            ->href(route('users.create')),

        ToolbarAction::make('import')
            ->label('Import CSV')
            ->icon('upload')
            ->href(route('users.import'))
            ->method('post'),
    ])
```

## Preset Actions (Shortcuts)

Kinetics provides built-in static methods for the most common toolbar actions. These presets automatically configure the appropriate labels and icons.

```php
ToolbarAction::create(route('users.create')); 
// Automatically adds a 'plus' icon and 'Create' label

ToolbarAction::import(route('users.import')); 
// Automatically adds an 'upload' icon and 'Import' label
```

## Dynamic Visibility (Conditional)

Toolbar actions can be shown or hidden dynamically. Since toolbar actions do not apply to a specific row, the closure passed to the condition does *not* receive the row record.

### Hiding Actions

Use the `visibleWhen()` method to conditionally show the action.

```php
ToolbarAction::make('import')
    ->label('Import Data')
    ->href(route('users.import'))
    ->visibleWhen(fn () => auth()->user()->can('import_users'))
```

### Disabling Actions

Use `disabledWhen()` to make the button unclickable (grayed out) rather than hiding it entirely.

```php
ToolbarAction::make('sync')
    ->label('Sync Now')
    ->disabledWhen(fn () => Cache::has('sync_in_progress'))
```

## Appearance Customization

You can fully customize the look and feel of the toolbar buttons.

```php
ToolbarAction::make('create')
    ->label('New Document')
    ->icon('file-text')
    ->variant('default') // 'default', 'outline', 'destructive', 'ghost'
```

### Display Modes

If your toolbar has many buttons and space is limited, you can adjust the display mode.

```php
ToolbarAction::make('refresh')
    ->icon('refresh-cw')
    ->iconOnly() // Hides the text label

ToolbarAction::make('create')
    ->label('Create')
    ->icon('plus')
    ->iconOnlyOnMobile() // Shows text on desktop, but collapses to icon-only on small screens
```

## Advanced Interactivity

### Confirmation Dialogs (`confirm`)

If an action performs a destructive or major operation (like "Delete All"), you should ask for confirmation first.

```php
ToolbarAction::make('truncate')
    ->label('Delete All Data')
    ->variant('destructive')
    ->href(route('users.truncate'))
    ->method('delete')
    ->confirm('Are you absolutely sure?', 'This will wipe the entire database table.')
```

### Opening in a Modal (`modal`)

If you want the toolbar action to trigger an Inertia modal dialog instead of navigating away from the page, you can use the `->modal()` method.

```php
ToolbarAction::create(route('users.create'))->modal()
```

## API Reference

| Method | Description |
|--------|-------------|
| `make(string $key)` | Creates a new toolbar action instance. |
| `create(?string $route)` | **[Preset]** Creates a 'Create' button with a plus icon. |
| `import(?string $route)` | **[Preset]** Creates an 'Import' button with an upload icon. |
| `label(string $label)` | Sets the button label. |
| `icon(string $icon)` | Sets the button icon. |
| `variant(ActionVariant\|string $variant)` | Sets the button variant (e.g., default, outline, destructive). |
| `href(string\|\Closure $href)` | Sets the target URL or route name. |
| `method(string $method)`| Sets the HTTP method (get, post, put, delete, etc.). |
| `confirm(...)` | Attaches a confirmation dialog to the action. |
| `visibleWhen(bool\|\Closure $condition)`| Dynamically shows or hides the action. |
| `disabledWhen(bool\|\Closure $condition)`| Dynamically enables or disables the action. |
| `modal(bool $value = true)` | Opens the target link inside a modal. |
| `iconOnly(bool $value = true)` | Hides the text and only shows the icon. |
| `textOnly(bool $value = true)` | Hides the icon and only shows the text. |
| `iconOnlyOnMobile(bool $value = true)` | Collapses text on mobile screens. |
| `meta(array $meta)` | Sends arbitrary metadata to the frontend. |
