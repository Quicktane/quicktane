import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef } from "@tanstack/react-table";
import { Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { CartPriceRule } from "@/types/promotion";
import { PageHeader } from "@/components/PageHeader";
import { DataTable } from "@/components/data-table/DataTable";
import { DataTableColumnHeader } from "@/components/data-table/DataTableColumnHeader";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import { Skeleton } from "@/components/ui/skeleton";

const actionTypeLabels: Record<CartPriceRule["action_type"], string> = {
  by_percent: "Percent Discount",
  by_fixed: "Fixed Discount",
  buy_x_get_y: "Buy X Get Y",
  free_shipping: "Free Shipping",
};

function DeleteRuleButton({
  uuid,
  name,
  onDeleted,
}: {
  uuid: string;
  name: string;
  onDeleted: () => void;
}) {
  const [isDeleting, setIsDeleting] = useState(false);

  const handleDelete = async () => {
    setIsDeleting(true);

    try {
      await api.delete(`/admin/promotion/rules/${uuid}`);
      toast.success(`Rule "${name}" deleted`);
      onDeleted();
    } catch {
      toast.error("Failed to delete rule");
    } finally {
      setIsDeleting(false);
    }
  };

  return (
    <AlertDialog>
      <AlertDialogTrigger asChild>
        <Button variant="ghost" size="icon">
          <Trash2 className="h-4 w-4 text-destructive" />
        </Button>
      </AlertDialogTrigger>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Delete Price Rule</AlertDialogTitle>
          <AlertDialogDescription>
            Are you sure you want to delete the rule "{name}"? This action
            cannot be undone.
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel>Cancel</AlertDialogCancel>
          <AlertDialogAction onClick={handleDelete} disabled={isDeleting}>
            {isDeleting ? "Deleting..." : "Delete"}
          </AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  );
}

export function MarketingRulesIndex() {
  const [rules, setRules] = useState<CartPriceRule[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  const loadRules = useCallback(async () => {
    try {
      const response = await api.get<{ data: CartPriceRule[] }>(
        "/admin/promotion/rules",
      );
      setRules(response.data.data);
    } catch {
      toast.error("Failed to load price rules");
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadRules();
  }, [loadRules]);

  const columns: ColumnDef<CartPriceRule>[] = [
    {
      accessorKey: "name",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Name" />
      ),
    },
    {
      accessorKey: "action_type",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Action Type" />
      ),
      cell: ({ row }) => {
        const actionType = row.getValue<CartPriceRule["action_type"]>("action_type");
        return <Badge variant="secondary">{actionTypeLabels[actionType]}</Badge>;
      },
    },
    {
      accessorKey: "is_active",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Active" />
      ),
      cell: ({ row }) => (
        <Badge variant={row.getValue("is_active") ? "default" : "outline"}>
          {row.getValue("is_active") ? "Active" : "Inactive"}
        </Badge>
      ),
    },
    {
      accessorKey: "times_used",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Times Used" />
      ),
    },
    {
      accessorKey: "priority",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Priority" />
      ),
    },
    {
      id: "actions",
      header: "Actions",
      cell: function ActionsCell({ row }) {
        const rule = row.original;

        return (
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon" asChild>
              <Link to={`/marketing/rules/${rule.uuid}`}>
                <Pencil className="h-4 w-4" />
              </Link>
            </Button>
            <DeleteRuleButton
              uuid={rule.uuid}
              name={rule.name}
              onDeleted={loadRules}
            />
          </div>
        );
      },
    },
  ];

  if (isLoading) {
    return (
      <div className="space-y-4">
        <Skeleton className="h-8 w-48" />
        <Skeleton className="h-64 w-full" />
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <PageHeader
        title="Cart Price Rules"
        description="Manage promotional discount rules"
        actions={
          <Button asChild>
            <Link to="/marketing/rules/create">
              <Plus className="mr-2 h-4 w-4" />
              New Rule
            </Link>
          </Button>
        }
      />
      <DataTable columns={columns} data={rules} />
    </div>
  );
}
