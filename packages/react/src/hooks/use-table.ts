import { getCoreRowModel, useReactTable } from "@tanstack/react-table";
import {
  TableColumn,
  TableMeta,
  TableState,
  TableProps,
} from "@mdaushi/kinetics-core";
import { useTableProps } from "./use-table-props";
import { useTableColumns } from "./use-table-columns";
import { useTableNavigation } from "./use-table-navigation";
import { useTableFilters } from "./use-table-filters";
import { useTableRouter } from "./use-table-router";

export type { TableProps, TableColumn, TableMeta, TableState };

export interface UseTableOptions {
  url?: string;
}

export {
  useTableProps,
  useTableColumns,
  useTableNavigation,
  useTableFilters,
  useTableRouter,
};

export function useTable<TData extends Record<string, unknown>>(
  propName: string = "table",
  options?: UseTableOptions,
) {
  const url = options?.url;

  const {
    data,
    meta,
    actions: serverActions = [],
  } = useTableProps<TData>(propName, url);

  const { columnDefs, serverColumns } = useTableColumns<TData>(propName, url);
  const nav = useTableNavigation<TData>(propName, url);
  const filterParams = useTableFilters<TData>(propName, url);

  // TanStack Table instance
  const tableInstance = useReactTable<TData>({
    data,
    columns: columnDefs,
    state: {
      sorting: nav.ctrl.toSorting(),
      pagination: nav.ctrl.toPagination(),
    },
    manualSorting: true,
    manualPagination: true,
    manualFiltering: true,
    pageCount: meta.last_page,
    onSortingChange: nav.handleSortChange,
    onPaginationChange: nav.handlePageChange,
    getCoreRowModel: getCoreRowModel(),
  });

  return {
    tableInstance,
    columns: serverColumns,
    meta,
    search: filterParams.search,
    setSearch: filterParams.setSearch,
    setFilter: filterParams.setFilter,
    filters: filterParams.filters,
    filtersConfig: filterParams.filtersConfig,
    actions: serverActions,
    reset: filterParams.reset,
  };
}
