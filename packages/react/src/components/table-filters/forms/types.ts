import { TableFilter } from "@mdaushi/kinetics-core";

export interface FilterFormProps {
  definition: TableFilter;
  operator: string;
  value: any;
  onChange: (value: any) => void;
}
