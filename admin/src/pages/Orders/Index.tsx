import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef, type PaginationState } from "@tanstack/react-table";
import { Eye } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { Order } from "@/types/order";
import { PageHeader } from "@/components/PageHeader";
import { DataTable } from "@/components/data-table/DataTable";
import { DataTableColumnHeader } from "@/components/data-table/DataTableColumnHeader";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";

const orderStatusVariant: Record<
  string,
  "default" | "secondary" | "outline" | "destructive"
> = {
  pending: "secondary",
  processing: "default",
  complete: "default",
  closed: "outline",
  canceled: "destructive",
  holded: "outline",
  payment_review: "secondary",
};

const columns: ColumnDef<Order>[] = [
  {
    accessorKey: "increment_id",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Order #" />
    ),
    cell: ({ row }) => (
      <span className="font-mono font-medium">
        {row.getValue<string>("increment_id")}
      </span>
    ),
  },
  {
    id: "customer",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Customer" />
    ),
    cell: ({ row }) => {
      const order = row.original;
      return (
        <div>
          {order.customer ? (
            <span className="font-medium">{order.customer.name}</span>
          ) : null}
          <span className="block text-xs text-muted-foreground">
            {order.customer_email}
          </span>
        </div>
      );
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
        <Badge variant={orderStatusVariant[status] ?? "outline"}>
          {status.charAt(0).toUpperCase() + status.slice(1).replace(/_/g, " ")}
        </Badge>
      );
    },
  },
  {
    accessorKey: "grand_total",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Grand Total" />
    ),
    cell: ({ row }) => {
      return `${row.original.currency_code} ${row.getValue<string>("grand_total")}`;
    },
  },
  {
    accessorKey: "payment_method_label",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Payment" />
    ),
    cell: ({ row }) =>
      row.getValue<string | null>("payment_method_label") ?? "-",
  },
  {
    accessorKey: "created_at",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Date" />
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
      return (
        <Button variant="ghost" size="icon" asChild>
          <Link to={`/orders/${row.original.uuid}`}>
            <Eye className="h-4 w-4" />
          </Link>
        </Button>
      );
    },
  },
];

export function OrdersIndex() {
  const [orders, setOrders] = useState<Order[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [pagination, setPagination] = useState<PaginationState>({
    pageIndex: 0,
    pageSize: 15,
  });
  const [pageCount, setPageCount] = useState(0);

  const loadOrders = useCallback(async () => {
    setIsLoading(true);

    try {
      const response = await api.get<{
        data: Order[];
        meta: { last_page: number };
      }>("/admin/order/orders", {
        params: {
          page: pagination.pageIndex + 1,
          per_page: pagination.pageSize,
        },
      });

      setOrders(response.data.data);
      setPageCount(response.data.meta.last_page);
    } catch {
      toast.error("Failed to load orders");
    } finally {
      setIsLoading(false);
    }
  }, [pagination.pageIndex, pagination.pageSize]);

  useEffect(() => {
    loadOrders();
  }, [loadOrders]);

  if (isLoading && orders.length === 0) {
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
        title="Orders"
        description="Manage customer orders and fulfillment"
      />
      <DataTable
        columns={columns}
        data={orders}
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
