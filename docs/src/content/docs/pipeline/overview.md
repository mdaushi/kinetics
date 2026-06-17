---
title: Pipeline Overview
description: Understanding the pipeline architecture concept in Kinetics.
---

One of the main advantages of Kinetics is its Pipeline-based architecture. Instead of executing monolithic queries to the database, the data manipulation process is broken down into several independent stages (pipes).

## Basic Concept

When you call the `make()` method on the `Table` class, the data (an Eloquent Builder) will flow through a series of pipes. Each pipe is responsible for one specific task:

```text
Request 
  ↓
[SortPipe]      -> Adds order_by to the query if there is a sorting request
  ↓
[SearchPipe]    -> Adds where/orWhere if there is a search keyword
  ↓
[FilterPipe]    -> Adds specific conditions based on active filters
  ↓
[PaginatePipe]  -> Executes the query and paginates
  ↓
TableResult (JSON)
```

## Advantages

1. **Modularity**: Each feature is isolated. The search logic will not interfere with the pagination logic.
2. **Customizability**: You can change the order of pipes, remove built-in pipes, or insert your own custom pipes in the middle of the flow.
3. **Predictable Performance**: Because all data manipulations occur at the database level through Eloquent, you avoid performance issues (such as pulling all data into RAM for processing).

On the next pages, we will discuss each built-in pipe provided by Kinetics in detail.
