import { useTable } from "../hooks/use-table";
import { TablePagination } from "./table-pagination";
import { TableToolbar } from "./table-toolbar";
import { RenderTable } from "./table-render";

interface TableComponentProps {
  table: string;
  searchPlaceholder?: string;
}

export function Table<TData extends Record<string, unknown>>({
  table,
  searchPlaceholder = "Search...",
}: TableComponentProps) {
  const {
    tableInstance,
    columns: serverColumns,
    meta,
    search,
    setSearch,
    setFilter,
    filters,
    filtersConfig,
    actions,
    reset,
  } = useTable<TData>(table);

  return (
    <div className="space-y-3">
      <TableToolbar
        search={search}
        setSearch={setSearch}
        placeholder={searchPlaceholder}
        columns={serverColumns}
        filters={filters}
        filtersConfig={filtersConfig}
        setFilter={setFilter}
        reset={reset}
        debounce={meta.debounce}
        actions={actions}
      />
      <RenderTable table={tableInstance} />

      <TablePagination meta={meta} table={tableInstance} />
    </div>
  );
}
