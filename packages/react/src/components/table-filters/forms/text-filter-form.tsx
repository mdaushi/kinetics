import { Input } from "../../ui/input";
import { FilterFormProps } from "./types";

export function TextFilterForm({ value, onChange }: FilterFormProps) {
  return (
    <Input
      placeholder="Type here..."
      value={value}
      onChange={(e) => onChange(e.target.value)}
      className="h-8"
      autoFocus
    />
  );
}
