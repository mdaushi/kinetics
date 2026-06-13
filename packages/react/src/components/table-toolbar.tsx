import * as React from "react";
import { TableColumn, TableFilter } from "@mdaushi/kinetics-core";
import { X } from "lucide-react";

import { Input } from "./ui/input";
import { Button } from "./ui/button";
import { AddFilterDropdown, ActiveFilterPills } from "./table-filters";

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
}
export default function TableToolbar({
  search,
  columns,
  setSearch,
  placeholder,
  filters,
  filtersConfig,
  setFilter,
  reset,
  debounce,
}: TableToolbarProps) {
  const [draftFilters, setDraftFilters] = React.useState<string[]>([]);

  const hasSearch = columns.some((c) => c.searchable);

  const hasFilter = search || Object.keys(filters).length > 0;

  return (
    <div className="flex flex-col gap-3">
      <div className="flex items-center justify-between">
        <div className="flex flex-1 items-center gap-2">
          {hasSearch && (
            <Input
              placeholder={placeholder}
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="h-8 w-[150px] lg:w-[250px]"
            />
          )}

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
    </div>
  );
}
