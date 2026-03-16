import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef, type PaginationState } from "@tanstack/react-table";
import { Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { User } from "@/types/auth";
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

function DeleteUserButton({
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
      await api.delete(`/admin/user/users/${uuid}`);
      toast.success(`User "${name}" deleted successfully`);
      window.dispatchEvent(new CustomEvent("system-user-deleted"));
    } catch {
      toast.error("Failed to delete user");
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
          <AlertDialogTitle>Delete User</AlertDialogTitle>
          <AlertDialogDescription>
            Are you sure you want to delete the user "{name}"? This action
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

const columns: ColumnDef<User>[] = [
  {
    id: "name",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Name" />
    ),
    cell: ({ row }) => {
      const user = row.original;
      return `${user.first_name} ${user.last_name}`;
    },
  },
  {
    accessorKey: "email",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Email" />
    ),
  },
  {
    id: "role",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Role" />
    ),
    cell: ({ row }) => {
      const role = row.original.role;
      return role ? (
        <Badge variant="secondary">{role.name}</Badge>
      ) : (
        <span className="text-muted-foreground">—</span>
      );
    },
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
    accessorKey: "last_login_at",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Last Login" />
    ),
    cell: ({ row }) => {
      const value = row.getValue<string | null>("last_login_at");
      if (!value) return <span className="text-muted-foreground">Never</span>;
      return new Date(value).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      });
    },
  },
  {
    id: "actions",
    header: "Actions",
    cell: function ActionsCell({ row }) {
      const user = row.original;
      const fullName = `${user.first_name} ${user.last_name}`;

      return (
        <div className="flex items-center gap-2">
          <Button variant="ghost" size="icon" asChild>
            <Link to={`/system/users/${user.uuid}`}>
              <Pencil className="h-4 w-4" />
            </Link>
          </Button>
          <DeleteUserButton uuid={user.uuid} name={fullName} />
        </div>
      );
    },
  },
];

export function UsersIndex() {
  const [users, setUsers] = useState<User[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [pagination, setPagination] = useState<PaginationState>({
    pageIndex: 0,
    pageSize: 15,
  });
  const [pageCount, setPageCount] = useState(0);

  const loadUsers = useCallback(async () => {
    setIsLoading(true);

    try {
      const response = await api.get<{
        data: User[];
        meta: { last_page: number };
      }>("/admin/user/users", {
        params: {
          page: pagination.pageIndex + 1,
          per_page: pagination.pageSize,
        },
      });

      setUsers(response.data.data);
      setPageCount(response.data.meta.last_page);
    } catch {
      toast.error("Failed to load users");
    } finally {
      setIsLoading(false);
    }
  }, [pagination.pageIndex, pagination.pageSize]);

  useEffect(() => {
    loadUsers();

    const handleDeleted = () => loadUsers();
    window.addEventListener("system-user-deleted", handleDeleted);

    return () =>
      window.removeEventListener("system-user-deleted", handleDeleted);
  }, [loadUsers]);

  if (isLoading && users.length === 0) {
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
        title="Admin Users"
        description="Manage admin panel user accounts"
        actions={
          <Button asChild>
            <Link to="/system/users/create">
              <Plus className="mr-2 h-4 w-4" />
              New User
            </Link>
          </Button>
        }
      />
      <DataTable
        columns={columns}
        data={users}
        pagination={{
          pageIndex: pagination.pageIndex,
          pageSize: pagination.pageSize,
          pageCount,
          onPaginationChange: setPagination,
        }}
      />
    </div>
  );
}
