/**
 * Formats the value according to the column type to display as a string.
 * Adapters (React/Vue) can use this directly or override it on a per-column basis.
 */
export function formatValue(value: unknown, type: string): string {
  if (value === null || value === undefined) return "—";

  switch (type) {
    case "date":
      return new Date(value as string).toLocaleDateString("id-ID");

    case "datetime":
      return new Date(value as string).toLocaleString("id-ID");

    case "currency":
      return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
      }).format(value as number);

    default:
      return String(value);
  }
}
