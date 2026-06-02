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
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "./ui/dropdown-menu";
import { DynamicIcon, IconName } from "lucide-react/dynamic";
import { useState } from "react";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "./ui/alert-dialog";

export type { TableAction, TableActionGroup, ActionItem };

interface ActionCellProps {
  actions: ActionItem[];
}

export function ActionCell({ actions }: ActionCellProps) {
  const [confirmAction, setConfirmAction] = useState<TableAction | null>(null);
  const [openConfirmAction, setOpenConfirmAction] = useState<boolean>(false);

  function handleActionClick(
    action: TableAction,
    e?: React.MouseEvent | Event,
  ) {
    if (e) e.stopPropagation();
    if (action.disabled) return;

    if (action.confirm) {
      setConfirmAction(action);
      setOpenConfirmAction(true);
    } else {
      execute(action);
    }
  }

  function handleConfirmExecute() {
    if (confirmAction) {
      execute(confirmAction);
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

      <AlertDialog open={openConfirmAction} onOpenChange={setOpenConfirmAction}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>
              {confirmAction?.confirm?.title ?? "Konfirmasi"}
            </AlertDialogTitle>
            <AlertDialogDescription>
              {confirmAction?.confirm?.message}
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction
              variant={confirmAction?.variant}
              onClick={handleConfirmExecute}
            >
              Continue
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </>
  );
}

function ActionButton({
  action,
  onActionClick,
}: {
  action: TableAction;
  onActionClick: (a: TableAction, e: React.MouseEvent) => void;
}) {
  return (
    <Button
      size={"sm"}
      onClick={(e) => onActionClick(action, e)}
      disabled={action.disabled}
      title={action.label}
      variant={action.variant}
    >
      {action.icon && <Icon name={action.icon} />}
      <span className="sr-only sm:not-sr-only">{action.label}</span>
    </Button>
  );
}

function ActionGroupDropdown({
  group,
  onActionClick,
}: {
  group: TableActionGroup;
  onActionClick: (a: TableAction, e: Event) => void;
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
          <Button variant="ghost" size="icon">
            {group.icon && <Icon name={group.icon} />}
            <span className="sr-only">Open menu</span>
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
