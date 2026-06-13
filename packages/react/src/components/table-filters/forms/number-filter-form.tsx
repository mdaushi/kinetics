import { Input } from "../../ui/input";
import { FilterFormProps } from "./types";

export function NumberFilterForm({
  definition,
  operator,
  value,
  onChange,
}: FilterFormProps) {
  if (definition.meta?.is_range || operator === "between") {
    return (
      <div className="flex flex-col gap-2">
        <Input
          type="number"
          placeholder="Min"
          value={Array.isArray(value) ? value[0] : ""}
          onChange={(e) =>
            onChange([e.target.value, Array.isArray(value) ? value[1] : ""])
          }
          className="h-8"
        />
        <Input
          type="number"
          placeholder="Max"
          value={Array.isArray(value) ? value[1] : ""}
          onChange={(e) =>
            onChange([Array.isArray(value) ? value[0] : "", e.target.value])
          }
          className="h-8"
        />
      </div>
    );
  }

  return (
    <Input
      type="number"
      placeholder="Type number..."
      value={(value as string) || ""}
      onChange={(e) => onChange(e.target.value)}
      className="h-8"
      autoFocus
    />
  );
}
