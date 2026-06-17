---
title: Pagination
description: Automatic data pagination using PaginatePipe.
---

Pagination in Kinetics is handled as the final stage in the pipeline flow. After the query is filtered, searched, and sorted, `PaginatePipe` will execute the query using Eloquent's built-in pagination methods.

## How PaginatePipe Works

No additional configuration is required on your part. `PaginatePipe` is included by default in every `Table` class pipeline.

```php
// Pagination is already running automatically behind the scenes.
$table = Table::model(User::class)->columns([...])->make();
```

`PaginatePipe` will:
1. Read the current request parameters (e.g., `?page=2`).
2. Add `->paginate()` (or a compatible method) to the query builder.
3. Embed pagination metadata (such as total rows, number of pages, and previous/next page links) into the final JSON Response.

On the frontend side, the `<Table />` component will automatically read this metadata and render the pagination navigation controls below the table.
