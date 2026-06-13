import * as React from "react";
import { Trash2 } from "lucide-react";
import { Badge } from "../ui/badge";
import { Button } from "../ui/button";
import { useDebouncedCallback } from "use-debounce";
import { Popover, PopoverContent, PopoverTrigger } from "../ui/popover";
import { Select, SelectContent, SelectItem, SelectTrigger } from "../ui/select";
import { TableFilter } from "@mdaushi/kinetics-core";
import { TextFilterForm } from "./forms/text-filter-form";
import { SelectFilterForm } from "./forms/select-filter-form";
import { DateFilterForm } from "./forms/date-filter-form";
import { NumberFilterForm } from "./forms/number-filter-form";

export interface FilterPillProps {
  definition: TableFilter;
  currentValue: any;
  onChange: (value: any) => void;
  onRemove: () => void;
  defaultOpen?: boolean;
}

function getDisplayValue(
  definition: TableFilter,
  operator: string,
  value: any,
): string {
  if (!value) return "...";

  switch (definition.type) {
    case "select":
      if (Array.isArray(value) && value.length > 0) {
        const options = definition.meta?.options as any[];
        return value
          .map((v) => options?.find((o) => o.value == v)?.label ?? v)
          .join(", ");
      }
      return "...";

    case "date":
    case "number":
      if (definition.meta?.is_range || operator === "between") {
        if (Array.isArray(value) && (value[0] || value[1])) {
          return `${value[0] || "..."} to ${value[1] || "..."}`;
        }
        return "...";
      }
      return String(value);

    case "text":
    default:
      return String(value);
  }
}

function renderFilterForm(
  definition: TableFilter,
  operator: string,
  value: any,
  onChange: (val: any) => void,
) {
  const props = { definition, operator, value, onChange };
  switch (definition.type) {
    case "text":
      return <TextFilterForm {...props} />;
    case "number":
      return <NumberFilterForm {...props} />;
    case "select":
      return <SelectFilterForm {...props} />;
    case "date":
      return <DateFilterForm {...props} />;
    default:
      return null;
  }
}

export function FilterPill({
  definition,
  currentValue,
  onChange,
  onRemove,
  defaultOpen,
}: FilterPillProps) {
  // Normalize value to object format { operator, value }
  const valObj = React.useMemo(() => {
    if (
      typeof currentValue === "object" &&
      currentValue !== null &&
      !Array.isArray(currentValue)
    ) {
      return currentValue;
    }
    return {
      operator: definition.operators?.[0]?.value || "is",
      value: currentValue || (definition.type === "select" ? [] : ""),
    };
  }, [currentValue, definition]);

  const [operator, setOperator] = React.useState(valObj.operator);
  const [value, setValue] = React.useState(valObj.value);
  const [isOpen, setIsOpen] = React.useState(defaultOpen || false);

  const debouncedOnChange = useDebouncedCallback((op, val) => {
    onChange({ operator: op, value: val });
  }, 500);

  const handleOperatorChange = (newOp: string) => {
    setOperator(newOp);
    debouncedOnChange(newOp, value);
  };

  const handleValueChange = (newVal: any) => {
    setValue(newVal);
    debouncedOnChange(operator, newVal);
  };

  const displayValue = getDisplayValue(definition, operator, value);

  return (
    <Popover open={isOpen} onOpenChange={setIsOpen}>
      <PopoverTrigger
        render={
          <Badge
            render={<button type="button" />}
            variant="secondary"
            className="h-8 cursor-pointer gap-1 rounded-full px-3 py-1 font-normal hover:bg-secondary/80"
          >
            <span className="font-medium text-foreground">
              {definition.label}:
            </span>
            <span className="text-muted-foreground truncate max-w-[120px]">
              {displayValue}
            </span>
          </Badge>
        }
      />
      <PopoverContent align="start" className="max-w-[280px] w-fit gap-1">
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-2">
            <span className="text-sm font-medium text-muted-foreground">
              {definition.label}
            </span>
            <Select value={operator} onValueChange={handleOperatorChange}>
              <SelectTrigger className="h-7 text-xs border-none bg-transparent hover:bg-accent focus:ring-0 px-2 py-0">
                <span className="truncate">
                  {definition.operators?.find(
                    (op) => String(op.value) === String(operator),
                  )?.label || operator}
                </span>
              </SelectTrigger>
              <SelectContent>
                {definition.operators?.map((op) => (
                  <SelectItem
                    key={op.value}
                    value={String(op.value)}
                    className="text-sm"
                  >
                    {op.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          <Button
            variant="ghost"
            size="icon"
            className="h-6 w-6 text-muted-foreground hover:text-destructive"
            onClick={(e) => {
              e.stopPropagation();
              onRemove();
              setIsOpen(false);
            }}
          >
            <Trash2 className="h-4 w-4" />
          </Button>
        </div>

        <div className="pt-2">
          {renderFilterForm(definition, operator, value, handleValueChange)}
        </div>
      </PopoverContent>
    </Popover>
  );
}
