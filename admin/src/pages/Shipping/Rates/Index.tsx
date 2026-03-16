import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef } from "@tanstack/react-table";
import { Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { ShippingRate } from "@/types/shipping";
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

function DeleteShippingRateButton({
  uuid,
  onDeleted,
}: {
  uuid: string;
  onDeleted: () => void;
}) {
  const [isDeleting, setIsDeleting] = useState(false);

  const handleDelete = async () => {
    setIsDeleting(true);

    try {
      await api.delete(`/admin/shipping/rates/${uuid}`);
      toast.success("Shipping rate deleted");
      onDeleted();
    } catch {
      toast.error("Failed to delete shipping rate");
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
          <AlertDialogTitle>Delete Shipping Rate</AlertDialogTitle>
          <AlertDialogDescription>
            Are you sure you want to delete this shipping rate? This action
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

export function ShippingRatesIndex() {
  const [shippingRates, setShippingRates] = useState<ShippingRate[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  const loadShippingRates = useCallback(async () => {
    try {
      const response = await api.get<{ data: ShippingRate[] }>(
        "/admin/shipping/rates",
      );
      setShippingRates(response.data.data);
    } catch {
      toast.error("Failed to load shipping rates");
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadShippingRates();
  }, [loadShippingRates]);

  const columns: ColumnDef<ShippingRate>[] = [
    {
      id: "method",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Method" />
      ),
      cell: ({ row }) => row.original.method?.name ?? "-",
    },
    {
      id: "zone",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Zone" />
      ),
      cell: ({ row }) => row.original.zone?.name ?? "-",
    },
    {
      accessorKey: "price",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Price" />
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
        const rate = row.original;

        return (
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon" asChild>
              <Link to={`/shipping/rates/${rate.uuid}`}>
                <Pencil className="h-4 w-4" />
              </Link>
            </Button>
            <DeleteShippingRateButton
              uuid={rate.uuid}
              onDeleted={loadShippingRates}
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
        title="Shipping Rates"
        description="Manage pricing for shipping methods by zone"
        actions={
          <Button asChild>
            <Link to="/shipping/rates/create">
              <Plus className="mr-2 h-4 w-4" />
              New Rate
            </Link>
          </Button>
        }
      />
      <DataTable columns={columns} data={shippingRates} />
    </div>
  );
}
