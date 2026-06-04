import { TableColumn } from "@mdaushi/kinetics-core";
import { X } from "lucide-react";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "./ui/select";
import { Button } from "./ui/button";
import { Input } from "./ui/input";

interface TableToolbarProps {
  search: string;
  columns: TableColumn[];
  setSearch: (val: string) => void;
  placeholder: string;
  filters: Record<string, unknown>;
  setFilter: (key: string, value: unknown) => void;
  reset: () => void;
}
export default function TableToolbar({
  search,
  columns,
  setSearch,
  placeholder,
  filters,
  setFilter,
  reset,
}: TableToolbarProps) {
  const filterableColumns = columns.filter((c) => c.filterable);

  const hasSearch = columns.some((c) => c.searchable);

  const hasFilter = search || Object.keys(filters).length > 0;

  return (
    <div className="flex items-center justify-between">
      <div className="flex flex-1 items-center gap-2">
        {hasSearch && (
          <Input
            placeholder={placeholder}
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="h-8 w-37.5 lg:w-62.5"
          />
        )}

        {filterableColumns.map((col) => {
          const options = Array.isArray(col.filterOptions)
            ? Object.fromEntries(col.filterOptions.map((o) => [o, o]))
            : col.filterOptions;

          return (
            <Select
              items={options}
              value={(filters[col.key] as string) ?? ""}
              onValueChange={(e) => setFilter(col.key, e || null)}
            >
              <SelectTrigger>
                <SelectValue placeholder={col.label} />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectItem value="">All {col.label}</SelectItem>
                  {Object.entries(options).map(([value, label]) => (
                    <SelectItem key={value} value={value}>
                      {label}
                    </SelectItem>
                  ))}
                </SelectGroup>
              </SelectContent>
            </Select>
          );
        })}

        {hasFilter && (
          <Button variant="ghost" onClick={reset}>
            Reset
            <X />
          </Button>
        )}
      </div>
    </div>
  );
}
