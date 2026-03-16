import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef, type PaginationState } from "@tanstack/react-table";
import { Eye } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { Cart } from "@/types/cart";
import { PageHeader } from "@/components/PageHeader";
import { DataTable } from "@/components/data-table/DataTable";
import { DataTableColumnHeader } from "@/components/data-table/DataTableColumnHeader";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";

const statusVariant: Record<string, "default" | "secondary" | "outline" | "destructive"> = {
  active: "default",
  converted: "secondary",
  abandoned: "destructive",
  merged: "outline",
};

const columns: ColumnDef<Cart>[] = [
  {
    id: "customer",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Customer" />
    ),
    cell: ({ row }) => {
      const customer = row.original.customer;
      if (customer) {
        return (
          <div>
            <span className="font-medium">{customer.name}</span>
            <span className="block text-xs text-muted-foreground">
              {customer.email}
            </span>
          </div>
        );
      }
      return <span className="text-muted-foreground">Guest</span>;
    },
  },
  {
    accessorKey: "status",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Status" />
    ),
    cell: ({ row }) => {
      const status = row.getValue<string>("status");
      return (
        <Badge variant={statusVariant[status] ?? "outline"}>
          {status.charAt(0).toUpperCase() + status.slice(1)}
        </Badge>
      );
    },
  },
  {
    accessorKey: "items_count",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Items" />
    ),
  },
  {
    accessorKey: "subtotal",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Subtotal" />
    ),
    cell: ({ row }) => {
      const subtotal = row.getValue<string>("subtotal");
      const currency = row.original.currency_code;
      return `${currency} ${subtotal}`;
    },
  },
  {
    accessorKey: "created_at",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Created" />
    ),
    cell: ({ row }) =>
      new Date(row.getValue<string>("created_at")).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
      }),
  },
  {
    accessorKey: "updated_at",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Updated" />
    ),
    cell: ({ row }) =>
      new Date(row.getValue<string>("updated_at")).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
      }),
  },
  {
    id: "actions",
    header: "Actions",
    cell: function ActionsCell({ row }) {
      return (
        <Button variant="ghost" size="icon" asChild>
          <Link to={`/carts/${row.original.uuid}`}>
            <Eye className="h-4 w-4" />
          </Link>
        </Button>
      );
    },
  },
];

export function CartsIndex() {
  const [carts, setCarts] = useState<Cart[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [pagination, setPagination] = useState<PaginationState>({
    pageIndex: 0,
    pageSize: 15,
  });
  const [pageCount, setPageCount] = useState(0);

  const loadCarts = useCallback(async () => {
    setIsLoading(true);

    try {
      const response = await api.get<{
        data: Cart[];
        meta: { last_page: number };
      }>("/admin/cart/carts", {
        params: {
          page: pagination.pageIndex + 1,
          per_page: pagination.pageSize,
        },
      });

      setCarts(response.data.data);
      setPageCount(response.data.meta.last_page);
    } catch {
      toast.error("Failed to load carts");
    } finally {
      setIsLoading(false);
    }
  }, [pagination.pageIndex, pagination.pageSize]);

  useEffect(() => {
    loadCarts();
  }, [loadCarts]);

  if (isLoading && carts.length === 0) {
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
        title="Carts"
        description="View shopping carts for support and analytics"
      />
      <DataTable
        columns={columns}
        data={carts}
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
