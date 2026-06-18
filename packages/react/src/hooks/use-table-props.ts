import { usePage } from "@inertiajs/react";
import { TableProps } from "@mdaushi/kinetics-core";

export function useTableProps<TData extends Record<string, unknown>>(
  table: string,
): TableProps<TData> {
  const props = usePage().props;
  if (!props[table]) {
    throw new Error(
      `Kinetics Datatable: Table props not found in page props for key: "${table}"`,
    );
  }

  return props[table] as TableProps<TData>;
}
