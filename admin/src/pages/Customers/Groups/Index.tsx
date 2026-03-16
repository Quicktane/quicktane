import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef, type PaginationState } from "@tanstack/react-table";
import { Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { CustomerGroup } from "@/types/customer";
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
import { Card, CardContent } from "@/components/ui/card";

function DeleteGroupButton({
  uuid,
  name,
  isDefault,
}: {
  uuid: string;
  name: string;
  isDefault: boolean;
}) {
  const [isDeleting, setIsDeleting] = useState(false);

  const handleDelete = async () => {
    setIsDeleting(true);

    try {
      await api.delete(`/admin/customer/groups/${uuid}`);
      toast.success(`Group "${name}" deleted successfully`);
      window.dispatchEvent(new CustomEvent("customer-group-deleted"));
    } catch {
      toast.error("Failed to delete group");
    } finally {
      setIsDeleting(false);
    }
  };

  if (isDefault) {
    return null;
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
          <AlertDialogTitle>Delete Customer Group</AlertDialogTitle>
          <AlertDialogDescription>
            Are you sure you want to delete the group "{name}"? This action
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

const columns: ColumnDef<CustomerGroup>[] = [
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
    accessorKey: "is_default",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Default" />
    ),
    cell: ({ row }) =>
      row.getValue("is_default") ? (
        <Badge variant="default">Default</Badge>
      ) : null,
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
      const group = row.original;

      return (
        <div className="flex items-center gap-2">
          <Button variant="ghost" size="icon" asChild>
            <Link to={`/customers/groups/${group.uuid}`}>
              <Pencil className="h-4 w-4" />
            </Link>
          </Button>
          <DeleteGroupButton
            uuid={group.uuid}
            name={group.name}
            isDefault={group.is_default}
          />
        </div>
      );
    },
  },
];

export function CustomerGroupsIndex() {
  const [groups, setGroups] = useState<CustomerGroup[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(false);
  const [pagination, setPagination] = useState<PaginationState>({
    pageIndex: 0,
    pageSize: 15,
  });
  const [pageCount, setPageCount] = useState(0);

  const loadGroups = useCallback(async () => {
    setIsLoading(true);
    setError(false);

    try {
      const response = await api.get<{
        data: CustomerGroup[];
        meta: { last_page: number };
      }>("/admin/customer/groups", {
        params: {
          page: pagination.pageIndex + 1,
          per_page: pagination.pageSize,
        },
      });

      setGroups(response.data.data);
      setPageCount(response.data.meta.last_page);
    } catch {
      setError(true);
      toast.error("Failed to load customer groups");
    } finally {
      setIsLoading(false);
    }
  }, [pagination.pageIndex, pagination.pageSize]);

  useEffect(() => {
    loadGroups();

    const handleDeleted = () => loadGroups();
    window.addEventListener("customer-group-deleted", handleDeleted);

    return () =>
      window.removeEventListener("customer-group-deleted", handleDeleted);
  }, [loadGroups]);

  if (isLoading && groups.length === 0) {
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
        title="Customer Groups"
        description="Manage customer groups and segmentation"
        actions={
          <Button asChild>
            <Link to="/customers/groups/create">
              <Plus className="mr-2 h-4 w-4" />
              New Group
            </Link>
          </Button>
        }
      />
      {error && (
        <Card className="border-destructive">
          <CardContent className="flex items-center justify-between p-4">
            <p className="text-sm text-destructive">
              Failed to load customer groups. Please check the server connection
              and try again.
            </p>
            <Button variant="outline" size="sm" onClick={loadGroups}>
              Retry
            </Button>
          </CardContent>
        </Card>
      )}
      <DataTable
        columns={columns}
        data={groups}
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
