import * as React from "react";
import { Plus, Search } from "lucide-react";
import { Button } from "../ui/button";
import { Popover, PopoverContent, PopoverTrigger } from "../ui/popover";
import { TableFiltersProps } from "./types";

export function AddFilterDropdown({
  filters = [],
  activeFilters = {},
  setFilter,
  draftFilters = [],
  onAddDraft,
}: Omit<TableFiltersProps, "resetFilters">) {
  const [searchFilter, setSearchFilter] = React.useState("");
  const [isOpen, setIsOpen] = React.useState(false);
  const [selectedIndex, setSelectedIndex] = React.useState(0);

  const inactiveFilters = filters.filter((f) => {
    const val = activeFilters[f.name];
    const isDraft = draftFilters.includes(f.name);
    return !isDraft && (val === undefined || val === null);
  });

  const filteredInactive = inactiveFilters.filter((f) =>
    f.label.toLowerCase().includes(searchFilter.toLowerCase()),
  );

  React.useEffect(() => {
    setSelectedIndex(0);
  }, [searchFilter, isOpen]);

  const handleAddFilter = (filterName: string) => {
    const def = filters.find((f) => f.name === filterName);
    if (!def) return;

    if (onAddDraft) {
      onAddDraft(filterName);
    } else {
      setFilter(filterName, {
        operator: def.operators?.[0]?.value || "is",
        value: def.type === "select" ? [] : "",
      });
    }
  };

  const handleOpenChange = (open: boolean) => {
    setIsOpen(open);
    if (!open) setSearchFilter("");
  };

  const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
    if (filteredInactive.length === 0) return;

    switch (e.key) {
      case "ArrowDown":
        e.preventDefault();
        setSelectedIndex((prev) =>
          prev < filteredInactive.length - 1 ? prev + 1 : prev,
        );
        break;
      case "ArrowUp":
        e.preventDefault();
        setSelectedIndex((prev) => (prev > 0 ? prev - 1 : 0));
        break;
      case "Enter":
        e.preventDefault();
        const selected = filteredInactive[selectedIndex];
        if (selected) {
          handleAddFilter(selected.name);
          setIsOpen(false);
        }
        break;
    }
  };

  if (inactiveFilters.length === 0) return null;

  return (
    <Popover open={isOpen} onOpenChange={handleOpenChange}>
      <PopoverTrigger
        render={
          <Button variant="outline" size="sm" className="border-dashed">
            <Plus />
            Add filter
          </Button>
        }
      />
      <PopoverContent align="start" className="w-[200px] p-0 gap-0">
        <div className="flex items-center border-b px-3">
          <Search className="mr-2 h-4 w-4 shrink-0 opacity-50" />
          <input
            className="flex h-9 w-full rounded-md bg-transparent py-3 text-sm outline-none placeholder:text-muted-foreground disabled:cursor-not-allowed disabled:opacity-50"
            placeholder="Filter by..."
            value={searchFilter}
            onChange={(e) => setSearchFilter(e.target.value)}
            onKeyDown={handleKeyDown}
            autoFocus
          />
        </div>
        <div className="max-h-[300px] overflow-y-auto p-1 flex flex-col gap-0.5">
          {filteredInactive.length === 0 ? (
            <div className="py-6 text-center text-sm text-muted-foreground">
              No filters found.
            </div>
          ) : (
            filteredInactive.map((f, index) => (
              <button
                type="button"
                key={f.name}
                className={`relative flex w-full cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors text-left ${
                  selectedIndex === index
                    ? "bg-accent text-accent-foreground"
                    : "hover:bg-accent hover:text-accent-foreground"
                }`}
                onMouseEnter={() => setSelectedIndex(index)}
                onClick={() => {
                  handleAddFilter(f.name);
                  setIsOpen(false);
                }}
              >
                {f.label}
              </button>
            ))
          )}
        </div>
      </PopoverContent>
    </Popover>
  );
}
