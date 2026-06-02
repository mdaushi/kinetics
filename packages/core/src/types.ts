// Types — mirror of TableResult::toArray() in Laravel

export interface TableColumn {
  key: string;
  label: string;
  sortable: boolean;
  searchable: boolean;
  filterable: boolean;
  filterOptions: string[] | Record<string, string>;
  visible: boolean;
  type: TableColumnType;
  meta: Record<string, unknown>;
}

export type TableColumnType = "text" | "badge" | "actions" | string;

export interface TableMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number | null;
  to: number | null;
}

export interface TableState {
  sort: string | null;
  direction: "asc" | "desc";
  search: string;
  filters: Record<string, unknown>;
  per_page: number;
}

export interface TableProps<TData = Record<string, unknown>> {
  data: TData[];
  columns: TableColumn[];
  meta: TableMeta;
  state: TableState;
}

// Action types — mirror of Action::resolve() in Laravel
export interface TableActionConfirm {
  title: string;
  message: string;
}

export interface TableAction {
  type: "action";
  key: string;
  label: string;
  icon: string | null;
  variant: "default" | "destructive";
  href: string | null;
  method: Method;
  modal: boolean;
  disabled: boolean;
  confirm: TableActionConfirm | null;
  meta: Record<string, unknown>;
}

export type Method = "get" | "post" | "put" | "patch" | "delete";

export interface TableActionGroup {
  type: "group";
  label: string;
  icon: string | null;
  actions: TableAction[];
}

export type ActionItem = TableAction | TableActionGroup;
