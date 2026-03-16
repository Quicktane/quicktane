import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef } from "@tanstack/react-table";
import { Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { Attribute } from "@/types/catalog";
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

const columns: ColumnDef<Attribute>[] = [
  {
    accessorKey: "code",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Code" />
    ),
  },
  {
    accessorKey: "name",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Name" />
    ),
  },
  {
    accessorKey: "type",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Type" />
    ),
    cell: ({ row }) => (
      <Badge variant="secondary">{row.getValue("type")}</Badge>
    ),
  },
  {
    accessorKey: "is_required",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Required" />
    ),
    cell: ({ row }) => (
      <Badge variant={row.getValue("is_required") ? "default" : "outline"}>
        {row.getValue("is_required") ? "Yes" : "No"}
      </Badge>
    ),
  },
  {
    accessorKey: "is_filterable",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Filterable" />
    ),
    cell: ({ row }) => (
      <Badge variant={row.getValue("is_filterable") ? "default" : "outline"}>
        {row.getValue("is_filterable") ? "Yes" : "No"}
      </Badge>
    ),
  },
  {
    id: "actions",
    header: "Actions",
    cell: function ActionsCell({ row }) {
      const attribute = row.original;

      return (
        <div className="flex items-center gap-2">
          <Button variant="ghost" size="icon" asChild>
            <Link to={`/catalog/attributes/${attribute.uuid}`}>
              <Pencil className="h-4 w-4" />
            </Link>
          </Button>
          <DeleteAttributeButton
            uuid={attribute.uuid}
            name={attribute.name}
          />
        </div>
      );
    },
  },
];

function DeleteAttributeButton({
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
      await api.delete(`/admin/catalog/attributes/${uuid}`);
      toast.success(`Attribute "${name}" deleted successfully`);
      window.dispatchEvent(new CustomEvent("attribute-deleted"));
    } catch {
      toast.error("Failed to delete attribute");
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
          <AlertDialogTitle>Delete Attribute</AlertDialogTitle>
          <AlertDialogDescription>
            Are you sure you want to delete the attribute "{name}"? This action
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

export function AttributesIndex() {
  const [attributes, setAttributes] = useState<Attribute[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  const loadAttributes = useCallback(async () => {
    try {
      const response = await api.get<{ data: Attribute[] }>(
        "/admin/catalog/attributes",
      );
      setAttributes(response.data.data);
    } catch {
      toast.error("Failed to load attributes");
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadAttributes();

    const handleDeleted = () => loadAttributes();
    window.addEventListener("attribute-deleted", handleDeleted);

    return () => window.removeEventListener("attribute-deleted", handleDeleted);
  }, [loadAttributes]);

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
        title="Attributes"
        description="Manage product attributes"
        actions={
          <Button asChild>
            <Link to="/catalog/attributes/create">
              <Plus className="mr-2 h-4 w-4" />
              New Attribute
            </Link>
          </Button>
        }
      />
      <DataTable columns={columns} data={attributes} />
    </div>
  );
}
