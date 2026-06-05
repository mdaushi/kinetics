# Kinetics

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mdaushi/kinetics.svg?style=flat-square)](https://packagist.org/packages/mdaushi/kinetics)
[![npm version](https://img.shields.io/npm/v/@mdaushi/kinetics-react.svg?style=flat-square)](https://www.npmjs.com/package/@mdaushi/kinetics-react)
[![Tests](https://img.shields.io/github/actions/workflow/status/mdaushi/kinetics/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mdaushi/kinetics/actions/workflows/tests.yml)
[![License](https://img.shields.io/packagist/l/mdaushi/kinetics.svg?style=flat-square)](https://packagist.org/packages/mdaushi/kinetics)

**Zero-Friction Server-Side Datatables for Laravel, Inertia.js, and React.**

Kinetics is the ultimate table layer for your Laravel + Inertia.js stack. Everything — sorting, filtering, searching, and pagination — runs seamlessly server-side through a clean and composable Eloquent query pipeline.

Building a table in an Inertia.js app usually means wiring up query parameters, managing state, and keeping the UI in sync with the server — all by hand. Kinetics eliminates that friction. Define your columns and actions on the backend, drop a single component into your page, and get a fully functional, reactive table with zero boilerplate.

Under the hood, the React adapter is built on **TanStack Table** for flexible, headless table logic and **shadcn/ui** for accessible, composable UI components — so you get a polished experience out of the box while retaining full control to customise when needed.


## Installation

**1. Install the Laravel package**

```bash
composer require mdaushi/kinetics
```

**2. Install the React package**

```bash
npm install @mdaushi/kinetics-react
```

**3. Configure Tailwind CSS**

Since this package uses Tailwind classes, you need to tell Tailwind to scan the package's dist files so those classes are not purged during build.

**Tailwind v4** — add `@source` to `resources/css/app.css`:

```css
@import "tailwindcss";

@source "../../node_modules/@mdaushi/kinetics-react/dist";
```

**Tailwind v3** — add the path to `tailwind.config.js`:

```js
export default {
  content: [
    // ... existing paths
    './node_modules/@mdaushi/kinetics-react/dist/**/*.js',
  ],
}
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

| Package | Registry | Description |
|---|---|---|
| [`mdaushi/kinetics`](https://packagist.org/packages/mdaushi/kinetics) | Packagist | PHP/Laravel package — server-side pipeline, columns, actions |
| [`@mdaushi/kinetics-react`](https://www.npmjs.com/package/@mdaushi/kinetics-react) | npm | React package — `<Table>` component, `useTable` hook |

---

## Documentation

- [Backend (Laravel)](./docs/laravel-backend.md) — Installation, columns, actions, pipes, complete API reference
- [Frontend (React)](./docs/react-frontend.md) — <Table> component and useTable hook
- [Custom Pipes](./docs/custom-pipes.md) — A guide to creating custom pipes + built-in pipe reference
- [Architecture](./docs/architecture.md) — Internal design: pipelines, contexts, response builders

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
# Build core types first
pnpm --filter @mdaushi/kinetics-core run build

# Then build the React package
pnpm --filter @mdaushi/kinetics-react run build
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
│   ├── core/               # @mdaushi/kinetics-core (shared types)
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
