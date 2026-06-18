import React, { useMemo } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { ActionItem } from "@mdaushi/kinetics-core";
import { ActionCell } from "../components/action-cell";
import { TableColumnHeader } from "../components/table-column-header";
import { Badge } from "../components/ui/badge";
import { useTableProps } from "./use-table-props";

export function useTableColumns<TData extends Record<string, unknown>>(
  table: string,
) {
  const { columns: serverColumns } = useTableProps<TData>(table);

  const columnDefs = useMemo<ColumnDef<TData>[]>(() => {
    return serverColumns
      .filter((col) => col.visible)
      .map((col): ColumnDef<TData> => {
        // Action column — auto-inject ActionCell
        if (col.type === "actions") {
          return {
            id: col.key,
            accessorKey: col.key,
            header: col.label,
            enableSorting: false,
            cell: ({ getValue }) => (
              <ActionCell actions={(getValue() as ActionItem[]) ?? []} />
            ),
          };
        }

        return {
          id: col.key,
          accessorKey: col.key,
          header: ({ column }) => (
            <TableColumnHeader<TData> column={column} title={col.label} />
          ),
          enableSorting: col.sortable,
          cell: ({ getValue }) => {
            const value = getValue();
            if (col.type === "badge") {
              const colorMeta = col.meta?.color;
              let finalColor = "default";

              if (typeof colorMeta === "string") {
                finalColor = colorMeta;
              } else if (typeof colorMeta === "object" && colorMeta !== null) {
                finalColor =
                  (colorMeta as Record<string, string>)[String(value)] ??
                  "default";
              }

              return (
                <Badge
                  variant={
                    finalColor as React.ComponentProps<typeof Badge>["variant"]
                  }
                >
                  {String(value)}
                </Badge>
              );
            }

            const formatted = value ? String(value) : null;

            return formatted;
          },
        };
      });
  }, [serverColumns]);

  return { columnDefs, serverColumns };
}
