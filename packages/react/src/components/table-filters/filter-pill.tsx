import * as React from "react";
import { Trash2, Search } from "lucide-react";
import { Badge } from "../ui/badge";
import { Button } from "../ui/button";
import { Input } from "../ui/input";
import { Checkbox } from "../ui/checkbox";
import { useDebouncedCallback } from "use-debounce";
import { Popover, PopoverContent, PopoverTrigger } from "../ui/popover";
import { Select, SelectContent, SelectItem, SelectTrigger } from "../ui/select";
import { TableFilter } from "@mdaushi/kinetics-core";

export interface FilterPillProps {
  definition: TableFilter;
  currentValue: any;
  onChange: (value: any) => void;
  onRemove: () => void;
  defaultOpen?: boolean;
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
  const [searchQuery, setSearchQuery] = React.useState("");

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

  // Sync internal state when opened
  const handleOpenChange = (open: boolean) => {
    setIsOpen(open);
    if (!open) setSearchQuery("");
  };

  // Format pill text based on type
  let displayValue = "...";
  if (
    definition.type === "select" &&
    Array.isArray(value) &&
    value.length > 0
  ) {
    const selectedLabels = value.map((v) => {
      const opt = definition.options?.find((o) => o.value == v);
      return opt ? opt.label : v;
    });
    displayValue = selectedLabels.join(", ");
  } else if (definition.type === "text" && value) {
    displayValue = value;
  }

  return (
    <Popover open={isOpen} onOpenChange={handleOpenChange}>
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
          {definition.type === "text" && (
            <Input
              placeholder="Type here..."
              value={value}
              onChange={(e) => handleValueChange(e.target.value)}
              className="h-8"
              autoFocus
            />
          )}

          {definition.type === "select" && (
            <div className="flex flex-col gap-2">
              <div className="relative">
                <Search className="absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
                <Input
                  placeholder={`Search ${definition.label}...`}
                  className="h-8 pl-8 text-xs"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                />
              </div>
              <div className="max-h-[200px] overflow-y-auto pr-1 flex flex-col gap-1.5">
                {definition.options
                  ?.filter((opt) =>
                    opt.label.toLowerCase().includes(searchQuery.toLowerCase()),
                  )
                  .map((opt) => {
                    const isChecked =
                      Array.isArray(value) && value.includes(String(opt.value));
                    return (
                      <label
                        key={opt.value}
                        className="flex items-center gap-2 rounded-md p-1.5 hover:bg-accent cursor-pointer"
                      >
                        <Checkbox
                          checked={isChecked}
                          onCheckedChange={(checked) => {
                            let newVal = Array.isArray(value) ? [...value] : [];
                            if (checked) {
                              newVal.push(String(opt.value));
                            } else {
                              newVal = newVal.filter(
                                (v) => v !== String(opt.value),
                              );
                            }
                            handleValueChange(newVal);
                          }}
                        />
                        <span className="text-sm">{opt.label}</span>
                      </label>
                    );
                  })}
              </div>
            </div>
          )}
        </div>

        {definition.type === "select" &&
          Array.isArray(value) &&
          value.length > 0 && (
            <Button
              variant="ghost"
              size="sm"
              className="w-full"
              onClick={() => handleValueChange([])}
            >
              Clear selection
            </Button>
          )}
      </PopoverContent>
    </Popover>
  );
}
