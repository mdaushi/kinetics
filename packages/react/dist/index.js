import { router } from '@inertiajs/react';
import { useReactTable, getCoreRowModel, flexRender } from '@tanstack/react-table';
import React, { useMemo, useState, useCallback } from 'react';
import { useDebouncedCallback } from 'use-debounce';
import { TableController, formatValue } from '@mdaushi/kinetics-core';
import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';
import { Button as Button$1 } from '@base-ui/react/button';
import { cva } from 'class-variance-authority';
import { jsx, jsxs } from 'react/jsx-runtime';
import { Menu } from '@base-ui/react/menu';
import { ArrowDown, ArrowUp, ChevronsUpDown, X, ChevronsLeft, ChevronLeft, ChevronRight, ChevronsRight, ChevronDownIcon, CheckIcon, ChevronUpIcon } from 'lucide-react';
import { DynamicIcon } from 'lucide-react/dynamic';
import { Select as Select$1 } from '@base-ui/react/select';
import { Input as Input$1 } from '@base-ui/react/input';

// src/hooks/use-table.ts
function cn(...inputs) {
  return twMerge(clsx(inputs));
}
var buttonVariants = cva(
  "group/button inline-flex shrink-0 items-center justify-center rounded-lg border border-transparent bg-clip-padding text-sm font-medium whitespace-nowrap transition-all outline-none select-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 active:not-aria-[haspopup]:translate-y-px disabled:pointer-events-none disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-3 aria-invalid:ring-destructive/20 dark:aria-invalid:border-destructive/50 dark:aria-invalid:ring-destructive/40 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4",
  {
    variants: {
      variant: {
        default: "bg-primary text-primary-foreground hover:bg-primary/80",
        outline: "border-border bg-background hover:bg-muted hover:text-foreground aria-expanded:bg-muted aria-expanded:text-foreground dark:border-input dark:bg-input/30 dark:hover:bg-input/50",
        secondary: "bg-secondary text-secondary-foreground hover:bg-[color-mix(in_oklch,var(--secondary),var(--foreground)_5%)] aria-expanded:bg-secondary aria-expanded:text-secondary-foreground",
        ghost: "hover:bg-muted hover:text-foreground aria-expanded:bg-muted aria-expanded:text-foreground dark:hover:bg-muted/50",
        destructive: "bg-destructive/10 text-destructive hover:bg-destructive/20 focus-visible:border-destructive/40 focus-visible:ring-destructive/20 dark:bg-destructive/20 dark:hover:bg-destructive/30 dark:focus-visible:ring-destructive/40",
        link: "text-primary underline-offset-4 hover:underline"
      },
      size: {
        default: "h-8 gap-1.5 px-2.5 has-data-[icon=inline-end]:pr-2 has-data-[icon=inline-start]:pl-2",
        xs: "h-6 gap-1 rounded-[min(var(--radius-md),10px)] px-2 text-xs in-data-[slot=button-group]:rounded-lg has-data-[icon=inline-end]:pr-1.5 has-data-[icon=inline-start]:pl-1.5 [&_svg:not([class*='size-'])]:size-3",
        sm: "h-7 gap-1 rounded-[min(var(--radius-md),12px)] px-2.5 text-[0.8rem] in-data-[slot=button-group]:rounded-lg has-data-[icon=inline-end]:pr-1.5 has-data-[icon=inline-start]:pl-1.5 [&_svg:not([class*='size-'])]:size-3.5",
        lg: "h-9 gap-1.5 px-2.5 has-data-[icon=inline-end]:pr-2 has-data-[icon=inline-start]:pl-2",
        icon: "size-8",
        "icon-xs": "size-6 rounded-[min(var(--radius-md),10px)] in-data-[slot=button-group]:rounded-lg [&_svg:not([class*='size-'])]:size-3",
        "icon-sm": "size-7 rounded-[min(var(--radius-md),12px)] in-data-[slot=button-group]:rounded-lg",
        "icon-lg": "size-9"
      }
    },
    defaultVariants: {
      variant: "default",
      size: "default"
    }
  }
);
function Button({
  className,
  variant = "default",
  size = "default",
  ...props
}) {
  return /* @__PURE__ */ jsx(
    Button$1,
    {
      "data-slot": "button",
      className: cn(buttonVariants({ variant, size, className })),
      ...props
    }
  );
}
function DropdownMenu({ ...props }) {
  return /* @__PURE__ */ jsx(Menu.Root, { "data-slot": "dropdown-menu", ...props });
}
function DropdownMenuTrigger({ ...props }) {
  return /* @__PURE__ */ jsx(Menu.Trigger, { "data-slot": "dropdown-menu-trigger", ...props });
}
function DropdownMenuContent({
  align = "start",
  alignOffset = 0,
  side = "bottom",
  sideOffset = 4,
  className,
  ...props
}) {
  return /* @__PURE__ */ jsx(Menu.Portal, { children: /* @__PURE__ */ jsx(
    Menu.Positioner,
    {
      className: "isolate z-50 outline-none",
      align,
      alignOffset,
      side,
      sideOffset,
      children: /* @__PURE__ */ jsx(
        Menu.Popup,
        {
          "data-slot": "dropdown-menu-content",
          className: cn(
            "z-50 max-h-(--available-height) w-(--anchor-width) min-w-32 origin-(--transform-origin) overflow-x-hidden overflow-y-auto rounded-lg bg-popover p-1 text-popover-foreground shadow-md ring-1 ring-foreground/10 duration-100 outline-none data-[side=bottom]:slide-in-from-top-2 data-[side=inline-end]:slide-in-from-left-2 data-[side=inline-start]:slide-in-from-right-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 data-open:animate-in data-open:fade-in-0 data-open:zoom-in-95 data-closed:animate-out data-closed:overflow-hidden data-closed:fade-out-0 data-closed:zoom-out-95",
            className
          ),
          ...props
        }
      )
    }
  ) });
}
function DropdownMenuItem({
  className,
  inset,
  variant = "default",
  ...props
}) {
  return /* @__PURE__ */ jsx(
    Menu.Item,
    {
      "data-slot": "dropdown-menu-item",
      "data-inset": inset,
      "data-variant": variant,
      className: cn(
        "group/dropdown-menu-item relative flex cursor-default items-center gap-1.5 rounded-md px-1.5 py-1 text-sm outline-hidden select-none focus:bg-accent focus:text-accent-foreground not-data-[variant=destructive]:focus:**:text-accent-foreground data-inset:pl-7 data-[variant=destructive]:text-destructive data-[variant=destructive]:focus:bg-destructive/10 data-[variant=destructive]:focus:text-destructive dark:data-[variant=destructive]:focus:bg-destructive/20 data-disabled:pointer-events-none data-disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4 data-[variant=destructive]:*:[svg]:text-destructive",
        className
      ),
      ...props
    }
  );
}
function ActionCell({ actions }) {
  return /* @__PURE__ */ jsx("div", { className: "flex items-center justify-end gap-1", children: actions.map(
    (item, idx) => item.type === "group" ? /* @__PURE__ */ jsx(ActionGroupDropdown, { group: item }, idx) : /* @__PURE__ */ jsx(ActionButton, { action: item }, item.key)
  ) });
}
function ActionButton({ action }) {
  function handleClick() {
    if (action.disabled) return;
    execute(action);
  }
  return /* @__PURE__ */ jsxs(
    Button,
    {
      size: "sm",
      onClick: handleClick,
      disabled: action.disabled,
      title: action.label,
      variant: action.variant,
      children: [
        action.icon && /* @__PURE__ */ jsx(Icon, { name: action.icon }),
        /* @__PURE__ */ jsx("span", { className: "sr-only sm:not-sr-only", children: action.label })
      ]
    }
  );
}
function ActionGroupDropdown({ group }) {
  return /* @__PURE__ */ jsxs(DropdownMenu, { children: [
    /* @__PURE__ */ jsx(
      DropdownMenuTrigger,
      {
        render: /* @__PURE__ */ jsxs(Button, { variant: "ghost", size: "icon", children: [
          group.icon && /* @__PURE__ */ jsx(Icon, { name: group.icon }),
          /* @__PURE__ */ jsx("span", { className: "sr-only", children: "Open menu" })
        ] })
      }
    ),
    /* @__PURE__ */ jsx(DropdownMenuContent, { align: "end", className: "w-40", children: group.actions.map((action) => /* @__PURE__ */ jsxs(
      DropdownMenuItem,
      {
        disabled: action.disabled,
        variant: action.variant,
        children: [
          action.icon && /* @__PURE__ */ jsx(Icon, { name: action.icon }),
          action.label
        ]
      },
      action.key
    )) })
  ] });
}
function execute(action) {
  if (!action.href) return;
  if (action.method === "get") {
    router.visit(action.href);
  } else {
    router.visit(action.href, { method: action.method });
  }
}
function Icon({ name, size = 16 }) {
  return /* @__PURE__ */ jsx(DynamicIcon, { name, size });
}
function TableColumnHeader({
  column,
  title,
  className
}) {
  if (!column.getCanSort()) {
    return /* @__PURE__ */ jsx("div", { className: cn(className), children: title });
  }
  return /* @__PURE__ */ jsx("div", { className: cn("flex items-center gap-2", className), children: /* @__PURE__ */ jsxs(DropdownMenu, { children: [
    /* @__PURE__ */ jsx(
      DropdownMenuTrigger,
      {
        render: /* @__PURE__ */ jsxs(
          Button,
          {
            variant: "ghost",
            size: "sm",
            className: "-ml-3 h-8 data-[state=open]:bg-accent",
            children: [
              /* @__PURE__ */ jsx("span", { children: title }),
              column.getIsSorted() === "desc" ? /* @__PURE__ */ jsx(ArrowDown, {}) : column.getIsSorted() === "asc" ? /* @__PURE__ */ jsx(ArrowUp, {}) : /* @__PURE__ */ jsx(ChevronsUpDown, {})
            ]
          }
        )
      }
    ),
    /* @__PURE__ */ jsxs(DropdownMenuContent, { align: "start", children: [
      /* @__PURE__ */ jsxs(DropdownMenuItem, { onClick: () => column.toggleSorting(false), children: [
        /* @__PURE__ */ jsx(ArrowUp, {}),
        "Asc"
      ] }),
      /* @__PURE__ */ jsxs(DropdownMenuItem, { onClick: () => column.toggleSorting(true), children: [
        /* @__PURE__ */ jsx(ArrowDown, {}),
        "Desc"
      ] })
    ] })
  ] }) });
}

// src/hooks/use-table.ts
function useTable({
  table: serverData,
  url,
  searchDebounce = 300
}) {
  const { data, columns: serverColumns, meta, state: serverState } = serverData;
  const ctrl = useMemo(
    () => new TableController(serverState, meta),
    [serverState, meta, url]
  );
  const [search, setSearchLocal] = useState(serverState.search ?? "");
  const visit = useCallback(
    (params) => {
      router.get(url ?? window.location.pathname, params, {
        preserveState: true,
        preserveScroll: true,
        replace: true
      });
    },
    [url]
  );
  const handleSortChange = useCallback(
    (updater) => {
      const current = ctrl.toSorting();
      const next = typeof updater === "function" ? updater(current) : updater;
      visit(ctrl.resolveSort(next));
    },
    [ctrl, visit]
  );
  const handlePageChange = useCallback(
    (updater) => {
      const current = ctrl.toPagination();
      const next = typeof updater === "function" ? updater(current) : updater;
      visit(ctrl.resolvePagination(next));
    },
    [ctrl, visit]
  );
  const handleSearchChange = useDebouncedCallback((value) => {
    visit(ctrl.resolveSearchParams(value));
  }, searchDebounce);
  const handleFilterChange = useCallback(
    (key, value) => {
      visit(ctrl.resolveFilterParams(key, value));
    },
    [ctrl, visit]
  );
  const columnDefs = useMemo(() => {
    return serverColumns.filter((col) => col.visible).map((col) => {
      if (col.type === "actions") {
        return {
          id: col.key,
          accessorKey: col.key,
          header: col.label,
          enableSorting: false,
          cell: ({ getValue }) => React.createElement(ActionCell, {
            actions: getValue() ?? []
          })
        };
      }
      return {
        id: col.key,
        accessorKey: col.key,
        header: ({ column }) => React.createElement(TableColumnHeader, {
          column,
          title: col.label
        }),
        enableSorting: col.sortable,
        enableColumnFilter: col.filterable,
        cell: ({ getValue }) => {
          const value = getValue();
          return formatValue(value, col.type);
        }
      };
    });
  }, [serverColumns]);
  const tableInstance = useReactTable({
    data,
    columns: columnDefs,
    state: {
      sorting: ctrl.toSorting(),
      pagination: ctrl.toPagination()
    },
    manualSorting: true,
    manualPagination: true,
    manualFiltering: true,
    pageCount: meta.last_page,
    onSortingChange: handleSortChange,
    onPaginationChange: handlePageChange,
    getCoreRowModel: getCoreRowModel()
  });
  return {
    tableInstance,
    columns: serverColumns,
    meta,
    search,
    setSearch: (value) => {
      setSearchLocal(value);
      handleSearchChange(value);
    },
    setFilter: handleFilterChange,
    filters: serverState.filters
  };
}
function Label({ className, ...props }) {
  return /* @__PURE__ */ jsx(
    "label",
    {
      "data-slot": "label",
      className: cn(
        "flex items-center gap-2 text-sm leading-none font-medium select-none group-data-[disabled=true]:pointer-events-none group-data-[disabled=true]:opacity-50 peer-disabled:cursor-not-allowed peer-disabled:opacity-50",
        className
      ),
      ...props
    }
  );
}
var Select = Select$1.Root;
function SelectGroup({ className, ...props }) {
  return /* @__PURE__ */ jsx(
    Select$1.Group,
    {
      "data-slot": "select-group",
      className: cn("scroll-my-1 p-1", className),
      ...props
    }
  );
}
function SelectValue({ className, ...props }) {
  return /* @__PURE__ */ jsx(
    Select$1.Value,
    {
      "data-slot": "select-value",
      className: cn("flex flex-1 text-left", className),
      ...props
    }
  );
}
function SelectTrigger({
  className,
  size = "default",
  children,
  ...props
}) {
  return /* @__PURE__ */ jsxs(
    Select$1.Trigger,
    {
      "data-slot": "select-trigger",
      "data-size": size,
      className: cn(
        "flex w-fit items-center justify-between gap-1.5 rounded-lg border border-input bg-transparent py-2 pr-2 pl-2.5 text-sm whitespace-nowrap transition-colors outline-none select-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-3 aria-invalid:ring-destructive/20 data-placeholder:text-muted-foreground data-[size=default]:h-8 data-[size=sm]:h-7 data-[size=sm]:rounded-[min(var(--radius-md),10px)] *:data-[slot=select-value]:line-clamp-1 *:data-[slot=select-value]:flex *:data-[slot=select-value]:items-center *:data-[slot=select-value]:gap-1.5 dark:bg-input/30 dark:hover:bg-input/50 dark:aria-invalid:border-destructive/50 dark:aria-invalid:ring-destructive/40 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4",
        className
      ),
      ...props,
      children: [
        children,
        /* @__PURE__ */ jsx(
          Select$1.Icon,
          {
            render: /* @__PURE__ */ jsx(ChevronDownIcon, { className: "pointer-events-none size-4 text-muted-foreground" })
          }
        )
      ]
    }
  );
}
function SelectContent({
  className,
  children,
  side = "bottom",
  sideOffset = 4,
  align = "center",
  alignOffset = 0,
  alignItemWithTrigger = true,
  ...props
}) {
  return /* @__PURE__ */ jsx(Select$1.Portal, { children: /* @__PURE__ */ jsx(
    Select$1.Positioner,
    {
      side,
      sideOffset,
      align,
      alignOffset,
      alignItemWithTrigger,
      className: "isolate z-50",
      children: /* @__PURE__ */ jsxs(
        Select$1.Popup,
        {
          "data-slot": "select-content",
          "data-align-trigger": alignItemWithTrigger,
          className: cn(
            "relative isolate z-50 max-h-(--available-height) w-(--anchor-width) min-w-36 origin-(--transform-origin) overflow-x-hidden overflow-y-auto rounded-lg bg-popover text-popover-foreground shadow-md ring-1 ring-foreground/10 duration-100 data-[align-trigger=true]:animate-none data-[side=bottom]:slide-in-from-top-2 data-[side=inline-end]:slide-in-from-left-2 data-[side=inline-start]:slide-in-from-right-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 data-open:animate-in data-open:fade-in-0 data-open:zoom-in-95 data-closed:animate-out data-closed:fade-out-0 data-closed:zoom-out-95",
            className
          ),
          ...props,
          children: [
            /* @__PURE__ */ jsx(SelectScrollUpButton, {}),
            /* @__PURE__ */ jsx(Select$1.List, { children }),
            /* @__PURE__ */ jsx(SelectScrollDownButton, {})
          ]
        }
      )
    }
  ) });
}
function SelectItem({
  className,
  children,
  ...props
}) {
  return /* @__PURE__ */ jsxs(
    Select$1.Item,
    {
      "data-slot": "select-item",
      className: cn(
        "relative flex w-full cursor-default items-center gap-1.5 rounded-md py-1 pr-8 pl-1.5 text-sm outline-hidden select-none focus:bg-accent focus:text-accent-foreground not-data-[variant=destructive]:focus:**:text-accent-foreground data-disabled:pointer-events-none data-disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4 *:[span]:last:flex *:[span]:last:items-center *:[span]:last:gap-2",
        className
      ),
      ...props,
      children: [
        /* @__PURE__ */ jsx(Select$1.ItemText, { className: "flex flex-1 shrink-0 gap-2 whitespace-nowrap", children }),
        /* @__PURE__ */ jsx(
          Select$1.ItemIndicator,
          {
            render: /* @__PURE__ */ jsx("span", { className: "pointer-events-none absolute right-2 flex size-4 items-center justify-center", children: /* @__PURE__ */ jsx(CheckIcon, { className: "pointer-events-none" }) })
          }
        )
      ]
    }
  );
}
function SelectScrollUpButton({
  className,
  ...props
}) {
  return /* @__PURE__ */ jsx(
    Select$1.ScrollUpArrow,
    {
      "data-slot": "select-scroll-up-button",
      className: cn(
        "top-0 z-10 flex w-full cursor-default items-center justify-center bg-popover py-1 [&_svg:not([class*='size-'])]:size-4",
        className
      ),
      ...props,
      children: /* @__PURE__ */ jsx(ChevronUpIcon, {})
    }
  );
}
function SelectScrollDownButton({
  className,
  ...props
}) {
  return /* @__PURE__ */ jsx(
    Select$1.ScrollDownArrow,
    {
      "data-slot": "select-scroll-down-button",
      className: cn(
        "bottom-0 z-10 flex w-full cursor-default items-center justify-center bg-popover py-1 [&_svg:not([class*='size-'])]:size-4",
        className
      ),
      ...props,
      children: /* @__PURE__ */ jsx(ChevronDownIcon, {})
    }
  );
}
function TablePagination({
  meta,
  table
}) {
  return /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
    /* @__PURE__ */ jsx("div", { className: "flex-1 text-sm text-muted-foreground", children: /* @__PURE__ */ jsxs("span", { children: [
      "Showing ",
      /* @__PURE__ */ jsx("strong", { children: meta.from ?? 0 }),
      "-",
      /* @__PURE__ */ jsxs("strong", { children: [
        " ",
        meta.to ?? 0
      ] }),
      " of ",
      /* @__PURE__ */ jsx("strong", { children: meta.total }),
      " ",
      "results"
    ] }) }),
    /* @__PURE__ */ jsxs("div", { className: "flex items-center space-x-6 lg:space-x-8", children: [
      /* @__PURE__ */ jsxs("div", { className: "hidden items-center gap-2 lg:flex", children: [
        /* @__PURE__ */ jsx(Label, { htmlFor: "rows-per-page", className: "text-sm font-medium", children: "Rows per page" }),
        /* @__PURE__ */ jsxs(
          Select,
          {
            value: meta.per_page.toString(),
            onValueChange: (value) => table.setPageSize(Number(value)),
            children: [
              /* @__PURE__ */ jsx(SelectTrigger, { size: "sm", className: "w-20", id: "rows-per-page", children: /* @__PURE__ */ jsx(SelectValue, { placeholder: meta.per_page }) }),
              /* @__PURE__ */ jsx(SelectContent, { side: "left", align: "end", children: [10, 15, 50, 100].map((pageSize) => /* @__PURE__ */ jsx(SelectItem, { value: `${pageSize}`, children: pageSize }, pageSize)) })
            ]
          }
        )
      ] }),
      /* @__PURE__ */ jsxs("div", { className: "flex w-fit items-center justify-center text-sm font-medium", children: [
        "Page ",
        meta.current_page,
        " of ",
        meta.last_page
      ] }),
      /* @__PURE__ */ jsxs("div", { className: "ml-auto flex items-center gap-2 lg:ml-0", children: [
        /* @__PURE__ */ jsxs(
          Button,
          {
            variant: "outline",
            className: "hidden h-8 w-8 p-0 lg:flex",
            onClick: () => table.firstPage(),
            disabled: !table.getCanPreviousPage(),
            children: [
              /* @__PURE__ */ jsx("span", { className: "sr-only", children: "Go to first page" }),
              /* @__PURE__ */ jsx(ChevronsLeft, {})
            ]
          }
        ),
        /* @__PURE__ */ jsxs(
          Button,
          {
            variant: "outline",
            className: "size-8",
            size: "icon",
            onClick: () => table.previousPage(),
            disabled: !table.getCanPreviousPage(),
            children: [
              /* @__PURE__ */ jsx("span", { className: "sr-only", children: "Go to previous page" }),
              /* @__PURE__ */ jsx(ChevronLeft, {})
            ]
          }
        ),
        /* @__PURE__ */ jsxs(
          Button,
          {
            variant: "outline",
            className: "size-8",
            size: "icon",
            onClick: () => table.nextPage(),
            disabled: !table.getCanNextPage(),
            children: [
              /* @__PURE__ */ jsx("span", { className: "sr-only", children: "Go to next page" }),
              /* @__PURE__ */ jsx(ChevronRight, {})
            ]
          }
        ),
        /* @__PURE__ */ jsxs(
          Button,
          {
            variant: "outline",
            className: "hidden size-8 lg:flex",
            size: "icon",
            onClick: () => table.lastPage(),
            disabled: !table.getCanNextPage(),
            children: [
              /* @__PURE__ */ jsx("span", { className: "sr-only", children: "Go to last page" }),
              /* @__PURE__ */ jsx(ChevronsRight, {})
            ]
          }
        )
      ] })
    ] })
  ] });
}
function Input({ className, type, ...props }) {
  return /* @__PURE__ */ jsx(
    Input$1,
    {
      type,
      "data-slot": "input",
      className: cn(
        "h-8 w-full min-w-0 rounded-lg border border-input bg-transparent px-2.5 py-1 text-base transition-colors outline-none file:inline-flex file:h-6 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 disabled:pointer-events-none disabled:cursor-not-allowed disabled:bg-input/50 disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-3 aria-invalid:ring-destructive/20 md:text-sm dark:bg-input/30 dark:disabled:bg-input/80 dark:aria-invalid:border-destructive/50 dark:aria-invalid:ring-destructive/40",
        className
      ),
      ...props
    }
  );
}
function TableToolbar({
  search,
  columns,
  setSearch,
  placeholder,
  filters,
  setFilter
}) {
  const filterableColumns = columns.filter((c) => c.filterable);
  const hasSearch = columns.some((c) => c.searchable);
  const hasFilter = search || Object.keys(filters).length > 0;
  return /* @__PURE__ */ jsx("div", { className: "flex items-center justify-between", children: /* @__PURE__ */ jsxs("div", { className: "flex flex-1 items-center gap-2", children: [
    hasSearch && /* @__PURE__ */ jsx(
      Input,
      {
        placeholder,
        value: search,
        onChange: (e) => setSearch(e.target.value),
        className: "h-8 w-37.5 lg:w-62.5"
      }
    ),
    filterableColumns.map((col) => {
      const options = Array.isArray(col.filterOptions) ? Object.fromEntries(col.filterOptions.map((o) => [o, o])) : col.filterOptions;
      return /* @__PURE__ */ jsxs(
        Select,
        {
          items: options,
          value: filters[col.key] ?? "",
          onValueChange: (e) => setFilter(col.key, e || null),
          children: [
            /* @__PURE__ */ jsx(SelectTrigger, { children: /* @__PURE__ */ jsx(SelectValue, { placeholder: col.label }) }),
            /* @__PURE__ */ jsx(SelectContent, { children: /* @__PURE__ */ jsxs(SelectGroup, { children: [
              /* @__PURE__ */ jsxs(SelectItem, { value: "", children: [
                "All ",
                col.label
              ] }),
              Object.entries(options).map(([value, label]) => /* @__PURE__ */ jsx(SelectItem, { value, children: label }, value))
            ] }) })
          ]
        }
      );
    }),
    hasFilter && /* @__PURE__ */ jsxs(Button, { variant: "ghost", children: [
      "Reset",
      /* @__PURE__ */ jsx(X, {})
    ] })
  ] }) });
}
function Table({ className, ...props }) {
  return /* @__PURE__ */ jsx(
    "div",
    {
      "data-slot": "table-container",
      className: "relative w-full overflow-x-auto",
      children: /* @__PURE__ */ jsx(
        "table",
        {
          "data-slot": "table",
          className: cn("w-full caption-bottom text-sm", className),
          ...props
        }
      )
    }
  );
}
function TableHeader({ className, ...props }) {
  return /* @__PURE__ */ jsx(
    "thead",
    {
      "data-slot": "table-header",
      className: cn("[&_tr]:border-b", className),
      ...props
    }
  );
}
function TableBody({ className, ...props }) {
  return /* @__PURE__ */ jsx(
    "tbody",
    {
      "data-slot": "table-body",
      className: cn("[&_tr:last-child]:border-0", className),
      ...props
    }
  );
}
function TableRow({ className, ...props }) {
  return /* @__PURE__ */ jsx(
    "tr",
    {
      "data-slot": "table-row",
      className: cn(
        "border-b transition-colors hover:bg-muted/50 has-aria-expanded:bg-muted/50 data-[state=selected]:bg-muted",
        className
      ),
      ...props
    }
  );
}
function TableHead({ className, ...props }) {
  return /* @__PURE__ */ jsx(
    "th",
    {
      "data-slot": "table-head",
      className: cn(
        "h-10 px-2 text-left align-middle font-medium whitespace-nowrap text-foreground [&:has([role=checkbox])]:pr-0",
        className
      ),
      ...props
    }
  );
}
function TableCell({ className, ...props }) {
  return /* @__PURE__ */ jsx(
    "td",
    {
      "data-slot": "table-cell",
      className: cn(
        "p-2 align-middle whitespace-nowrap [&:has([role=checkbox])]:pr-0",
        className
      ),
      ...props
    }
  );
}
function RenderTable({ table }) {
  return /* @__PURE__ */ jsx("div", { className: "overflow-hidden rounded-md border", children: /* @__PURE__ */ jsxs(Table, { children: [
    /* @__PURE__ */ jsx(TableHeader, { className: "sticky top-0 z-10 bg-muted", children: table.getHeaderGroups().map((headerGroup) => /* @__PURE__ */ jsx(TableRow, { children: headerGroup.headers.map((header) => {
      return /* @__PURE__ */ jsx(TableHead, { colSpan: header.colSpan, children: header.isPlaceholder ? null : flexRender(
        header.column.columnDef.header,
        header.getContext()
      ) }, header.id);
    }) }, headerGroup.id)) }),
    /* @__PURE__ */ jsx(TableBody, { children: table.getRowModel().rows?.length ? table.getRowModel().rows.map((row) => /* @__PURE__ */ jsx(
      TableRow,
      {
        "data-state": row.getIsSelected() && "selected",
        children: row.getVisibleCells().map((cell) => /* @__PURE__ */ jsx(TableCell, { children: flexRender(cell.column.columnDef.cell, cell.getContext()) }, cell.id))
      },
      row.id
    )) : /* @__PURE__ */ jsx(TableRow, { children: /* @__PURE__ */ jsx(
      TableCell,
      {
        colSpan: table.getAllColumns.length,
        className: "h-24 text-center",
        children: "No results."
      }
    ) }) })
  ] }) });
}
function Table2({
  table: serverData,
  searchPlaceholder = "Search..."
}) {
  const {
    tableInstance,
    columns: serverColumns,
    meta,
    search,
    setSearch,
    setFilter,
    filters
  } = useTable({ table: serverData });
  return /* @__PURE__ */ jsxs("div", { className: "space-y-4", children: [
    /* @__PURE__ */ jsx(
      TableToolbar,
      {
        search,
        setSearch,
        placeholder: searchPlaceholder,
        columns: serverColumns,
        filters,
        setFilter
      }
    ),
    /* @__PURE__ */ jsx(RenderTable, { table: tableInstance }),
    /* @__PURE__ */ jsx(TablePagination, { meta, table: tableInstance })
  ] });
}

export { ActionCell, Table2 as Table, useTable };
//# sourceMappingURL=index.js.map
//# sourceMappingURL=index.js.map