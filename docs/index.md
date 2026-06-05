# Kinetics

**Zero-Friction Tables for Inertia.js — sorting, filtering, searching, and pagination through a clean composable pipeline.**

Kinetics is the table layer for Laravel + Inertia.js. Everything — sorting, filtering, searching, pagination — runs server-side through a clean and composable query pipeline.

Building a table in an Inertia.js app usually means wiring up query parameters, managing state, and keeping the UI in sync with the server — all by hand. Kinetics eliminates that friction. Define your columns and actions on the backend, drop a single component into your page, and get a fully functional, reactive table with zero boilerplate.

Under the hood, the React adapter is built on **TanStack Table** for flexible, headless table logic and **shadcn/ui** for accessible, composable UI components — so you get a polished experience out of the box while retaining full control to customise when needed.

---

## How It Works

```
Browser Request (sort, search, filter, page)
        │
        ▼
Laravel Controller
  └─ Table::model() / Table::query()
       └─ Pipeline: Sort → Search → Filter → [Custom Pipes] → Paginate
              │
              ▼
         Inertia / JSON Response
           { data, columns, meta, state }
              │
              ▼
   React Component (<Table /> / useTable)
     └─ TanStack Table + shadcn/ui
```

1. **Backend (Laravel)** — define columns, actions, and query. Run the pipeline and return data via Inertia.
2. **Frontend (React)** — receive data from Inertia props, render the table with sort/filter/pagination that automatically trigger new server requests.

---

## Packages

| Package | Description |
|---|---|
| `mdaushi/kinetics` | PHP/Laravel package — server side |
| `@mdaushi/kinetics-react` | React package — ready-to-use components & hook |

---

## Documentation

- [Backend (Laravel)](./laravel-backend.md) — Installation, column config, actions, pipes, full API reference
- [Frontend (React)](./react-frontend.md) — `<Table>` component and `useTable` hook
- [Custom Pipes](./custom-pipes.md) — Building custom pipes + built-in pipe reference
- [Architecture](./architecture.md) — Internal design: pipeline, context, response builder

---

## Quick Start

```bash
composer require mdaushi/kinetics
php artisan vendor:publish --tag=kinetics-config
```

```php
// app/Http/Controllers/UserController.php
use Kinetics\Table;
use Kinetics\Columns\TextColumn;
use Kinetics\Columns\ActionColumn;
use Kinetics\Actions\Action;

public function index()
{
    $table = Table::model(User::class)
        ->columns([
            TextColumn::make('name')->sortable()->searchable(),
            TextColumn::make('email')->sortable(),
            TextColumn::make('role')->filterable(['admin', 'user']),
            ActionColumn::make()->actions([
                Action::edit('users.edit'),
                Action::delete('users.destroy'),
            ]),
        ])
        ->defaultSort('name')
        ->perPage(15, 100)
        ->make();

    return Inertia::render('Users/Index', compact('table'));
}
```
