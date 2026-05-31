# Kinetics

**Zero-Friction Tables for Inertia.js.**

Kinetics is the table layer for Laravel + Inertia.js. Everything — sorting, filtering, searching, pagination — runs server-side through a clean and composable query pipeline.

Building a table in an Inertia.js app usually means wiring up query parameters, managing state, and keeping the UI in sync with the server — all by hand. Kinetics eliminates that friction. Define your columns and actions on the backend, drop a single component into your page, and get a fully functional, reactive table with zero boilerplate.

Under the hood, the React adapter is built on **TanStack Table** for flexible, headless table logic and **shadcn/ui** for accessible, composable UI components — so you get a polished experience out of the box while retaining full control to customise when needed.

---

## Quick Look

**Backend — define your table once:**

```php
// app/Http/Controllers/UserController.php

public function index()
{
    $table = Table::model(User::class)
        ->columns([
            Column::make('name')->sortable()->searchable(),
            Column::make('email')->sortable()->searchable(),
            Column::make('status')->filterable(['active' => 'Active', 'inactive' => 'Inactive']),
            ActionColumn::make()->actions([
                Action::edit('/users/:id/edit'),
                Action::delete('/users/:id'),
            ]),
        ])
        ->defaultSort('created_at', 'desc')
        ->make();

    return Inertia::render('Users/Index', ['table' => $table]);
}
```

**Frontend — one component, done:**

```tsx
// resources/js/Pages/Users/Index.tsx

import { Table, TableProps } from '@mdaushi/kinetics-react';

export default function UsersIndex({ table }: { table: TableProps<User> }) {
    return <Table table={table} searchPlaceholder="Search users..." />;
}
```

---

## How It Works

```
Browser Request (sort, search, filter, page)
        │
        ▼
Laravel Controller
  └─ Table::model() / Table::query()
       └─ Pipeline: Sort → Search → Filter → Paginate
              │
              ▼
         Inertia Response (data + columns + meta + state)
              │
              ▼
   React Component (<Table /> / useTable)
     └─ TanStack Table (UI rendering)
```

---

## Installation

**1. Laravel (server-side)**

```bash
composer require mdaushi/kinetics
```

**2. React (client-side)**

```bash
pnpm add @mdaushi/kinetics-react
pnpm add @inertiajs/react @tanstack/react-table  # peer dependencies
```

---

## Features

- **Server-side everything** — sort, search, filter, and paginate via Eloquent; no client-side data processing
- **Zero-boilerplate frontend** — one `<Table>` component renders the full UI out of the box
- **Composable pipeline** — add, remove, or replace query pipes to fit any use case
- **Fluent column API** — chainable methods for sortable, searchable, filterable, relation, and formatted columns
- **Action columns** — per-row buttons and dropdown groups with visibility/disabled conditions
- **TanStack Table** — full access to the underlying table instance for custom layouts
- **shadcn/ui components** — polished, accessible UI that matches your existing design system
- **TypeScript-first** — fully typed props, hooks, and column definitions

---

## Packages

| Package | Description |
|---|---|
| `mdaushi/kinetics` | PHP/Laravel package — server-side pipeline, columns, actions |
| `@mdaushi/kinetics-react` | React package — `<Table>` component, `useTable` hook |

---

## Documentation

- [Backend (Laravel)](./docs/laravel-backend.md)
- [Frontend (React)](./docs/react-frontend.md)

---

## Contributing

Contributions are welcome! Whether it's a bug fix, a new feature, or an improvement to the docs — all pull requests are appreciated.

### Local Setup

**1. Clone the repository**

```bash
git clone https://github.com/mdaushi/kinetics
cd kinetics
```

**2. Install PHP dependencies**

```bash
composer install
```

**3. Install JS dependencies**

```bash
pnpm install
```

**4. Build the packages**

```bash
# Build core types
cd packages/core && pnpm build

# Build the React package
cd packages/react && pnpm build
```

**5. Run the PHP test suite**

```bash
composer test
```

**6. Check & fix code style (PSR-12)**

```bash
composer lint        # Check for violations
composer lint:fix    # Auto-fix all violations
```

### Project Structure

```
kinetics/
├── src/                    # Laravel package source
│   ├── Table.php           # Main entry point
│   ├── Columns/            # Column & ActionColumn
│   ├── Actions/            # Action & ActionGroup
│   ├── Pipes/              # Query pipeline stages
│   ├── Resources/          # TableResult (response formatter)
│   └── Support/            # TableConfig, TableContext
├── packages/
│   └── react/              # @mdaushi/kinetics-react
│       └── src/
│           ├── components/ # <Table>, ActionCell, Toolbar, Pagination
│           └── hooks/      # useTable
├── tests/                  # PHPUnit test suite
└── docs/                   # Documentation
```

### Guidelines

- **Backend (PHP)** — follow PSR-12 coding style. Add PHPUnit tests for any new feature or bug fix under `tests/`.
- **Frontend (React/TS)** — keep components headless-friendly; UI logic belongs in `hooks/`, not in components.
- **New pipe** — implement `PipeInterface`, add it to `src/Pipes/`, and document it in `docs/laravel-backend.md`.
- **Commits** — use [Conventional Commits](https://www.conventionalcommits.org/) (`feat:`, `fix:`, `docs:`, `chore:`, etc.).

### Reporting Issues

Please [open an issue](https://github.com/mdaushi/kinetics/issues) with a clear description and, where possible, a minimal reproduction.
