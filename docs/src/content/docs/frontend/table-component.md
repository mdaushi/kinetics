---
title: Table Component
description: The main component for rendering tables on the user interface (Frontend).
---

One of the main advantages of Kinetics is its *"Zero-boilerplate Frontend"* philosophy. All data processing logic happens on the *server* side, so your *frontend* only needs to display the results.

For this purpose, Kinetics provides a React component called `<Table />`.

## Component Usage

Import the `<Table />` component from the Kinetics React package and pass the **name of the Inertia prop** (as a string) that contains the table data. You don't even need to extract the data from your page props!

```tsx
import { Table } from "@mdaushi/kinetics-react";

export default function MyPage() {
  return (
    <div className="p-4">
      {/* "tableData" is the key from your Laravel controller -> Inertia::render */}
      <Table table="tableData" />
    </div>
  );
}
```

## What Does the `<Table />` Component Handle?

This component will parse the received `tableData` and automatically:
1. Render the columns and header labels (including clickability for sorting).
2. Render the search bar (if at least one column is searchable).
3. Render the table data rows.
4. Automatically display badges if configured via column configuration (e.g., `TextColumn::make()->badge()`).
5. Render Actions buttons and Dropdown Menu groups for Action Groups on each row.
6. Display the Pagination navigation bar at the bottom.

All states (such as the active page or current search query) are automatically synchronized into the URL via Inertia.js URL update features (Inertia's built-in `router.get` or `router.visit` methods). This ensures that every page and search state can be directly shared or bookmarked.
