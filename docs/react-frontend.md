# Frontend (React)

The `@mdaushi/kinetics-react` package provides ready-to-use components and a hook for displaying data from an Inertia response. Under the hood it uses **TanStack Table** for rendering and the **Inertia Router** for navigation without page reloads.

## Installation

```bash
pnpm add @mdaushi/kinetics-react
```

Make sure the following peer dependencies are installed in your React/Inertia project:

```bash
pnpm add @inertiajs/react @tanstack/react-table
```

---

## Basic Usage: `<Table>` Component

The simplest approach. Pass the `table` prop received from Inertia directly to the `<Table>` component.

```tsx
// resources/js/Pages/Users/Index.tsx

import { Head } from '@inertiajs/react';
import { Table, TableProps } from '@mdaushi/kinetics-react';

interface User {
    id: number;
    name: string;
    email: string;
    status: string;
}

interface Props {
    table: TableProps<User>;
}

export default function UsersIndex({ table }: Props) {
    return (
        <>
            <Head title="Users" />

            <div className="p-6">
                <h1 className="text-2xl font-bold mb-4">Users</h1>

                <Table
                    table={table}
                    searchPlaceholder="Search by name or email..."
                />
            </div>
        </>
    );
}
```

The `<Table>` component automatically renders:

- **Toolbar** — global search input + filter dropdowns for `filterable` columns
- **Table** — column headers that can be clicked to toggle sorting
- **Pagination** — page navigation, total count info, and a rows-per-page selector
- All interactions (sort, search, filter, page change) trigger an Inertia request to the server without a page reload

### Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `table` | `TableProps<TData>` | — | Data from the Inertia prop (required) |
| `searchPlaceholder` | `string` | `"Search..."` | Placeholder text for the search input |

---

## Advanced Usage: `useTable` Hook

Use this hook when you need full control over the table's appearance — for example integrating into an existing layout, adding custom buttons, or building your own UI on top of the TanStack Table API.

```tsx
import { useTable, TableProps } from '@mdaushi/kinetics-react';

interface Props {
    table: TableProps<any>;
}

export default function CustomTablePage({ table: serverData }: Props) {
    const {
        tableInstance, // TanStack Table instance — use this to render rows and columns
        columns,       // Column definitions from the server
        meta,          // Pagination info: current_page, last_page, total, etc.
        search,        // The current active search string
        setSearch,     // Call this to update the search (debounced, triggers an Inertia request)
        setFilter,     // Call this to update a single filter: setFilter('status', 'active')
        filters,       // Object containing all currently active filters
    } = useTable({
        table: serverData,
        searchDebounce: 300, // Debounce delay for search in ms (default: 300)
    });

    return (
        <div>
            {/* Search input */}
            <input
                type="text"
                value={search}
                onChange={e => setSearch(e.target.value)}
                placeholder="Search..."
            />

            {/* Custom filter */}
            <select onChange={e => setFilter('status', e.target.value)}>
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            {/* Render the table using the TanStack Table API */}
            <table>
                <thead>
                    {tableInstance.getHeaderGroups().map(headerGroup => (
                        <tr key={headerGroup.id}>
                            {headerGroup.headers.map(header => (
                                <th
                                    key={header.id}
                                    onClick={header.column.getToggleSortingHandler()}
                                    style={{ cursor: header.column.getCanSort() ? 'pointer' : 'default' }}
                                >
                                    {header.isPlaceholder
                                        ? null
                                        : typeof header.column.columnDef.header === 'function'
                                            ? header.column.columnDef.header(header.getContext())
                                            : header.column.columnDef.header}
                                </th>
                            ))}
                        </tr>
                    ))}
                </thead>
                <tbody>
                    {tableInstance.getRowModel().rows.map(row => (
                        <tr key={row.id}>
                            {row.getVisibleCells().map(cell => (
                                <td key={cell.id}>
                                    {typeof cell.column.columnDef.cell === 'function'
                                        ? cell.column.columnDef.cell(cell.getContext())
                                        : String(cell.getValue() ?? '')}
                                </td>
                            ))}
                        </tr>
                    ))}
                </tbody>
            </table>

            {/* Pagination info */}
            <p>
                Page {meta.current_page} of {meta.last_page}
                &nbsp;({meta.total} total records)
            </p>

            {/* Pagination controls */}
            <button
                onClick={() => tableInstance.previousPage()}
                disabled={!tableInstance.getCanPreviousPage()}
            >
                Previous
            </button>
            <button
                onClick={() => tableInstance.nextPage()}
                disabled={!tableInstance.getCanNextPage()}
            >
                Next
            </button>
        </div>
    );
}
```

### `useTable` Return Value

| Property | Type | Description |
|---|---|---|
| `tableInstance` | `Table<TData>` | TanStack Table instance, use this to render the UI |
| `columns` | `TableColumn[]` | Column definitions from the server |
| `meta` | `TableMeta` | Pagination info from the server |
| `search` | `string` | The current search value (bind this to your input) |
| `setSearch(value)` | `Function` | Update search — automatically debounced and triggers an Inertia request |
| `setFilter(key, value)` | `Function` | Update a single filter — pass `null` or `""` to clear it |
| `filters` | `Record<string, unknown>` | All currently active filters |

---

## TypeScript Types

### `TableProps<TData>`

The type for the `table` prop received from Inertia (result of `.make()` in Laravel):

```ts
interface TableProps<TData> {
    data:    TData[];
    columns: TableColumn[];
    meta:    TableMeta;
    state:   TableState;
}
```

### `TableMeta`

```ts
interface TableMeta {
    current_page: number;
    last_page:    number;
    per_page:     number;
    total:        number;
    from:         number | null;
    to:           number | null;
}
```

### `TableState`

```ts
interface TableState {
    sort:      string | null;
    direction: 'asc' | 'desc';
    search:    string;
    filters:   Record<string, unknown>;
    per_page:  number;
}
```

### `TableColumn`

```ts
interface TableColumn {
    key:           string;
    label:         string;
    sortable:      boolean;
    searchable:    boolean;
    filterable:    boolean;
    filterOptions: string[] | Record<string, string>;
    visible:       boolean;
    type:          string;
}
```

---

## Custom URL

By default all Inertia requests are sent to `window.location.pathname` (the current page URL). Pass a `url` option to override this:

```tsx
const { tableInstance, ...rest } = useTable({
    table: serverData,
    url: route('users.index'), // or a static URL string
});
```
