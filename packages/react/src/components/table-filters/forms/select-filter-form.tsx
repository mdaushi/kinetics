import * as React from "react";
import { Search } from "lucide-react";
import { Input } from "../../ui/input";
import { Checkbox } from "../../ui/checkbox";
import { Button } from "../../ui/button";
import { FilterFormProps } from "./types";

export function SelectFilterForm({
  definition,
  value,
  onChange,
}: FilterFormProps) {
  const [searchQuery, setSearchQuery] = React.useState("");

  return (
    <>
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
          {(definition.meta?.options as any[])
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
                        newVal = newVal.filter((v) => v !== String(opt.value));
                      }
                      onChange(newVal);
                    }}
                  />
                  <span className="text-sm">{opt.label}</span>
                </label>
              );
            })}
        </div>
      </div>
      {Array.isArray(value) && value.length > 0 && (
        <Button
          variant="ghost"
          size="sm"
          className="w-full mt-2"
          onClick={() => onChange([])}
        >
          Clear selection
        </Button>
      )}
    </>
  );
}
