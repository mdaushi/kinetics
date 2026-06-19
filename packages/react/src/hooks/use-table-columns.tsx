import React, { useMemo } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { ActionItem } from "@mdaushi/kinetics-core";
import { ActionCell } from "../components/action-cell";
import { TableColumnHeader } from "../components/table-column-header";
import { Badge } from "../components/ui/badge";
import { Checkbox } from "../components/ui/checkbox";
import { useTableProps } from "./use-table-props";

export function useTableColumns<TData extends Record<string, unknown>>(
  table: string,
) {
  const { columns: serverColumns, bulk_actions } = useTableProps<TData>(table);

  const columnDefs = useMemo<ColumnDef<TData>[]>(() => {
    const defs = serverColumns
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

    if (bulk_actions && bulk_actions.length > 0) {
      defs.unshift({
        id: "select",
        header: ({ table }) => {
          "use no memo";
          const isAllSelected = table.getIsAllPageRowsSelected();
          const isSomeSelected = table.getIsSomePageRowsSelected();
          return (
            <Checkbox
              checked={isAllSelected}
              indeterminate={!isAllSelected && isSomeSelected}
              onCheckedChange={(value) =>
                table.toggleAllPageRowsSelected(!!value)
              }
              aria-label="Select all"
            />
          );
        },
        cell: ({ row }) => {
          "use no memo";
          return (
            <Checkbox
              checked={row.getIsSelected()}
              onCheckedChange={(value) => row.toggleSelected(!!value)}
              aria-label="Select row"
            />
          );
        },
        enableSorting: false,
        enableHiding: false,
      });
    }

    return defs;
  }, [serverColumns, bulk_actions]);

  return { columnDefs, serverColumns };
}
