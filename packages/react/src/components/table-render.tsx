import { flexRender, Table as TableInstance } from "@tanstack/react-table";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "./ui/table";
import { TableSelectionBar } from "./table-selection-bar";

interface RenderTableProps<TData> {
  table: TableInstance<TData>;
  rowSelection?: Record<string, boolean>;
  selectAllPages?: boolean;
  setSelectAllPages?: (val: boolean) => void;
  totalRecords?: number;
}

export function RenderTable<TData>({
  table,
  rowSelection = {},
  selectAllPages = false,
  setSelectAllPages,
  totalRecords = 0,
}: RenderTableProps<TData>) {
  return (
    <div className="overflow-hidden rounded-md border">
      <TableSelectionBar
        table={table}
        rowSelection={rowSelection}
        selectAllPages={selectAllPages}
        setSelectAllPages={setSelectAllPages ?? (() => {})}
        totalRecords={totalRecords}
      />

      <Table>
        <TableHeader className="sticky top-0 z-10 bg-muted">
          {table.getHeaderGroups().map((headerGroup) => (
            <TableRow key={headerGroup.id}>
              {headerGroup.headers.map((header) => {
                return (
                  <TableHead key={header.id} colSpan={header.colSpan}>
                    {header.isPlaceholder
                      ? null
                      : flexRender(
                          header.column.columnDef.header,
                          header.getContext(),
                        )}
                  </TableHead>
                );
              })}
            </TableRow>
          ))}
        </TableHeader>
        <TableBody>
          {table.getRowModel().rows?.length ? (
            table.getRowModel().rows.map((row) => (
              <TableRow
                key={row.id}
                data-state={row.getIsSelected() && "selected"}
              >
                {row.getVisibleCells().map((cell) => (
                  <TableCell key={cell.id}>
                    {flexRender(cell.column.columnDef.cell, cell.getContext())}
                  </TableCell>
                ))}
              </TableRow>
            ))
          ) : (
            <TableRow>
              <TableCell
                colSpan={table.getAllColumns().length}
                className="h-24 text-center"
              >
                No results.
              </TableCell>
            </TableRow>
          )}
        </TableBody>
      </Table>
    </div>
  );
}
