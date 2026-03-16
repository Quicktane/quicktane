import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef } from "@tanstack/react-table";
import { Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { ShippingMethod } from "@/types/shipping";
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

function DeleteShippingMethodButton({
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
      await api.delete(`/admin/shipping/methods/${uuid}`);
      toast.success(`Shipping method "${name}" deleted`);
      onDeleted();
    } catch {
      toast.error("Failed to delete shipping method");
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
          <AlertDialogTitle>Delete Shipping Method</AlertDialogTitle>
          <AlertDialogDescription>
            Are you sure you want to delete the shipping method "{name}"? This
            action cannot be undone.
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

export function ShippingMethodsIndex() {
  const [shippingMethods, setShippingMethods] = useState<ShippingMethod[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  const loadShippingMethods = useCallback(async () => {
    try {
      const response = await api.get<{ data: ShippingMethod[] }>(
        "/admin/shipping/methods",
      );
      setShippingMethods(response.data.data);
    } catch {
      toast.error("Failed to load shipping methods");
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadShippingMethods();
  }, [loadShippingMethods]);

  const columns: ColumnDef<ShippingMethod>[] = [
    {
      accessorKey: "name",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Name" />
      ),
    },
    {
      accessorKey: "code",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Code" />
      ),
      cell: ({ row }) => (
        <span className="font-mono text-sm">{row.getValue<string>("code")}</span>
      ),
    },
    {
      accessorKey: "carrier_code",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Carrier" />
      ),
      cell: ({ row }) => (
        <span className="font-mono text-sm">
          {row.getValue<string>("carrier_code")}
        </span>
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
        const method = row.original;

        return (
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon" asChild>
              <Link to={`/shipping/methods/${method.uuid}`}>
                <Pencil className="h-4 w-4" />
              </Link>
            </Button>
            <DeleteShippingMethodButton
              uuid={method.uuid}
              name={method.name}
              onDeleted={loadShippingMethods}
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
        title="Shipping Methods"
        description="Manage shipping carriers and delivery methods"
        actions={
          <Button asChild>
            <Link to="/shipping/methods/create">
              <Plus className="mr-2 h-4 w-4" />
              New Method
            </Link>
          </Button>
        }
      />
      <DataTable columns={columns} data={shippingMethods} />
    </div>
  );
}
