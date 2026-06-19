import { getCoreRowModel, useReactTable } from "@tanstack/react-table";
import {
  TableColumn,
  TableMeta,
  TableState,
  TableProps,
  ToolbarActionItem,
  BulkActionItem,
  ActionItem,
} from "@mdaushi/kinetics-core";
import { useTableProps } from "./use-table-props";
import { useTableColumns } from "./use-table-columns";
import { useTableNavigation } from "./use-table-navigation";
import { useTableFilters } from "./use-table-filters";
import { useTableRouter } from "./use-table-router";
import { useEffect, useRef, useState } from "react";

export type {
  TableProps,
  TableColumn,
  TableMeta,
  TableState,
  ToolbarActionItem,
  BulkActionItem,
  ActionItem,
};

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
    bulk_actions: serverBulkActions = [],
  } = useTableProps<TData>(propName);

  const { columnDefs, serverColumns } = useTableColumns<TData>(propName);
  const nav = useTableNavigation<TData>(propName, url);
  const filterParams = useTableFilters<TData>(propName, url);

  const [rowSelection, setRowSelection] = useState<Record<string, boolean>>({});
  const [selectAllPages, setSelectAllPages] = useState(false);

  // Ref to distinguish selection changes from the user vs from internal auto-select
  const isAutoSelectingRef = useRef(false);

  // Reset selectAllPages only when the user changes the selection (not auto-select)
  function handleRowSelectionChange(updater: any) {
    if (!isAutoSelectingRef.current) {
      setSelectAllPages(false);
    }
    setRowSelection(updater);
  }

  // When selectAllPages=true and data changes (moves page),
  // auto-select all rows on the newly loaded page
  useEffect(() => {
    if (!selectAllPages || data.length === 0) return;

    isAutoSelectingRef.current = true;
    setRowSelection((prev) => {
      const next = { ...prev };
      data.forEach((row) => {
        const id = String(row.id ?? row.uuid ?? row.key);
        next[id] = true;
      });
      return next;
    });
    // Reset the flag after React processes the state update
    Promise.resolve().then(() => {
      isAutoSelectingRef.current = false;
    });
  }, [data, selectAllPages]);

  // TanStack Table instance
  const tableInstance = useReactTable<TData>({
    data,
    columns: columnDefs,
    state: {
      sorting: nav.ctrl.toSorting(),
      pagination: nav.ctrl.toPagination(),
      rowSelection,
    },
    manualSorting: true,
    manualPagination: true,
    manualFiltering: true,
    pageCount: meta.last_page,
    onSortingChange: nav.handleSortChange,
    onPaginationChange: nav.handlePageChange,
    onRowSelectionChange: handleRowSelectionChange,
    getRowId: (row) => String(row.id ?? row.uuid ?? row.key),
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
    bulkActions: serverBulkActions,
    rowSelection,
    setRowSelection,
    selectAllPages,
    setSelectAllPages,
    reset: filterParams.reset,
  };
}
