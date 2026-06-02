import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export function cleanQueryParams(
  params: Record<string, unknown>,
  defaultPerPage: number = 15,
): Record<string, any> {
  const cleanParams: Record<string, any> = {};

  for (const [key, value] of Object.entries(params)) {
    if (value === null || value === undefined || value === "") continue;

    // Omit default pagination states to keep URL clean
    if (key === "page" && value === 1) continue;
    if (key === "per_page" && value === defaultPerPage) continue;

    if (typeof value === "object" && !Array.isArray(value)) {
      const cleanNested = Object.fromEntries(
        Object.entries(value as Record<string, unknown>).filter(
          ([_, v]) => v !== null && v !== undefined && v !== "",
        ),
      );
      if (Object.keys(cleanNested).length > 0) {
        cleanParams[key] = cleanNested;
      }
    } else {
      cleanParams[key] = value;
    }
  }

  return cleanParams;
}
