import { TableFiltersProps } from "./types";
import { FilterPill } from "./filter-pill";

export function ActiveFilterPills({
  filters = [],
  activeFilters = {},
  setFilter,
  debounce,
  draftFilters = [],
  onRemoveDraft,
}: TableFiltersProps) {
  const inactiveFilters = filters.filter((f) => {
    const val = activeFilters[f.name];
    const isDraft = draftFilters.includes(f.name);
    return !isDraft && (val === undefined || val === null);
  });

  const activeFilterDefs = filters.filter((f) => !inactiveFilters.includes(f));

  const handleRemoveFilter = (filterName: string) => {
    setFilter(filterName, null);
    if (onRemoveDraft) onRemoveDraft(filterName);
  };

  if (activeFilterDefs.length === 0) return null;

  return (
    <div className="flex flex-wrap items-center gap-2">
      {activeFilterDefs.map((def) => {
        const isDraft = draftFilters.includes(def.name);
        return (
          <FilterPill
            key={def.name}
            definition={def}
            currentValue={activeFilters[def.name]}
            onChange={(val) => setFilter(def.name, val)}
            onRemove={() => handleRemoveFilter(def.name)}
            defaultOpen={isDraft}
            debounce={debounce}
          />
        );
      })}
    </div>
  );
}
