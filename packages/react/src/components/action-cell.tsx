import { router } from "@inertiajs/react";
import type {
  TableAction,
  TableActionGroup,
  ActionItem,
} from "@mdaushi/kinetics-core";
import { Button } from "./ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "./ui/dropdown-menu";
import { DynamicIcon, IconName } from "lucide-react/dynamic";

export type { TableAction, TableActionGroup, ActionItem };

interface ActionCellProps {
  actions: ActionItem[];
}

export function ActionCell({ actions }: ActionCellProps) {
  return (
    <div className="flex items-center justify-end gap-1">
      {actions.map((item, idx) =>
        item.type === "group" ? (
          <ActionGroupDropdown key={idx} group={item} />
        ) : (
          <ActionButton key={item.key} action={item} />
        ),
      )}
    </div>
  );
}

function ActionButton({ action }: { action: TableAction }) {
  function handleClick() {
    if (action.disabled) return;
    execute(action);
  }

  return (
    <Button
      size={"sm"}
      onClick={handleClick}
      disabled={action.disabled}
      title={action.label}
      variant={action.variant}
    >
      {action.icon && <Icon name={action.icon} />}
      <span className="sr-only sm:not-sr-only">{action.label}</span>
    </Button>
  );
}

function ActionGroupDropdown({ group }: { group: TableActionGroup }) {
  return (
    <DropdownMenu>
      <DropdownMenuTrigger
        render={
          <Button variant="ghost" size="icon">
            {group.icon && <Icon name={group.icon} />}
            <span className="sr-only">Open menu</span>
          </Button>
        }
      ></DropdownMenuTrigger>
      <DropdownMenuContent align="end" className="w-40">
        {group.actions.map((action) => (
          <DropdownMenuItem
            key={action.key}
            disabled={action.disabled}
            variant={action.variant}
          >
            {action.icon && <Icon name={action.icon} />}
            {action.label}
          </DropdownMenuItem>
        ))}
      </DropdownMenuContent>
    </DropdownMenu>
  );
}

function execute(action: TableAction) {
  if (!action.href) return;
  if (action.method === "get") {
    router.visit(action.href);
  } else {
    router.visit(action.href, { method: action.method });
  }
}

interface IconProps {
  name: string;
  size?: number;
}

export function Icon({ name, size = 16 }: IconProps) {
  return <DynamicIcon name={name as IconName} size={size} />;
}
