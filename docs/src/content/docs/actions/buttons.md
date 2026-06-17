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
