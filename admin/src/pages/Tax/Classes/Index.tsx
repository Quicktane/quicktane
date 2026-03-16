import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef } from "@tanstack/react-table";
import { Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { TaxClass } from "@/types/tax";
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

function DeleteTaxClassButton({
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
      await api.delete(`/admin/tax/classes/${uuid}`);
      toast.success(`Tax class "${name}" deleted`);
      onDeleted();
    } catch {
      toast.error("Failed to delete tax class");
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
          <AlertDialogTitle>Delete Tax Class</AlertDialogTitle>
          <AlertDialogDescription>
            Are you sure you want to delete the tax class "{name}"? This action
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

export function TaxClassesIndex() {
  const [taxClasses, setTaxClasses] = useState<TaxClass[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  const loadTaxClasses = useCallback(async () => {
    try {
      const response = await api.get<{ data: TaxClass[] }>("/admin/tax/classes");
      setTaxClasses(response.data.data);
    } catch {
      toast.error("Failed to load tax classes");
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadTaxClasses();
  }, [loadTaxClasses]);

  const columns: ColumnDef<TaxClass>[] = [
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
      cell: ({ row }) => {
        const type = row.getValue<string>("type");
        return (
          <Badge variant={type === "product" ? "default" : "secondary"}>
            {type.charAt(0).toUpperCase() + type.slice(1)}
          </Badge>
        );
      },
    },
    {
      accessorKey: "is_default",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Default" />
      ),
      cell: ({ row }) => (
        <Badge variant={row.getValue("is_default") ? "default" : "outline"}>
          {row.getValue("is_default") ? "Default" : "No"}
        </Badge>
      ),
    },
    {
      id: "actions",
      header: "Actions",
      cell: function ActionsCell({ row }) {
        const taxClass = row.original;

        return (
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon" asChild>
              <Link to={`/tax/classes/${taxClass.uuid}`}>
                <Pencil className="h-4 w-4" />
              </Link>
            </Button>
            <DeleteTaxClassButton
              uuid={taxClass.uuid}
              name={taxClass.name}
              onDeleted={loadTaxClasses}
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
        title="Tax Classes"
        description="Manage product and customer tax classifications"
        actions={
          <Button asChild>
            <Link to="/tax/classes/create">
              <Plus className="mr-2 h-4 w-4" />
              New Tax Class
            </Link>
          </Button>
        }
      />
      <DataTable columns={columns} data={taxClasses} />
    </div>
  );
}
