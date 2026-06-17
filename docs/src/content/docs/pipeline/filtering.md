---
title: Filtering
description: Applying specific filters to table data.
---

Filtering is the process of narrowing down data based on specific criteria (such as categories, statuses, date ranges) that is more than just text searching. `FilterPipe` is responsible for executing this logic.

## Built-in and Custom Filters

Currently, Kinetics is designed to allow you to define your own filter logic with great flexibility. Instead of restricting you to specific filter types, the filtering feature is generally implemented by adding custom filter configurations to the table or injecting them directly via the base query builder.

*(Detailed documentation regarding advanced custom filter configuration will be added soon).*

## Difference between Filter and Search

- **Search**: Searches text across multiple columns simultaneously using the `LIKE` operation. Usually involves just a simple input box in the UI.
- **Filter**: Matches exact values (e.g., `status = 'published'`), or uses ranges (e.g., `created_at > '2023-01-01'`). Usually uses specific UI elements like dropdowns, checkboxes, or date pickers.
