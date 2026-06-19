import * as React from "react";
import {
  TableColumn,
  TableFilter,
  ToolbarActionItem,
  BulkActionItem,
} from "@mdaushi/kinetics-core";
import { X } from "lucide-react";

import { Input } from "./ui/input";
import { Button } from "./ui/button";
import { AddFilterDropdown, ActiveFilterPills } from "./table-filters";
import { ActionCell } from "./action-cell";

interface TableToolbarProps {
  search: string;
  columns: TableColumn[];
  setSearch: (val: string) => void;
  placeholder: string;
  filters: Record<string, unknown>;
  filtersConfig: TableFilter[];
  setFilter: (key: string, value: unknown) => void;
  reset: () => void;
  debounce?: number;
  actions?: ToolbarActionItem[];
  bulkActions?: BulkActionItem[];
  rowSelection?: Record<string, boolean>;
  selectAllPages?: boolean;
}
export function TableToolbar({
  search,
  columns,
  setSearch,
  placeholder,
  filters,
  filtersConfig,
  setFilter,
  reset,
  debounce,
  actions = [],
  bulkActions = [],
  rowSelection = {},
  selectAllPages = false,
}: TableToolbarProps) {
  const [draftFilters, setDraftFilters] = React.useState<string[]>([]);

  const hasSearch = columns.some((c) => c.searchable);
  const hasFilter = search || Object.keys(filters).length > 0;
  const hasSelection = Object.keys(rowSelection).length > 0 || selectAllPages;

  return (
    <div className="flex flex-col gap-3">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div className="flex flex-col sm:flex-row items-start sm:items-center gap-2 w-full sm:w-auto flex-1">
          {hasSearch && (
            <Input
              placeholder={placeholder}
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="h-8 w-full sm:w-[150px] lg:w-[250px]"
            />
          )}

          <div className="flex items-center gap-2">
            <AddFilterDropdown
              filters={filtersConfig}
              activeFilters={filters}
              draftFilters={draftFilters}
              onAddDraft={(name) => setDraftFilters((prev) => [...prev, name])}
              setFilter={setFilter}
            />

            {hasFilter && (
              <Button
                variant="ghost"
                onClick={() => {
                  setDraftFilters([]);
                  reset();
                }}
                className="h-8 px-2 lg:px-3"
              >
                Reset
                <X className="ml-2 h-4 w-4" />
              </Button>
            )}
          </div>
        </div>

        {/* Regular actions */}
        {actions.length > 0 && !hasSelection && (
          <div className="hidden md:flex flex-wrap items-center gap-2 justify-end shrink-0">
            <ActionCell actions={actions} />
          </div>
        )}

        {/* Toolbar bulk actions */}
        {bulkActions.length > 0 && hasSelection && (
          <div className="hidden md:flex flex-wrap items-center gap-2 justify-end shrink-0">
            <ActionCell actions={bulkActions} />
          </div>
        )}
      </div>

      {(hasFilter || draftFilters.length > 0) && (
        <ActiveFilterPills
          filters={filtersConfig}
          activeFilters={filters}
          draftFilters={draftFilters}
          onRemoveDraft={(name) =>
            setDraftFilters((prev) => prev.filter((n) => n !== name))
          }
          setFilter={setFilter}
          debounce={debounce}
        />
      )}

      {actions.length > 0 && (
        <div className="flex md:hidden flex-wrap items-center gap-2 justify-end mt-1 w-full">
          <ActionCell actions={actions} />
        </div>
      )}
    </div>
  );
}
