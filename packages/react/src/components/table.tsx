import { useTable } from "../hooks/use-table";
import { TablePagination } from "./table-pagination";
import { TableToolbar } from "./table-toolbar";
import { RenderTable } from "./table-render";
import { TableBulkActionBar } from "./table-bulk-action-bar";

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
    bulkActions,
    rowSelection,
    selectAllPages,
    setSelectAllPages,
    reset,
  } = useTable<TData>(table);

  const toolbarBulkActions = bulkActions.filter(
    (a) => a.position === "toolbar",
  );
  const floatingBulkActions = bulkActions.filter(
    (a) => a.position === "floating",
  );

  function handleClearSelection() {
    tableInstance.resetRowSelection();
    setSelectAllPages(false);
  }

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
        bulkActions={toolbarBulkActions}
        rowSelection={rowSelection}
        selectAllPages={selectAllPages}
      />
      <RenderTable
        table={tableInstance}
        rowSelection={rowSelection}
        selectAllPages={selectAllPages}
        setSelectAllPages={setSelectAllPages}
        totalRecords={meta.total}
      />

      <TablePagination meta={meta} table={tableInstance} />

      {/* Floating bulk action bar — fixed bottom center viewport */}
      <TableBulkActionBar
        actions={floatingBulkActions}
        rowSelection={rowSelection}
        selectAllPages={selectAllPages}
        onClear={handleClearSelection}
      />
    </div>
  );
}
