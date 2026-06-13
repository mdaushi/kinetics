import { TableFilter } from "@mdaushi/kinetics-core";

export interface FilterValue {
  operator: string;
  value: any;
}

export interface TableFiltersProps {
  filters: TableFilter[];
  activeFilters: Record<string, any>;
  setFilter: (key: string, value: any) => void;
  debounce?: number;

  draftFilters?: string[];
  onAddDraft?: (name: string) => void;
  onRemoveDraft?: (name: string) => void;
}
