import { TableMeta, TableState } from "./types";
export interface TableControllerOptions {
    url?: string;
}
export declare class TableController {
    private readonly state;
    private readonly meta;
    constructor(state: TableState, meta: TableMeta);
    baseParams(): Record<string, unknown>;
    /**
     * Receive TanStack SortingState directly.
     */
    resolveSort(sorting: Array<{
        id: string;
        desc: boolean;
    }>): Record<string, unknown>;
    resolveSearchParams(search: string): Record<string, unknown>;
    resolveFilterParams(key: string, value: unknown): Record<string, unknown>;
    resolvePageParams(pageIndex: number, pageSize: number): Record<string, unknown>;
    /**
     * Receive TanStack PaginationState directly.
     */
    resolvePagination(pagination: {
        pageIndex: number;
        pageSize: number;
    }): Record<string, unknown>;
    /** TanStack SortingState from current server state */
    toSorting(): Array<{
        id: string;
        desc: boolean;
    }>;
    /** TanStack PaginationState from current server state */
    toPagination(): {
        pageIndex: number;
        pageSize: number;
    };
}
