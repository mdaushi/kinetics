import { router } from "@inertiajs/react";
import { useCallback, useMemo } from "react";
import { TableController } from "@mdaushi/kinetics-core";
import { cleanQueryParams } from "../lib/utils";
import { useTableProps } from "./use-table-props";

export function useTableRouter<TData extends Record<string, unknown>>(
  propName: string = "table",
  url?: string,
) {
  const { state: serverState, meta } = useTableProps<TData>(propName, url);

  const ctrl = useMemo(
    () => new TableController(serverState, meta),
    [serverState, meta],
  );

  const visit = useCallback(
    (params: Record<string, unknown>) => {
      const cleanParams = cleanQueryParams(params, meta.default_per_page);

      router.get(url ?? window.location.pathname, cleanParams, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      });
    },
    [url, meta.default_per_page],
  );

  return { ctrl, visit };
}
