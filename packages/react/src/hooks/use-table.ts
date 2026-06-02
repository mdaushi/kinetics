import { router } from "@inertiajs/react";
import {
  ColumnDef,
  getCoreRowModel,
  useReactTable,
  SortingState,
  PaginationState,
} from "@tanstack/react-table";
import React, { useCallback, useMemo, useState } from "react";
import { useDebouncedCallback } from "use-debounce";
import {
  ActionItem,
  TableColumn,
  TableMeta,
  TableState,
  TableController,
  TableProps,
} from "@mdaushi/kinetics-core";
import { ActionCell } from "../components/action-cell";
import { TableColumnHeader } from "../components/table-column-header";
import { Badge } from "../components/ui/badge";

export type { TableProps, TableColumn, TableMeta, TableState };

export interface UseTableOptions<TData> {
  table: TableProps<TData>;
  url?: string;
  searchDebounce?: number;
}

export function useTable<TData extends Record<string, unknown>>({
  table: serverData,
  url,
  searchDebounce = 300,
}: UseTableOptions<TData>) {
  const { data, columns: serverColumns, meta, state: serverState } = serverData;

  // Controller from core — all logic parameters are here
  const ctrl = useMemo(
    () => new TableController(serverState, meta),
    [serverState, meta, url],
  );

  const [search, setSearchLocal] = useState(serverState.search ?? "");

  const visit = useCallback(
    (params: Record<string, unknown>) => {
      router.get(url ?? window.location.pathname, params as any, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      });
    },
    [url],
  );

  // Handlers

  const handleSortChange = useCallback(
    (updater: SortingState | ((prev: SortingState) => SortingState)) => {
      const current = ctrl.toSorting();
      const next = typeof updater === "function" ? updater(current) : updater;
      visit(ctrl.resolveSort(next));
    },
    [ctrl, visit],
  );

  const handlePageChange = useCallback(
    (
      updater: PaginationState | ((prev: PaginationState) => PaginationState),
    ) => {
      const current = ctrl.toPagination();
      const next = typeof updater === "function" ? updater(current) : updater;
      visit(ctrl.resolvePagination(next));
    },
    [ctrl, visit],
  );

  const handleSearchChange = useDebouncedCallback((value: string) => {
    visit(ctrl.resolveSearchParams(value));
  }, searchDebounce);

  const handleFilterChange = useCallback(
    (key: string, value: unknown) => {
      visit(ctrl.resolveFilterParams(key, value));
    },
    [ctrl, visit],
  );

  // Build TanStack column defs from server column definitions

  const columnDefs = useMemo<ColumnDef<TData>[]>(() => {
    return serverColumns
      .filter((col) => col.visible)
      .map((col): ColumnDef<TData> => {
        // Action column — auto-inject ActionCell
        if (col.type === "actions") {
          return {
            id: col.key,
            accessorKey: col.key,
            header: col.label,
            enableSorting: false,
            cell: ({ getValue }) =>
              React.createElement(ActionCell, {
                actions: (getValue() as ActionItem[]) ?? [],
              }),
          };
        }

        return {
          id: col.key,
          accessorKey: col.key,
          header: ({ column }) =>
            React.createElement(TableColumnHeader, {
              column: column,
              title: col.label,
            } as any),
          enableSorting: col.sortable,
          enableColumnFilter: col.filterable,
          cell: ({ getValue }) => {
            const value = getValue();
            if (col.type === "badge") {
              const colorMeta = col.meta?.color;
              let finalColor = "default";

              if (typeof colorMeta === "string") {
                finalColor = colorMeta;
              } else if (typeof colorMeta === "object" && colorMeta !== null) {
                finalColor =
                  (colorMeta as Record<string, string>)[String(value)] ??
                  "default";
              }

              return React.createElement(
                Badge,
                {
                  variant: finalColor as React.ComponentProps<
                    typeof Badge
                  >["variant"],
                },
                String(value),
              );
            }

            const formatted = String(value ?? "—");

            return formatted;
          },
        };
      });
  }, [serverColumns]);

  // TanStack Table instance

  const tableInstance = useReactTable<TData>({
    data,
    columns: columnDefs,
    state: {
      sorting: ctrl.toSorting(),
      pagination: ctrl.toPagination(),
    },
    manualSorting: true,
    manualPagination: true,
    manualFiltering: true,
    pageCount: meta.last_page,
    onSortingChange: handleSortChange,
    onPaginationChange: handlePageChange,
    getCoreRowModel: getCoreRowModel(),
  });

  return {
    tableInstance,
    columns: serverColumns,
    meta,
    search,
    setSearch: (value: string) => {
      setSearchLocal(value);
      handleSearchChange(value);
    },
    setFilter: handleFilterChange,
    filters: serverState.filters,
  };
}
