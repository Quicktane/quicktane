import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef } from "@tanstack/react-table";
import { Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { TaxRule } from "@/types/tax";
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

function DeleteTaxRuleButton({
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
      await api.delete(`/admin/tax/rules/${uuid}`);
      toast.success(`Tax rule "${name}" deleted`);
      onDeleted();
    } catch {
      toast.error("Failed to delete tax rule");
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
          <AlertDialogTitle>Delete Tax Rule</AlertDialogTitle>
          <AlertDialogDescription>
            Are you sure you want to delete the tax rule "{name}"? This action
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

export function TaxRulesIndex() {
  const [taxRules, setTaxRules] = useState<TaxRule[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  const loadTaxRules = useCallback(async () => {
    try {
      const response = await api.get<{ data: TaxRule[] }>("/admin/tax/rules");
      setTaxRules(response.data.data);
    } catch {
      toast.error("Failed to load tax rules");
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadTaxRules();
  }, [loadTaxRules]);

  const columns: ColumnDef<TaxRule>[] = [
    {
      accessorKey: "name",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Name" />
      ),
    },
    {
      id: "rate",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Rate" />
      ),
      cell: ({ row }) => row.original.tax_rate?.name ?? "-",
    },
    {
      id: "product_tax_class",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Product Tax Class" />
      ),
      cell: ({ row }) => row.original.product_tax_class?.name ?? "-",
    },
    {
      id: "customer_tax_class",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Customer Tax Class" />
      ),
      cell: ({ row }) => row.original.customer_tax_class?.name ?? "-",
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
      id: "actions",
      header: "Actions",
      cell: function ActionsCell({ row }) {
        const taxRule = row.original;

        return (
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon" asChild>
              <Link to={`/tax/rules/${taxRule.uuid}`}>
                <Pencil className="h-4 w-4" />
              </Link>
            </Button>
            <DeleteTaxRuleButton
              uuid={taxRule.uuid}
              name={taxRule.name}
              onDeleted={loadTaxRules}
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
        title="Tax Rules"
        description="Manage tax rules linking rates to product and customer classes"
        actions={
          <Button asChild>
            <Link to="/tax/rules/create">
              <Plus className="mr-2 h-4 w-4" />
              New Tax Rule
            </Link>
          </Button>
        }
      />
      <DataTable columns={columns} data={taxRules} />
    </div>
  );
}
