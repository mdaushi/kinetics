import { useCallback, useEffect, useState } from "react";
import { useDebouncedCallback } from "use-debounce";
import { useTableProps } from "./use-table-props";
import { useTableRouter } from "./use-table-router";

export function useTableFilters<TData extends Record<string, unknown>>(
  table: string,
  url?: string,
) {
  const {
    state: serverState,
    meta,
    filters: serverFilters = [],
  } = useTableProps<TData>(table, url);
  const { ctrl, visit } = useTableRouter<TData>(table, url);

  const [search, setSearchLocal] = useState(serverState.search ?? "");

  useEffect(() => {
    setSearchLocal(serverState.search ?? "");
  }, [serverState.search]);

  const handleFilterChange = useCallback(
    (key: string, value: unknown) => {
      visit(ctrl.resolveFilterParams(key, value));
    },
    [ctrl, visit],
  );

  const handleSearchChange = useDebouncedCallback((value: string) => {
    visit(ctrl.resolveSearchParams(value));
  }, meta.debounce);

  const handleReset = useCallback(() => {
    setSearchLocal("");
    visit(ctrl.resolveReset());
  }, [ctrl, visit]);

  const setSearch = useCallback(
    (value: string) => {
      setSearchLocal(value);
      handleSearchChange(value);
    },
    [handleSearchChange],
  );

  return {
    filters: serverState.filters,
    filtersConfig: serverFilters,
    setFilter: handleFilterChange,
    search,
    setSearch,
    reset: handleReset,
  };
}
