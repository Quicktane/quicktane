import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef } from "@tanstack/react-table";
import { Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { TaxRate } from "@/types/tax";
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

function DeleteTaxRateButton({
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
      await api.delete(`/admin/tax/rates/${uuid}`);
      toast.success(`Tax rate "${name}" deleted`);
      onDeleted();
    } catch {
      toast.error("Failed to delete tax rate");
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
          <AlertDialogTitle>Delete Tax Rate</AlertDialogTitle>
          <AlertDialogDescription>
            Are you sure you want to delete the tax rate "{name}"? This action
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

export function TaxRatesIndex() {
  const [taxRates, setTaxRates] = useState<TaxRate[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  const loadTaxRates = useCallback(async () => {
    try {
      const response = await api.get<{ data: TaxRate[] }>("/admin/tax/rates");
      setTaxRates(response.data.data);
    } catch {
      toast.error("Failed to load tax rates");
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadTaxRates();
  }, [loadTaxRates]);

  const columns: ColumnDef<TaxRate>[] = [
    {
      accessorKey: "name",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Name" />
      ),
    },
    {
      id: "zone",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Zone" />
      ),
      cell: ({ row }) => row.original.zone?.name ?? "-",
    },
    {
      accessorKey: "rate",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Rate %" />
      ),
      cell: ({ row }) => `${row.getValue<string>("rate")}%`,
    },
    {
      accessorKey: "is_compound",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Compound" />
      ),
      cell: ({ row }) => (
        <Badge variant={row.getValue("is_compound") ? "default" : "outline"}>
          {row.getValue("is_compound") ? "Yes" : "No"}
        </Badge>
      ),
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
        const taxRate = row.original;

        return (
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon" asChild>
              <Link to={`/tax/rates/${taxRate.uuid}`}>
                <Pencil className="h-4 w-4" />
              </Link>
            </Button>
            <DeleteTaxRateButton
              uuid={taxRate.uuid}
              name={taxRate.name}
              onDeleted={loadTaxRates}
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
        title="Tax Rates"
        description="Manage tax rates for geographic zones"
        actions={
          <Button asChild>
            <Link to="/tax/rates/create">
              <Plus className="mr-2 h-4 w-4" />
              New Tax Rate
            </Link>
          </Button>
        }
      />
      <DataTable columns={columns} data={taxRates} />
    </div>
  );
}
