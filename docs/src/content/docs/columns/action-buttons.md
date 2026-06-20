---
title: Action Buttons
description: Configuring individual action buttons on table rows.
---

`Action` is the basic component for creating per-row buttons. This action is rendered as an interactive button (or an Anchor link styled as a button) in the rightmost column of the table.

## Basic Usage

Each action requires a unique name (as an identifier) and usually a target URL. You register these row actions inside an `ActionColumn`.

```php
use Kinetics\Columns\ActionColumn;
use Kinetics\Actions\Action;

ActionColumn::make()
    ->actions([
        Action::make('publish')
            ->label('Publish Post')
            ->href(fn ($record) => route('posts.publish', $record))
    ])
```

The closure in the `href()` or `href()` method will receive the Eloquent model for the current row (in the example above, `$record`), allowing you to extract its ID or slug to generate a dynamic URL.

## Preset Actions (Shortcuts)

Kinetics provides built-in static methods for the three most common actions: `view()`, `edit()`, and `delete()`. These presets automatically configure the appropriate labels, icons, variants (colors), and even confirmation dialogues (for delete).

Furthermore, you can simply pass the **Laravel Route Name** as a string, and Kinetics will automatically inject the current row's ID into the route parameters!

```php
ActionColumn::make()
    ->actions([
        Action::view('users.show'),
        // Automatically equivalent to:
        // Action::make('view')->label('View')->icon('eye')->variant('outline')->href(fn($r) => route('users.show', $r['id']))
        
        Action::edit('users.edit'),
        
        Action::delete('users.destroy'),
        // Automatically adds a "trash" icon, "destructive" (red) variant, 
        // HTTP method 'delete', and a confirmation dialog!
    ])
```

## Dynamic Visibility (Conditional)

One of the most powerful features of Actions in Kinetics is the ability to show or hide them based on data conditions in that row.

### Hiding Actions

Use the `visibleWhen()` method by passing a closure function that returns a boolean value (`true` to show).

```php
Action::make('publish')
    ->label('Publish')
    ->href(fn ($post) => route('posts.publish', $post))
    ->visibleWhen(fn ($post) => $post->status !== 'published')
```
*In the example above, the "Publish" button will only appear on unpublished articles.*

### Disabling Actions

Unlike `visibleWhen()`, the `disabledWhen()` method will still display the button, but make it unclickable (grayed out).

```php
Action::make('delete')
    ->label('Delete')
    ->href(fn ($post) => route('posts.destroy', $post))
    ->disabledWhen(fn ($post) => $post->is_locked)
```

## Appearance Customization

Even though the logic is on the backend, you can send specific metadata like icons to be consumed by the frontend.

```php
Action::make('edit')
    ->label('Edit')
    ->href(fn ($post) => route('posts.edit', $post))
    ->icon('pencil') // Instructs the frontend to render a pencil icon
```

### Display Modes

Sometimes you want an action to only display its icon (hiding the text) to save space, or conversely, only display text. Kinetics provides responsive display modes out of the box via the `HasDisplayMode` trait.

```php
Action::make('view')
    ->label('View Details')
    ->icon('eye')
    ->iconOnly() // The button will only show the eye icon
    
Action::make('delete')
    ->label('Delete')
    ->icon('trash')
    ->textOnly() // The button will only show the text 'Delete', hiding the trash icon
```

**Responsive Display:**
If you want the button to show both icon and text on desktop, but collapse to an icon-only button on mobile screens, use `iconOnlyOnMobile()`:

```php
Action::make('edit')
    ->label('Edit')
    ->icon('pencil')
    ->iconOnlyOnMobile() // Text visible on md+ screens, icon only on sm screens
```

## Advanced Interactivity

### Confirmation Dialogs (`confirm`)

Need to warn users before they trigger an action? Use `confirm()`. It will automatically trigger a frontend modal dialog before processing the click.

```php
Action::make('ban_user')
    ->label('Ban')
    ->variant('destructive')
    ->href(fn($row) => route('users.ban', $row['id']))
    ->confirm('Are you sure?', 'This user will lose all access immediately.')
```

### HTTP Methods (`method`)

If your action triggers a state change (like delete or update), you should change the HTTP method. The frontend will automatically execute a non-GET request.

```php
Action::delete('users.destroy')->method('delete')
```

### Opening in a Modal (`modal`)

If your href points to an Inertia route that should be rendered in a popup modal (rather than navigating the entire page), just chain the `modal()` method!

```php
Action::make('quick_edit')
    ->label('Quick Edit')
    ->href(fn($row) => route('users.edit', $row['id']))
    ->modal()
```

## API Reference

| Method | Description |
|--------|-------------|
| `make(string $key)` | Creates a new action instance. |
| `label(string $label)` | Sets the button label. |
| `icon(string $icon)` | Sets the button icon. |
| `variant(ActionVariant\|string $variant)` | Sets the button variant (e.g., default, outline, destructive). |
| `href(string\|\Closure $href)` | Sets the target URL or route name. |
| `method(string $method)`| Sets the HTTP method (get, post, put, delete, etc.). |
| `confirm(...)` | Attaches a confirmation dialog to the action. |
| `visibleWhen(bool\|\Closure $condition)`| Dynamically shows or hides the action per row. |
| `disabledWhen(bool\|\Closure $condition)`| Dynamically enables or disables the action per row. |
| `modal(bool $value = true)` | Opens the target link inside a modal. |
| `iconOnly(bool $value = true)` | Hides the text and only shows the icon. |
| `textOnly(bool $value = true)` | Hides the icon and only shows the text. |
| `iconOnlyOnMobile(bool $value = true)` | Collapses text on mobile screens. |
