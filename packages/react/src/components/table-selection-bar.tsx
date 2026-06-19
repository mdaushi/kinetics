import { Table as TableInstance } from "@tanstack/react-table";
import { CheckSquare2Icon, XIcon } from "lucide-react";
import { Button } from "./ui/button";

export interface TableSelectionBarProps<TData> {
  table: TableInstance<TData>;
  rowSelection: Record<string, boolean>;
  selectAllPages: boolean;
  setSelectAllPages: (val: boolean) => void;
  totalRecords: number;
}

export function TableSelectionBar<TData>({
  table,
  rowSelection,
  selectAllPages,
  setSelectAllPages,
  totalRecords,
}: TableSelectionBarProps<TData>) {
  const selectedCount = Object.keys(rowSelection).length;

  if (selectedCount === 0 && !selectAllPages) return null;

  function handleClearAll() {
    table.resetRowSelection();
    setSelectAllPages(false);
  }

  return (
    <div className="flex items-center justify-between gap-3 border-b bg-primary/5 px-3 py-2 text-sm animate-in fade-in slide-in-from-top-1 duration-200">
      <div className="flex items-center gap-2 text-primary">
        {selectAllPages ? (
          <span>
            All <strong>{totalRecords}</strong> records selected
          </span>
        ) : (
          <span>
            <strong>{selectedCount}</strong>{" "}
            {selectedCount === 1 ? "row" : "rows"} selected
          </span>
        )}
      </div>

      <div className="flex items-center gap-1.5">
        {!selectAllPages && (
          <>
            <Button
              size="sm"
              variant="ghost"
              className="text-xs text-primary hover:bg-primary/10 hover:text-primary"
              onClick={() => setSelectAllPages(true)}
            >
              Select all
              <span className="hidden sm:inline-block">
                {totalRecords} records
              </span>
            </Button>
          </>
        )}

        <Button
          size="sm"
          variant="ghost"
          className="text-xs text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
          onClick={handleClearAll}
        >
          <XIcon />
          Clear <span className="hidden sm:inline-block">selection</span>
        </Button>
      </div>
    </div>
  );
}
