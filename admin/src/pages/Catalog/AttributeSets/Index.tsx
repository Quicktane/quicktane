import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef } from "@tanstack/react-table";
import { Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { AttributeSet } from "@/types/catalog";
import { PageHeader } from "@/components/PageHeader";
import { DataTable } from "@/components/data-table/DataTable";
import { DataTableColumnHeader } from "@/components/data-table/DataTableColumnHeader";
import { Button } from "@/components/ui/button";
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

const columns: ColumnDef<AttributeSet>[] = [
  {
    accessorKey: "name",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Name" />
    ),
  },
  {
    accessorKey: "sort_order",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Sort Order" />
    ),
  },
  {
    id: "attributes_count",
    header: "Attributes",
    cell: ({ row }) => {
      const attributeSet = row.original;
      return attributeSet.attributes?.length ?? 0;
    },
  },
  {
    id: "actions",
    header: "Actions",
    cell: function ActionsCell({ row }) {
      const attributeSet = row.original;

      return (
        <div className="flex items-center gap-2">
          <Button variant="ghost" size="icon" asChild>
            <Link to={`/catalog/attribute-sets/${attributeSet.uuid}`}>
              <Pencil className="h-4 w-4" />
            </Link>
          </Button>
          <DeleteAttributeSetButton
            uuid={attributeSet.uuid}
            name={attributeSet.name}
          />
        </div>
      );
    },
  },
];

function DeleteAttributeSetButton({
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
      await api.delete(`/admin/catalog/attribute-sets/${uuid}`);
      toast.success(`Attribute set "${name}" deleted successfully`);
      window.dispatchEvent(new CustomEvent("attribute-set-deleted"));
    } catch {
      toast.error("Failed to delete attribute set");
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
          <AlertDialogTitle>Delete Attribute Set</AlertDialogTitle>
          <AlertDialogDescription>
            Are you sure you want to delete the attribute set "{name}"? This
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

export function AttributeSetsIndex() {
  const [attributeSets, setAttributeSets] = useState<AttributeSet[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  const loadAttributeSets = useCallback(async () => {
    try {
      const response = await api.get<{ data: AttributeSet[] }>(
        "/admin/catalog/attribute-sets",
      );
      setAttributeSets(response.data.data);
    } catch {
      toast.error("Failed to load attribute sets");
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadAttributeSets();

    const handleDeleted = () => loadAttributeSets();
    window.addEventListener("attribute-set-deleted", handleDeleted);

    return () =>
      window.removeEventListener("attribute-set-deleted", handleDeleted);
  }, [loadAttributeSets]);

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
        title="Attribute Sets"
        description="Manage attribute sets for products"
        actions={
          <Button asChild>
            <Link to="/catalog/attribute-sets/create">
              <Plus className="mr-2 h-4 w-4" />
              New Attribute Set
            </Link>
          </Button>
        }
      />
      <DataTable columns={columns} data={attributeSets} />
    </div>
  );
}
