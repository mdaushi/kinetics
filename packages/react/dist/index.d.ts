import * as _tanstack_react_table from '@tanstack/react-table';
import { TableProps, TableColumn, TableMeta, ActionItem } from '@mdaushi/kinetics-core';
export { ActionItem, TableAction, TableActionGroup, TableColumn, TableMeta, TableProps, TableState } from '@mdaushi/kinetics-core';
import * as react_jsx_runtime from 'react/jsx-runtime';

interface UseTableOptions<TData> {
    table: TableProps<TData>;
    url?: string;
    searchDebounce?: number;
}
declare function useTable<TData extends Record<string, unknown>>({ table: serverData, url, searchDebounce, }: UseTableOptions<TData>): {
    tableInstance: _tanstack_react_table.Table<TData>;
    columns: TableColumn[];
    meta: TableMeta;
    search: string;
    setSearch: (value: string) => void;
    setFilter: (key: string, value: unknown) => void;
    filters: Record<string, unknown>;
};

interface TableComponentProps<TData extends Record<string, unknown>> {
    table: TableProps<TData>;
    searchPlaceholder?: string;
}
declare function Table<TData extends Record<string, unknown>>({ table: serverData, searchPlaceholder, }: TableComponentProps<TData>): react_jsx_runtime.JSX.Element;

interface ActionCellProps {
    actions: ActionItem[];
}
declare function ActionCell({ actions }: ActionCellProps): react_jsx_runtime.JSX.Element;

export { ActionCell, Table, type UseTableOptions, useTable };
