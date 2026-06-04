// All the logic for constructing query params — no dependencies on React,
// Vue, or any other framework. The adapter (React/Vue) simply calls this method
// and passes the results to its respective router.

import { TableMeta, TableState } from "./types";

export interface TableControllerOptions {
  url?: string;
}

export class TableController {
  constructor(
    private readonly state: TableState,
    private readonly meta: TableMeta,
    // private readonly options: TableControllerOptions = {},
  ) {}

  // Base params from current state

  baseParams(): Record<string, unknown> {
    return {
      sort: this.state.sort,
      direction: this.state.direction,
      search: this.state.search,
      filters: this.state.filters,
      per_page: this.state.per_page,
    };
  }

  // Sort

  // resolveSortParams(columnId: string | null): Record<string, unknown> {
  //   if (!columnId) {
  //     return { ...this.baseParams(), sort: null, direction: "asc", page: 1 };
  //   }

  //   const isSameColumn = this.state.sort === columnId;
  //   const direction =
  //     isSameColumn && this.state.direction === "asc" ? "desc" : "asc";

  //   return {
  //     ...this.baseParams(),
  //     sort: columnId,
  //     direction,
  //     page: 1,
  //   };
  // }

  /**
   * Receive TanStack SortingState directly.
   */
  resolveSort(
    sorting: Array<{ id: string; desc: boolean }>,
  ): Record<string, unknown> {
    const col = sorting[0] ?? null;

    return {
      ...this.baseParams(),
      sort: col?.id ?? null,
      direction: col?.desc ? "desc" : "asc",
      page: 1,
    };
  }

  // Search

  resolveSearchParams(search: string): Record<string, unknown> {
    return {
      ...this.baseParams(),
      search,
      page: 1,
    };
  }

  // Filter

  resolveFilterParams(key: string, value: unknown): Record<string, unknown> {
    const filters = { ...this.state.filters };

    if (value === null || value === "" || value === undefined) {
      delete filters[key];
    } else {
      filters[key] = value;
    }

    return {
      ...this.baseParams(),
      filters,
      page: 1,
    };
  }

  // Reset

  resolveReset(): Record<string, unknown> {
    return {
      sort: this.state.sort,
      direction: this.state.direction,
      per_page: this.state.per_page,
      search: null,
      filters: {},
      page: 1,
    };
  }

  // Pagination

  resolvePageParams(
    pageIndex: number,
    pageSize: number,
  ): Record<string, unknown> {
    return {
      ...this.baseParams(),
      page: pageIndex + 1, // TanStack 0-based -> Laravel 1-based
      per_page: pageSize,
    };
  }

  /**
   * Receive TanStack PaginationState directly.
   */
  resolvePagination(pagination: {
    pageIndex: number;
    pageSize: number;
  }): Record<string, unknown> {
    return this.resolvePageParams(pagination.pageIndex, pagination.pageSize);
  }

  // Helpers

  /** TanStack SortingState from current server state */
  toSorting(): Array<{ id: string; desc: boolean }> {
    if (!this.state.sort) return [];
    return [{ id: this.state.sort, desc: this.state.direction === "desc" }];
  }

  /** TanStack PaginationState from current server state */
  toPagination(): { pageIndex: number; pageSize: number } {
    return {
      pageIndex: this.meta.current_page - 1,
      pageSize: this.meta.per_page,
    };
  }
}
