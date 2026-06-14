import { useCallback } from "react";
import { SortingState, PaginationState } from "@tanstack/react-table";
import { useTableRouter } from "./use-table-router";

export function useTableNavigation<TData extends Record<string, unknown>>(
  table: string,
  url?: string,
) {
  const { ctrl, visit } = useTableRouter<TData>(table, url);

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

  return {
    ctrl,
    handleSortChange,
    handlePageChange,
  };
}
