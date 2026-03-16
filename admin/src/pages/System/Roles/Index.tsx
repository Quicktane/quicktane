import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef } from "@tanstack/react-table";
import { Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { Role } from "@/types/auth";
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

function DeleteRoleButton({
  roleId,
  name,
  isSystem,
}: {
  roleId: number;
  name: string;
  isSystem: boolean;
}) {
  const [isDeleting, setIsDeleting] = useState(false);

  const handleDelete = async () => {
    setIsDeleting(true);

    try {
      await api.delete(`/admin/user/roles/${roleId}`);
      toast.success(`Role "${name}" deleted successfully`);
      window.dispatchEvent(new CustomEvent("system-role-deleted"));
    } catch {
      toast.error("Failed to delete role");
    } finally {
      setIsDeleting(false);
    }
  };

  if (isSystem) {
    return (
      <Button variant="ghost" size="icon" disabled title="System roles cannot be deleted">
        <Trash2 className="h-4 w-4 text-muted-foreground" />
      </Button>
    );
  }

  return (
    <AlertDialog>
      <AlertDialogTrigger asChild>
        <Button variant="ghost" size="icon">
          <Trash2 className="h-4 w-4 text-destructive" />
        </Button>
      </AlertDialogTrigger>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Delete Role</AlertDialogTitle>
          <AlertDialogDescription>
            Are you sure you want to delete the role "{name}"? This action
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

const columns: ColumnDef<Role>[] = [
  {
    accessorKey: "name",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Name" />
    ),
  },
  {
    accessorKey: "slug",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Slug" />
    ),
    cell: ({ row }) => (
      <code className="rounded bg-muted px-1 py-0.5 text-xs">
        {row.getValue("slug")}
      </code>
    ),
  },
  {
    accessorKey: "description",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Description" />
    ),
    cell: ({ row }) => {
      const description = row.getValue<string | null>("description");
      return description ? (
        <span>{description}</span>
      ) : (
        <span className="text-muted-foreground">—</span>
      );
    },
  },
  {
    accessorKey: "is_system",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Type" />
    ),
    cell: ({ row }) => (
      <Badge variant={row.getValue("is_system") ? "secondary" : "outline"}>
        {row.getValue("is_system") ? "System" : "Custom"}
      </Badge>
    ),
  },
  {
    id: "actions",
    header: "Actions",
    cell: function ActionsCell({ row }) {
      const role = row.original;

      return (
        <div className="flex items-center gap-2">
          <Button variant="ghost" size="icon" asChild>
            <Link to={`/system/roles/${role.id}`}>
              <Pencil className="h-4 w-4" />
            </Link>
          </Button>
          <DeleteRoleButton
            roleId={role.id}
            name={role.name}
            isSystem={role.is_system}
          />
        </div>
      );
    },
  },
];

export function RolesIndex() {
  const [roles, setRoles] = useState<Role[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  const loadRoles = useCallback(async () => {
    try {
      const response = await api.get<{ data: Role[] }>("/admin/user/roles");
      setRoles(response.data.data);
    } catch {
      toast.error("Failed to load roles");
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadRoles();

    const handleDeleted = () => loadRoles();
    window.addEventListener("system-role-deleted", handleDeleted);

    return () =>
      window.removeEventListener("system-role-deleted", handleDeleted);
  }, [loadRoles]);

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
        title="Roles"
        description="Manage admin panel roles and permissions"
        actions={
          <Button asChild>
            <Link to="/system/roles/create">
              <Plus className="mr-2 h-4 w-4" />
              New Role
            </Link>
          </Button>
        }
      />
      <DataTable columns={columns} data={roles} />
    </div>
  );
}
