import { BulkActionItem, BulkAction } from "@mdaushi/kinetics-core";
import { router } from "@inertiajs/react";
import { useState } from "react";
import { ConfirmActionDialog } from "./confirm-action-dialog";
import { ActionCell } from "./action-cell";

interface BulkActionPayload {
  ids: string[];
  select_all: boolean;
}

interface TableBulkActionBarProps {
  actions: BulkActionItem[];
  rowSelection: Record<string, boolean>;
  selectAllPages: boolean;
  onClear: () => void;
}

export function TableBulkActionBar({
  actions,
  rowSelection,
  selectAllPages,
  onClear,
}: TableBulkActionBarProps) {
  const [confirmAction, setConfirmAction] = useState<BulkAction | null>(null);

  const selectedCount = Object.keys(rowSelection).length;
  const hasSelection = selectedCount > 0 || selectAllPages;

  if (!hasSelection || actions.length === 0) return null;

  const payload: BulkActionPayload = {
    ids: Object.keys(rowSelection),
    select_all: selectAllPages,
  };

  function execute(action: BulkAction) {
    if (!action.href) return;
    router.visit(action.href, {
      method: action.method,
      data: payload as Record<string, any>,
    });
    onClear();
  }

  return (
    <>
      <div className="fixed bottom-8 left-1/2 z-50 -translate-x-1/2 animate-in fade-in slide-in-from-bottom-5 duration-300">
        <div className="flex items-center gap-1.5 rounded-2xl border border-black/5 bg-background/60 px-2 py-1.5 shadow-[0_8px_30px_rgb(0,0,0,0.12)] backdrop-blur-xl backdrop-saturate-150 dark:border-white/10 dark:bg-background/40">
          <span className="select-none pl-3 pr-1 text-[13px] font-medium tracking-tight text-foreground/80">
            Actions
          </span>
          <div className="h-5 w-px bg-border/50 mx-1" />
          <div className="flex items-center gap-1">
            <ActionCell actions={actions} payload={payload} />
          </div>
        </div>
      </div>
      {/* Confirm dialog */}
      <ConfirmActionDialog
        action={confirmAction}
        open={!!confirmAction}
        onOpenChange={(open) => !open && setConfirmAction(null)}
        onConfirm={() => {
          if (confirmAction) execute(confirmAction);
          setConfirmAction(null);
        }}
      />
    </>
  );
}
