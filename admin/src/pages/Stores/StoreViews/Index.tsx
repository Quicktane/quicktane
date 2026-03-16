import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef } from "@tanstack/react-table";
import { Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { StoreView } from "@/types/store";
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

function DeleteStoreViewButton({
  uuid,
  name,
}: {
  uuid: string;
  name: string;
}) {
  const [isDeleting, setIsDeleting] = useState(false);

  const handleDelete = async () => {
    setIsDeleting(true);

    try {
      await api.delete(`/admin/store/store-views/${uuid}`);
      toast.success(`Store view "${name}" deleted successfully`);
      window.dispatchEvent(new CustomEvent("store-view-deleted"));
    } catch {
      toast.error("Failed to delete store view");
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
          <AlertDialogTitle>Delete Store View</AlertDialogTitle>
          <AlertDialogDescription>
            Are you sure you want to delete the store view "{name}"? This action
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

const columns: ColumnDef<StoreView>[] = [
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
  },
  {
    accessorKey: "locale",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Locale" />
    ),
    cell: ({ row }) => row.getValue("locale") ?? "-",
  },
  {
    accessorKey: "sort_order",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Sort Order" />
    ),
  },
  {
    accessorKey: "is_active",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Status" />
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
      const storeView = row.original;

      return (
        <div className="flex items-center gap-2">
          <Button variant="ghost" size="icon" asChild>
            <Link to={`/stores/store-views/${storeView.uuid}`}>
              <Pencil className="h-4 w-4" />
            </Link>
          </Button>
          <DeleteStoreViewButton uuid={storeView.uuid} name={storeView.name} />
        </div>
      );
    },
  },
];

export function StoreViewsIndex() {
  const [storeViews, setStoreViews] = useState<StoreView[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  const loadStoreViews = useCallback(async () => {
    try {
      const response = await api.get<{ data: StoreView[] }>(
        "/admin/store/store-views",
      );
      setStoreViews(response.data.data);
    } catch {
      toast.error("Failed to load store views");
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadStoreViews();

    const handleDeleted = () => loadStoreViews();
    window.addEventListener("store-view-deleted", handleDeleted);

    return () =>
      window.removeEventListener("store-view-deleted", handleDeleted);
  }, [loadStoreViews]);

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
        title="Store Views"
        description="Manage store views for different locales and storefronts"
        actions={
          <Button asChild>
            <Link to="/stores/store-views/create">
              <Plus className="mr-2 h-4 w-4" />
              New Store View
            </Link>
          </Button>
        }
      />
      <DataTable columns={columns} data={storeViews} />
    </div>
  );
}
