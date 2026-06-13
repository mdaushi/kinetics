import { format } from "date-fns";
import { Calendar } from "../../ui/calendar";
import { FilterFormProps } from "./types";
import { DateRange } from "react-day-picker";

export function DateFilterForm({
  definition,
  operator,
  value,
  onChange,
}: FilterFormProps) {
  if (definition.meta?.is_range || operator === "between") {
    const selectedRange: DateRange = {
      from: Array.isArray(value) && value[0] ? new Date(value[0]) : undefined,
      to: Array.isArray(value) && value[1] ? new Date(value[1]) : undefined,
    };

    return (
      <div className="flex justify-center border rounded-md mt-2 bg-background">
        <Calendar
          mode="range"
          defaultMonth={selectedRange.from}
          selected={selectedRange}
          onSelect={(range) => {
            onChange([
              range?.from ? format(range.from, "yyyy-MM-dd") : "",
              range?.to ? format(range.to, "yyyy-MM-dd") : "",
            ]);
          }}
          numberOfMonths={1}
        />
      </div>
    );
  }

  const selectedDate =
    value && typeof value === "string" ? new Date(value) : undefined;

  return (
    <div className="flex justify-center border rounded-md mt-2 bg-background">
      <Calendar
        mode="single"
        selected={selectedDate}
        onSelect={(date) => onChange(date ? format(date, "yyyy-MM-dd") : "")}
      />
    </div>
  );
}
