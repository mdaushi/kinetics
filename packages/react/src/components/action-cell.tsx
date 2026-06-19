import { router } from "@inertiajs/react";
import type {
  ActionItem,
  TableAction,
  TableActionGroup,
  AnyActionItem,
  AnyAction,
  AnyActionGroup,
} from "@mdaushi/kinetics-core";
import { Button } from "./ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "./ui/dropdown-menu";
import { useState } from "react";
import { ConfirmActionDialog } from "./confirm-action-dialog";
import { DynamicIcon, IconName } from "lucide-react/dynamic";

export type { TableAction, TableActionGroup, ActionItem };

interface ActionCellProps {
  actions: AnyActionItem[];
  payload?: Record<string, any>;
}

export function ActionCell({ actions, payload }: ActionCellProps) {
  const [confirmAction, setConfirmAction] = useState<AnyAction | null>(null);
  const [openConfirmAction, setOpenConfirmAction] = useState<boolean>(false);

  function handleActionClick(action: AnyAction, e?: React.MouseEvent | Event) {
    if (e) e.stopPropagation();
    if (action.disabled) return;

    if (action.confirm) {
      setConfirmAction(action);
      setOpenConfirmAction(true);
    } else {
      execute(action, payload);
    }
  }

  function handleConfirmExecute() {
    if (confirmAction) {
      execute(confirmAction, payload);
      setOpenConfirmAction(false);
    }
  }

  return (
    <>
      <div className="flex items-center justify-end gap-1">
        {actions.map((item, idx) =>
          item.type === "group" ? (
            <ActionGroupDropdown
              key={idx}
              group={item}
              onActionClick={handleActionClick}
            />
          ) : (
            <ActionButton
              key={item.key}
              action={item}
              onActionClick={handleActionClick}
            />
          ),
        )}
      </div>

      <ConfirmActionDialog
        action={confirmAction}
        open={openConfirmAction}
        onOpenChange={setOpenConfirmAction}
        onConfirm={handleConfirmExecute}
      />
    </>
  );
}

function ActionButton({
  action,
  onActionClick,
}: {
  action: AnyAction;
  onActionClick: (a: AnyAction, e: React.MouseEvent) => void;
}) {
  return (
    <Button
      size={action.icon_only ? "icon" : "sm"}
      onClick={(e) => onActionClick(action, e)}
      disabled={action.disabled}
      title={action.label}
      variant={action.variant}
    >
      {action.icon && !action.text_only && <Icon name={action.icon} />}
      {!action.icon_only && (
        <span
          className={
            action.icon_only_on_mobile && action.icon
              ? "hidden md:inline-block"
              : ""
          }
        >
          {action.label}
        </span>
      )}
      {action.icon_only && <span className="sr-only">{action.label}</span>}
    </Button>
  );
}

function ActionGroupDropdown({
  group,
  onActionClick,
}: {
  group: AnyActionGroup;
  onActionClick: (a: AnyAction, e: Event) => void;
}) {
  const normalActions = group.actions.filter(
    (a) => a.variant !== "destructive",
  );
  const dangerActions = group.actions.filter(
    (a) => a.variant === "destructive",
  );
  const hasSeparator = normalActions.length > 0 && dangerActions.length > 0;

  return (
    <DropdownMenu>
      <DropdownMenuTrigger
        render={
          <Button
            variant={group.variant}
            size={group.icon_only ? "icon" : "sm"}
          >
            {group.icon && !group.text_only && <Icon name={group.icon} />}
            {!group.icon_only && (
              <span
                className={
                  group.icon_only_on_mobile && group.icon
                    ? "hidden md:inline-block"
                    : ""
                }
              >
                {group.label}
              </span>
            )}
            {group.icon_only && <span className="sr-only">{group.label}</span>}
          </Button>
        }
      ></DropdownMenuTrigger>
      <DropdownMenuContent align="end" className="w-40">
        {normalActions.map((action) => (
          <DropdownMenuItem
            onClick={(e: any) => onActionClick(action, e)}
            key={action.key}
            disabled={action.disabled}
            variant={action.variant}
          >
            {action.icon && <Icon name={action.icon} />}
            {action.label}
          </DropdownMenuItem>
        ))}

        {hasSeparator && <DropdownMenuSeparator />}

        {dangerActions.map((action) => (
          <DropdownMenuItem
            onClick={(e: any) => onActionClick(action, e)}
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

function execute(action: AnyAction, payload?: Record<string, any>) {
  if (!action.href) return;
  if (action.method === "get") {
    router.visit(action.href, { data: payload });
  } else {
    router.visit(action.href, { method: action.method, data: payload });
  }
}

interface IconProps {
  name: string;
  size?: number;
}

export function Icon({ name, size = 16 }: IconProps) {
  return <DynamicIcon name={name as IconName} size={size} />;
}
