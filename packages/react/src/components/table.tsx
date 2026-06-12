import { TableProps, useTable } from "../hooks/use-table";
import TablePagination from "./table-pagination";
import TableToolbar from "./table-toolbar";
import RenderTable from "./table-render";

interface TableComponentProps<TData extends Record<string, unknown>> {
  table: TableProps<TData>;
  searchPlaceholder?: string;
}

export function Table<TData extends Record<string, unknown>>({
  table: serverData,
  searchPlaceholder = "Search...",
}: TableComponentProps<TData>) {
  const {
    tableInstance,
    columns: serverColumns,
    meta,
    search,
    setSearch,
    setFilter,
    filters,
    filtersConfig,
    reset,
  } = useTable({ table: serverData });

  return (
    <div className="space-y-4">
      <TableToolbar
        search={search}
        setSearch={setSearch}
        placeholder={searchPlaceholder}
        columns={serverColumns}
        filters={filters}
        filtersConfig={filtersConfig}
        setFilter={setFilter}
        reset={reset}
      />
      <RenderTable table={tableInstance} />

      <TablePagination meta={meta} table={tableInstance} />
    </div>
  );
}
