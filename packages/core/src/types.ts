// Types — mirror of TableResult::toArray() in Laravel

export interface TableFilterOption {
  value: string | number;
  label: string;
}

export interface TableFilter {
  name: string;
  label: string;
  type: "text" | "select" | "date" | "number" | string;
  operators?: TableFilterOption[];
  meta: Record<string, unknown>;
}

export interface TableColumn {
  key: string;
  label: string;
  sortable: boolean;
  searchable: boolean;
  visible: boolean;
  type: TableColumnType;
  meta: Record<string, unknown>;
}

export type TableColumnType = "text" | "badge" | "actions" | string;

export interface TableMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  default_per_page: number;
  total: number;
  from: number | null;
  to: number | null;
  options_per_page: number[];
  debounce: number;
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
  filters?: TableFilter[];
  actions?: ToolbarActionItem[];
  bulk_actions?: BulkActionItem[];
  meta: TableMeta;
  state: TableState;
}

// Action types — mirror of Action::resolve() in Laravel
export type ActionVariant = "default" | "destructive";

export interface DisplayMode {
  icon_only: boolean;
  text_only: boolean;
  icon_only_on_mobile: boolean;
}

export interface TableActionConfirm {
  title: string;
  message: string;
}

export interface TableAction extends DisplayMode {
  type: "action";
  key: string;
  label: string;
  icon: string | null;
  variant: ActionVariant;
  href: string | null;
  method: Method;
  modal: boolean;
  disabled: boolean;
  confirm: TableActionConfirm | null;
  meta: Record<string, unknown>;
}

export type Method = "get" | "post" | "put" | "patch" | "delete";

export interface TableActionGroup extends DisplayMode {
  type: "group";
  label: string;
  icon: string | null;
  variant: ActionVariant;
  actions: TableAction[];
}

export type ActionItem = TableAction | TableActionGroup;

export interface ToolbarAction extends DisplayMode {
  type: "action";
  key: string;
  label: string;
  icon: string | null;
  variant: ActionVariant;
  href: string | null;
  method: Method;
  modal: boolean;
  disabled: boolean;
  confirm: TableActionConfirm | null;
  meta: Record<string, unknown>;
}

export interface ToolbarActionGroup extends DisplayMode {
  type: "group";
  label: string;
  icon: string | null;
  variant: ActionVariant;
  actions: ToolbarAction[];
}

export type ToolbarActionItem = ToolbarAction | ToolbarActionGroup;

export interface BulkAction extends DisplayMode {
  type: "action";
  key: string;
  label: string;
  icon: string | null;
  variant: ActionVariant;
  href: string | null;
  method: Method;
  disabled: boolean;
  confirm: TableActionConfirm | null;
  position: "toolbar" | "floating";
  meta: Record<string, unknown>;
}

export interface BulkActionGroup extends DisplayMode {
  type: "group";
  label: string;
  icon: string | null;
  variant: ActionVariant;
  position: "toolbar" | "floating";
  actions: BulkAction[];
}

export type BulkActionItem = BulkAction | BulkActionGroup;

export type AnyActionItem = ActionItem | ToolbarActionItem | BulkActionItem;
export type AnyAction = TableAction | ToolbarAction | BulkAction;
export type AnyActionGroup =
  | TableActionGroup
  | ToolbarActionGroup
  | BulkActionGroup;
