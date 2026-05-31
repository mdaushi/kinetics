# Kinetics

**Zero-Friction Tables for Inertia.js.**

Kinetics is the table layer for Laravel + Inertia.js. Everything — sorting, filtering, searching, pagination — runs server-side through a clean and a composable query pipeline.

Building a table in an Inertia.js app usually means wiring up query parameters, managing state, and keeping the UI in sync with the server — all by hand. Kinetics eliminates that friction. Define your columns and actions on the backend, drop a single component into your page, and get a fully functional, reactive table with zero boilerplate.

Under the hood, the React adapter is built on **TanStack Table** for flexible, headless table logic and **shadcn/ui** for accessible, composable UI components — so you get a polished experience out of the box while retaining full control to customise when needed.

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

1. **Backend (Laravel)** — define columns, actions, and query. Run the pipeline and return data via Inertia.
2. **Frontend (React)** — receive data from Inertia props, render the table with sorting/filtering/pagination that trigger new server requests automatically.

## Packages

| Package | Description |
|---|---|
| `mdaushi/kinetics-laravel` | PHP/Laravel package — server side |
| `@mdaushi/kinetics-react` | React package — ready-to-use components & hook |

## Documentation

- [Backend (Laravel)](./laravel-backend.md) — Installation, column config, actions, custom pipes
- [Frontend (React)](./react-frontend.md) — Installation, `<Table>` component, `useTable` hook
