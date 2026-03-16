import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef } from "@tanstack/react-table";
import { Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { TaxZone } from "@/types/tax";
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

function DeleteTaxZoneButton({
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
      await api.delete(`/admin/tax/zones/${uuid}`);
      toast.success(`Tax zone "${name}" deleted`);
      onDeleted();
    } catch {
      toast.error("Failed to delete tax zone");
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
          <AlertDialogTitle>Delete Tax Zone</AlertDialogTitle>
          <AlertDialogDescription>
            Are you sure you want to delete the tax zone "{name}"? This action
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

export function TaxZonesIndex() {
  const [taxZones, setTaxZones] = useState<TaxZone[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  const loadTaxZones = useCallback(async () => {
    try {
      const response = await api.get<{ data: TaxZone[] }>("/admin/tax/zones");
      setTaxZones(response.data.data);
    } catch {
      toast.error("Failed to load tax zones");
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadTaxZones();
  }, [loadTaxZones]);

  const columns: ColumnDef<TaxZone>[] = [
    {
      accessorKey: "name",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Name" />
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
      accessorKey: "sort_order",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Sort Order" />
      ),
    },
    {
      id: "actions",
      header: "Actions",
      cell: function ActionsCell({ row }) {
        const taxZone = row.original;

        return (
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon" asChild>
              <Link to={`/tax/zones/${taxZone.uuid}`}>
                <Pencil className="h-4 w-4" />
              </Link>
            </Button>
            <DeleteTaxZoneButton
              uuid={taxZone.uuid}
              name={taxZone.name}
              onDeleted={loadTaxZones}
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
        title="Tax Zones"
        description="Manage geographic tax zones"
        actions={
          <Button asChild>
            <Link to="/tax/zones/create">
              <Plus className="mr-2 h-4 w-4" />
              New Tax Zone
            </Link>
          </Button>
        }
      />
      <DataTable columns={columns} data={taxZones} />
    </div>
  );
}
