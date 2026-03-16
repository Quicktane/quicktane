import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef, type PaginationState } from "@tanstack/react-table";
import { Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { Customer } from "@/types/customer";
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

function DeleteCustomerButton({
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
      await api.delete(`/admin/customer/customers/${uuid}`);
      toast.success(`Customer "${name}" deleted successfully`);
      window.dispatchEvent(new CustomEvent("customer-deleted"));
    } catch {
      toast.error("Failed to delete customer");
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
          <AlertDialogTitle>Delete Customer</AlertDialogTitle>
          <AlertDialogDescription>
            Are you sure you want to delete the customer "{name}"? This action
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

const columns: ColumnDef<Customer>[] = [
  {
    id: "name",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Name" />
    ),
    cell: ({ row }) => {
      const customer = row.original;
      return `${customer.first_name} ${customer.last_name}`;
    },
  },
  {
    accessorKey: "email",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Email" />
    ),
  },
  {
    id: "group",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Group" />
    ),
    cell: ({ row }) => {
      const group = row.original.group;
      return group ? (
        <Badge variant="secondary">{group.name}</Badge>
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
      });
    },
  },
  {
    accessorKey: "created_at",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Registered" />
    ),
    cell: ({ row }) =>
      new Date(row.getValue<string>("created_at")).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
      }),
  },
  {
    id: "actions",
    header: "Actions",
    cell: function ActionsCell({ row }) {
      const customer = row.original;
      const fullName = `${customer.first_name} ${customer.last_name}`;

      return (
        <div className="flex items-center gap-2">
          <Button variant="ghost" size="icon" asChild>
            <Link to={`/customers/${customer.uuid}`}>
              <Pencil className="h-4 w-4" />
            </Link>
          </Button>
          <DeleteCustomerButton uuid={customer.uuid} name={fullName} />
        </div>
      );
    },
  },
];

export function CustomersIndex() {
  const [customers, setCustomers] = useState<Customer[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(false);
  const [pagination, setPagination] = useState<PaginationState>({
    pageIndex: 0,
    pageSize: 15,
  });
  const [pageCount, setPageCount] = useState(0);

  const loadCustomers = useCallback(async () => {
    setIsLoading(true);
    setError(false);

    try {
      const response = await api.get<{
        data: Customer[];
        meta: { last_page: number };
      }>("/admin/customer/customers", {
        params: {
          page: pagination.pageIndex + 1,
          per_page: pagination.pageSize,
        },
      });

      setCustomers(response.data.data);
      setPageCount(response.data.meta.last_page);
    } catch {
      setError(true);
      toast.error("Failed to load customers");
    } finally {
      setIsLoading(false);
    }
  }, [pagination.pageIndex, pagination.pageSize]);

  useEffect(() => {
    loadCustomers();

    const handleDeleted = () => loadCustomers();
    window.addEventListener("customer-deleted", handleDeleted);

    return () => window.removeEventListener("customer-deleted", handleDeleted);
  }, [loadCustomers]);

  if (isLoading && customers.length === 0) {
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
        title="Customers"
        description="Manage storefront customer accounts"
        actions={
          <Button asChild>
            <Link to="/customers/create">
              <Plus className="mr-2 h-4 w-4" />
              New Customer
            </Link>
          </Button>
        }
      />
      {error && (
        <Card className="border-destructive">
          <CardContent className="flex items-center justify-between p-4">
            <p className="text-sm text-destructive">
              Failed to load customers. Please check the server connection and
              try again.
            </p>
            <Button variant="outline" size="sm" onClick={loadCustomers}>
              Retry
            </Button>
          </CardContent>
        </Card>
      )}
      <DataTable
        columns={columns}
        data={customers}
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
