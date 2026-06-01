'use strict';

var react = require('@inertiajs/react');
var reactTable = require('@tanstack/react-table');
var React = require('react');
var useDebounce = require('use-debounce');
var kineticsCore = require('@mdaushi/kinetics-core');
var clsx = require('clsx');
var tailwindMerge = require('tailwind-merge');
var button = require('@base-ui/react/button');
var classVarianceAuthority = require('class-variance-authority');
var jsxRuntime = require('react/jsx-runtime');
var menu = require('@base-ui/react/menu');
var lucideReact = require('lucide-react');
var dynamic = require('lucide-react/dynamic');
var select = require('@base-ui/react/select');
var input = require('@base-ui/react/input');

function _interopDefault (e) { return e && e.__esModule ? e : { default: e }; }

var React__default = /*#__PURE__*/_interopDefault(React);

// src/hooks/use-table.ts
function cn(...inputs) {
  return tailwindMerge.twMerge(clsx.clsx(inputs));
}
var buttonVariants = classVarianceAuthority.cva(
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
  return /* @__PURE__ */ jsxRuntime.jsx(
    button.Button,
    {
      "data-slot": "button",
      className: cn(buttonVariants({ variant, size, className })),
      ...props
    }
  );
}
function DropdownMenu({ ...props }) {
  return /* @__PURE__ */ jsxRuntime.jsx(menu.Menu.Root, { "data-slot": "dropdown-menu", ...props });
}
function DropdownMenuTrigger({ ...props }) {
  return /* @__PURE__ */ jsxRuntime.jsx(menu.Menu.Trigger, { "data-slot": "dropdown-menu-trigger", ...props });
}
function DropdownMenuContent({
  align = "start",
  alignOffset = 0,
  side = "bottom",
  sideOffset = 4,
  className,
  ...props
}) {
  return /* @__PURE__ */ jsxRuntime.jsx(menu.Menu.Portal, { children: /* @__PURE__ */ jsxRuntime.jsx(
    menu.Menu.Positioner,
    {
      className: "isolate z-50 outline-none",
      align,
      alignOffset,
      side,
      sideOffset,
      children: /* @__PURE__ */ jsxRuntime.jsx(
        menu.Menu.Popup,
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
  return /* @__PURE__ */ jsxRuntime.jsx(
    menu.Menu.Item,
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
  return /* @__PURE__ */ jsxRuntime.jsx("div", { className: "flex items-center justify-end gap-1", children: actions.map(
    (item, idx) => item.type === "group" ? /* @__PURE__ */ jsxRuntime.jsx(ActionGroupDropdown, { group: item }, idx) : /* @__PURE__ */ jsxRuntime.jsx(ActionButton, { action: item }, item.key)
  ) });
}
function ActionButton({ action }) {
  function handleClick() {
    if (action.disabled) return;
    execute(action);
  }
  return /* @__PURE__ */ jsxRuntime.jsxs(
    Button,
    {
      size: "sm",
      onClick: handleClick,
      disabled: action.disabled,
      title: action.label,
      variant: action.variant,
      children: [
        action.icon && /* @__PURE__ */ jsxRuntime.jsx(Icon, { name: action.icon }),
        /* @__PURE__ */ jsxRuntime.jsx("span", { className: "sr-only sm:not-sr-only", children: action.label })
      ]
    }
  );
}
function ActionGroupDropdown({ group }) {
  return /* @__PURE__ */ jsxRuntime.jsxs(DropdownMenu, { children: [
    /* @__PURE__ */ jsxRuntime.jsx(
      DropdownMenuTrigger,
      {
        render: /* @__PURE__ */ jsxRuntime.jsxs(Button, { variant: "ghost", size: "icon", children: [
          group.icon && /* @__PURE__ */ jsxRuntime.jsx(Icon, { name: group.icon }),
          /* @__PURE__ */ jsxRuntime.jsx("span", { className: "sr-only", children: "Open menu" })
        ] })
      }
    ),
    /* @__PURE__ */ jsxRuntime.jsx(DropdownMenuContent, { align: "end", className: "w-40", children: group.actions.map((action) => /* @__PURE__ */ jsxRuntime.jsxs(
      DropdownMenuItem,
      {
        disabled: action.disabled,
        variant: action.variant,
        children: [
          action.icon && /* @__PURE__ */ jsxRuntime.jsx(Icon, { name: action.icon }),
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
    react.router.visit(action.href);
  } else {
    react.router.visit(action.href, { method: action.method });
  }
}
function Icon({ name, size = 16 }) {
  return /* @__PURE__ */ jsxRuntime.jsx(dynamic.DynamicIcon, { name, size });
}
function TableColumnHeader({
  column,
  title,
  className
}) {
  if (!column.getCanSort()) {
    return /* @__PURE__ */ jsxRuntime.jsx("div", { className: cn(className), children: title });
  }
  return /* @__PURE__ */ jsxRuntime.jsx("div", { className: cn("flex items-center gap-2", className), children: /* @__PURE__ */ jsxRuntime.jsxs(DropdownMenu, { children: [
    /* @__PURE__ */ jsxRuntime.jsx(
      DropdownMenuTrigger,
      {
        render: /* @__PURE__ */ jsxRuntime.jsxs(
          Button,
          {
            variant: "ghost",
            size: "sm",
            className: "-ml-3 h-8 data-[state=open]:bg-accent",
            children: [
              /* @__PURE__ */ jsxRuntime.jsx("span", { children: title }),
              column.getIsSorted() === "desc" ? /* @__PURE__ */ jsxRuntime.jsx(lucideReact.ArrowDown, {}) : column.getIsSorted() === "asc" ? /* @__PURE__ */ jsxRuntime.jsx(lucideReact.ArrowUp, {}) : /* @__PURE__ */ jsxRuntime.jsx(lucideReact.ChevronsUpDown, {})
            ]
          }
        )
      }
    ),
    /* @__PURE__ */ jsxRuntime.jsxs(DropdownMenuContent, { align: "start", children: [
      /* @__PURE__ */ jsxRuntime.jsxs(DropdownMenuItem, { onClick: () => column.toggleSorting(false), children: [
        /* @__PURE__ */ jsxRuntime.jsx(lucideReact.ArrowUp, {}),
        "Asc"
      ] }),
      /* @__PURE__ */ jsxRuntime.jsxs(DropdownMenuItem, { onClick: () => column.toggleSorting(true), children: [
        /* @__PURE__ */ jsxRuntime.jsx(lucideReact.ArrowDown, {}),
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
  const ctrl = React.useMemo(
    () => new kineticsCore.TableController(serverState, meta),
    [serverState, meta, url]
  );
  const [search, setSearchLocal] = React.useState(serverState.search ?? "");
  const visit = React.useCallback(
    (params) => {
      react.router.get(url ?? window.location.pathname, params, {
        preserveState: true,
        preserveScroll: true,
        replace: true
      });
    },
    [url]
  );
  const handleSortChange = React.useCallback(
    (updater) => {
      const current = ctrl.toSorting();
      const next = typeof updater === "function" ? updater(current) : updater;
      visit(ctrl.resolveSort(next));
    },
    [ctrl, visit]
  );
  const handlePageChange = React.useCallback(
    (updater) => {
      const current = ctrl.toPagination();
      const next = typeof updater === "function" ? updater(current) : updater;
      visit(ctrl.resolvePagination(next));
    },
    [ctrl, visit]
  );
  const handleSearchChange = useDebounce.useDebouncedCallback((value) => {
    visit(ctrl.resolveSearchParams(value));
  }, searchDebounce);
  const handleFilterChange = React.useCallback(
    (key, value) => {
      visit(ctrl.resolveFilterParams(key, value));
    },
    [ctrl, visit]
  );
  const columnDefs = React.useMemo(() => {
    return serverColumns.filter((col) => col.visible).map((col) => {
      if (col.type === "actions") {
        return {
          id: col.key,
          accessorKey: col.key,
          header: col.label,
          enableSorting: false,
          cell: ({ getValue }) => React__default.default.createElement(ActionCell, {
            actions: getValue() ?? []
          })
        };
      }
      return {
        id: col.key,
        accessorKey: col.key,
        header: ({ column }) => React__default.default.createElement(TableColumnHeader, {
          column,
          title: col.label
        }),
        enableSorting: col.sortable,
        enableColumnFilter: col.filterable,
        cell: ({ getValue }) => {
          const value = getValue();
          return kineticsCore.formatValue(value, col.type);
        }
      };
    });
  }, [serverColumns]);
  const tableInstance = reactTable.useReactTable({
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
    getCoreRowModel: reactTable.getCoreRowModel()
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
  return /* @__PURE__ */ jsxRuntime.jsx(
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
var Select = select.Select.Root;
function SelectGroup({ className, ...props }) {
  return /* @__PURE__ */ jsxRuntime.jsx(
    select.Select.Group,
    {
      "data-slot": "select-group",
      className: cn("scroll-my-1 p-1", className),
      ...props
    }
  );
}
function SelectValue({ className, ...props }) {
  return /* @__PURE__ */ jsxRuntime.jsx(
    select.Select.Value,
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
  return /* @__PURE__ */ jsxRuntime.jsxs(
    select.Select.Trigger,
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
        /* @__PURE__ */ jsxRuntime.jsx(
          select.Select.Icon,
          {
            render: /* @__PURE__ */ jsxRuntime.jsx(lucideReact.ChevronDownIcon, { className: "pointer-events-none size-4 text-muted-foreground" })
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
  return /* @__PURE__ */ jsxRuntime.jsx(select.Select.Portal, { children: /* @__PURE__ */ jsxRuntime.jsx(
    select.Select.Positioner,
    {
      side,
      sideOffset,
      align,
      alignOffset,
      alignItemWithTrigger,
      className: "isolate z-50",
      children: /* @__PURE__ */ jsxRuntime.jsxs(
        select.Select.Popup,
        {
          "data-slot": "select-content",
          "data-align-trigger": alignItemWithTrigger,
          className: cn(
            "relative isolate z-50 max-h-(--available-height) w-(--anchor-width) min-w-36 origin-(--transform-origin) overflow-x-hidden overflow-y-auto rounded-lg bg-popover text-popover-foreground shadow-md ring-1 ring-foreground/10 duration-100 data-[align-trigger=true]:animate-none data-[side=bottom]:slide-in-from-top-2 data-[side=inline-end]:slide-in-from-left-2 data-[side=inline-start]:slide-in-from-right-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 data-open:animate-in data-open:fade-in-0 data-open:zoom-in-95 data-closed:animate-out data-closed:fade-out-0 data-closed:zoom-out-95",
            className
          ),
          ...props,
          children: [
            /* @__PURE__ */ jsxRuntime.jsx(SelectScrollUpButton, {}),
            /* @__PURE__ */ jsxRuntime.jsx(select.Select.List, { children }),
            /* @__PURE__ */ jsxRuntime.jsx(SelectScrollDownButton, {})
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
  return /* @__PURE__ */ jsxRuntime.jsxs(
    select.Select.Item,
    {
      "data-slot": "select-item",
      className: cn(
        "relative flex w-full cursor-default items-center gap-1.5 rounded-md py-1 pr-8 pl-1.5 text-sm outline-hidden select-none focus:bg-accent focus:text-accent-foreground not-data-[variant=destructive]:focus:**:text-accent-foreground data-disabled:pointer-events-none data-disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4 *:[span]:last:flex *:[span]:last:items-center *:[span]:last:gap-2",
        className
      ),
      ...props,
      children: [
        /* @__PURE__ */ jsxRuntime.jsx(select.Select.ItemText, { className: "flex flex-1 shrink-0 gap-2 whitespace-nowrap", children }),
        /* @__PURE__ */ jsxRuntime.jsx(
          select.Select.ItemIndicator,
          {
            render: /* @__PURE__ */ jsxRuntime.jsx("span", { className: "pointer-events-none absolute right-2 flex size-4 items-center justify-center", children: /* @__PURE__ */ jsxRuntime.jsx(lucideReact.CheckIcon, { className: "pointer-events-none" }) })
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
  return /* @__PURE__ */ jsxRuntime.jsx(
    select.Select.ScrollUpArrow,
    {
      "data-slot": "select-scroll-up-button",
      className: cn(
        "top-0 z-10 flex w-full cursor-default items-center justify-center bg-popover py-1 [&_svg:not([class*='size-'])]:size-4",
        className
      ),
      ...props,
      children: /* @__PURE__ */ jsxRuntime.jsx(lucideReact.ChevronUpIcon, {})
    }
  );
}
function SelectScrollDownButton({
  className,
  ...props
}) {
  return /* @__PURE__ */ jsxRuntime.jsx(
    select.Select.ScrollDownArrow,
    {
      "data-slot": "select-scroll-down-button",
      className: cn(
        "bottom-0 z-10 flex w-full cursor-default items-center justify-center bg-popover py-1 [&_svg:not([class*='size-'])]:size-4",
        className
      ),
      ...props,
      children: /* @__PURE__ */ jsxRuntime.jsx(lucideReact.ChevronDownIcon, {})
    }
  );
}
function TablePagination({
  meta,
  table
}) {
  return /* @__PURE__ */ jsxRuntime.jsxs("div", { className: "flex items-center justify-between", children: [
    /* @__PURE__ */ jsxRuntime.jsx("div", { className: "flex-1 text-sm text-muted-foreground", children: /* @__PURE__ */ jsxRuntime.jsxs("span", { children: [
      "Showing ",
      /* @__PURE__ */ jsxRuntime.jsx("strong", { children: meta.from ?? 0 }),
      "-",
      /* @__PURE__ */ jsxRuntime.jsxs("strong", { children: [
        " ",
        meta.to ?? 0
      ] }),
      " of ",
      /* @__PURE__ */ jsxRuntime.jsx("strong", { children: meta.total }),
      " ",
      "results"
    ] }) }),
    /* @__PURE__ */ jsxRuntime.jsxs("div", { className: "flex items-center space-x-6 lg:space-x-8", children: [
      /* @__PURE__ */ jsxRuntime.jsxs("div", { className: "hidden items-center gap-2 lg:flex", children: [
        /* @__PURE__ */ jsxRuntime.jsx(Label, { htmlFor: "rows-per-page", className: "text-sm font-medium", children: "Rows per page" }),
        /* @__PURE__ */ jsxRuntime.jsxs(
          Select,
          {
            value: meta.per_page.toString(),
            onValueChange: (value) => table.setPageSize(Number(value)),
            children: [
              /* @__PURE__ */ jsxRuntime.jsx(SelectTrigger, { size: "sm", className: "w-20", id: "rows-per-page", children: /* @__PURE__ */ jsxRuntime.jsx(SelectValue, { placeholder: meta.per_page }) }),
              /* @__PURE__ */ jsxRuntime.jsx(SelectContent, { side: "left", align: "end", children: [10, 15, 50, 100].map((pageSize) => /* @__PURE__ */ jsxRuntime.jsx(SelectItem, { value: `${pageSize}`, children: pageSize }, pageSize)) })
            ]
          }
        )
      ] }),
      /* @__PURE__ */ jsxRuntime.jsxs("div", { className: "flex w-fit items-center justify-center text-sm font-medium", children: [
        "Page ",
        meta.current_page,
        " of ",
        meta.last_page
      ] }),
      /* @__PURE__ */ jsxRuntime.jsxs("div", { className: "ml-auto flex items-center gap-2 lg:ml-0", children: [
        /* @__PURE__ */ jsxRuntime.jsxs(
          Button,
          {
            variant: "outline",
            className: "hidden h-8 w-8 p-0 lg:flex",
            onClick: () => table.firstPage(),
            disabled: !table.getCanPreviousPage(),
            children: [
              /* @__PURE__ */ jsxRuntime.jsx("span", { className: "sr-only", children: "Go to first page" }),
              /* @__PURE__ */ jsxRuntime.jsx(lucideReact.ChevronsLeft, {})
            ]
          }
        ),
        /* @__PURE__ */ jsxRuntime.jsxs(
          Button,
          {
            variant: "outline",
            className: "size-8",
            size: "icon",
            onClick: () => table.previousPage(),
            disabled: !table.getCanPreviousPage(),
            children: [
              /* @__PURE__ */ jsxRuntime.jsx("span", { className: "sr-only", children: "Go to previous page" }),
              /* @__PURE__ */ jsxRuntime.jsx(lucideReact.ChevronLeft, {})
            ]
          }
        ),
        /* @__PURE__ */ jsxRuntime.jsxs(
          Button,
          {
            variant: "outline",
            className: "size-8",
            size: "icon",
            onClick: () => table.nextPage(),
            disabled: !table.getCanNextPage(),
            children: [
              /* @__PURE__ */ jsxRuntime.jsx("span", { className: "sr-only", children: "Go to next page" }),
              /* @__PURE__ */ jsxRuntime.jsx(lucideReact.ChevronRight, {})
            ]
          }
        ),
        /* @__PURE__ */ jsxRuntime.jsxs(
          Button,
          {
            variant: "outline",
            className: "hidden size-8 lg:flex",
            size: "icon",
            onClick: () => table.lastPage(),
            disabled: !table.getCanNextPage(),
            children: [
              /* @__PURE__ */ jsxRuntime.jsx("span", { className: "sr-only", children: "Go to last page" }),
              /* @__PURE__ */ jsxRuntime.jsx(lucideReact.ChevronsRight, {})
            ]
          }
        )
      ] })
    ] })
  ] });
}
function Input({ className, type, ...props }) {
  return /* @__PURE__ */ jsxRuntime.jsx(
    input.Input,
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
  return /* @__PURE__ */ jsxRuntime.jsx("div", { className: "flex items-center justify-between", children: /* @__PURE__ */ jsxRuntime.jsxs("div", { className: "flex flex-1 items-center gap-2", children: [
    hasSearch && /* @__PURE__ */ jsxRuntime.jsx(
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
      return /* @__PURE__ */ jsxRuntime.jsxs(
        Select,
        {
          items: options,
          value: filters[col.key] ?? "",
          onValueChange: (e) => setFilter(col.key, e || null),
          children: [
            /* @__PURE__ */ jsxRuntime.jsx(SelectTrigger, { children: /* @__PURE__ */ jsxRuntime.jsx(SelectValue, { placeholder: col.label }) }),
            /* @__PURE__ */ jsxRuntime.jsx(SelectContent, { children: /* @__PURE__ */ jsxRuntime.jsxs(SelectGroup, { children: [
              /* @__PURE__ */ jsxRuntime.jsxs(SelectItem, { value: "", children: [
                "All ",
                col.label
              ] }),
              Object.entries(options).map(([value, label]) => /* @__PURE__ */ jsxRuntime.jsx(SelectItem, { value, children: label }, value))
            ] }) })
          ]
        }
      );
    }),
    hasFilter && /* @__PURE__ */ jsxRuntime.jsxs(Button, { variant: "ghost", children: [
      "Reset",
      /* @__PURE__ */ jsxRuntime.jsx(lucideReact.X, {})
    ] })
  ] }) });
}
function Table({ className, ...props }) {
  return /* @__PURE__ */ jsxRuntime.jsx(
    "div",
    {
      "data-slot": "table-container",
      className: "relative w-full overflow-x-auto",
      children: /* @__PURE__ */ jsxRuntime.jsx(
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
  return /* @__PURE__ */ jsxRuntime.jsx(
    "thead",
    {
      "data-slot": "table-header",
      className: cn("[&_tr]:border-b", className),
      ...props
    }
  );
}
function TableBody({ className, ...props }) {
  return /* @__PURE__ */ jsxRuntime.jsx(
    "tbody",
    {
      "data-slot": "table-body",
      className: cn("[&_tr:last-child]:border-0", className),
      ...props
    }
  );
}
function TableRow({ className, ...props }) {
  return /* @__PURE__ */ jsxRuntime.jsx(
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
  return /* @__PURE__ */ jsxRuntime.jsx(
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
  return /* @__PURE__ */ jsxRuntime.jsx(
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
  return /* @__PURE__ */ jsxRuntime.jsx("div", { className: "overflow-hidden rounded-md border", children: /* @__PURE__ */ jsxRuntime.jsxs(Table, { children: [
    /* @__PURE__ */ jsxRuntime.jsx(TableHeader, { className: "sticky top-0 z-10 bg-muted", children: table.getHeaderGroups().map((headerGroup) => /* @__PURE__ */ jsxRuntime.jsx(TableRow, { children: headerGroup.headers.map((header) => {
      return /* @__PURE__ */ jsxRuntime.jsx(TableHead, { colSpan: header.colSpan, children: header.isPlaceholder ? null : reactTable.flexRender(
        header.column.columnDef.header,
        header.getContext()
      ) }, header.id);
    }) }, headerGroup.id)) }),
    /* @__PURE__ */ jsxRuntime.jsx(TableBody, { children: table.getRowModel().rows?.length ? table.getRowModel().rows.map((row) => /* @__PURE__ */ jsxRuntime.jsx(
      TableRow,
      {
        "data-state": row.getIsSelected() && "selected",
        children: row.getVisibleCells().map((cell) => /* @__PURE__ */ jsxRuntime.jsx(TableCell, { children: reactTable.flexRender(cell.column.columnDef.cell, cell.getContext()) }, cell.id))
      },
      row.id
    )) : /* @__PURE__ */ jsxRuntime.jsx(TableRow, { children: /* @__PURE__ */ jsxRuntime.jsx(
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
  return /* @__PURE__ */ jsxRuntime.jsxs("div", { className: "space-y-4", children: [
    /* @__PURE__ */ jsxRuntime.jsx(
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
    /* @__PURE__ */ jsxRuntime.jsx(RenderTable, { table: tableInstance }),
    /* @__PURE__ */ jsxRuntime.jsx(TablePagination, { meta, table: tableInstance })
  ] });
}

exports.ActionCell = ActionCell;
exports.Table = Table2;
exports.useTable = useTable;
//# sourceMappingURL=index.cjs.map
//# sourceMappingURL=index.cjs.map