import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef, type PaginationState } from "@tanstack/react-table";
import { Eye } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { InvoiceListItem } from "@/types/order";
import { PageHeader } from "@/components/PageHeader";
import { DataTable } from "@/components/data-table/DataTable";
import { DataTableColumnHeader } from "@/components/data-table/DataTableColumnHeader";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";

const invoiceStatusVariant: Record<
  string,
  "default" | "secondary" | "outline" | "destructive"
> = {
  pending: "secondary",
  paid: "default",
};

const columns: ColumnDef<InvoiceListItem>[] = [
  {
    accessorKey: "increment_id",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Invoice #" />
    ),
    cell: ({ row }) => (
      <span className="font-mono font-medium">
        {row.getValue<string>("increment_id")}
      </span>
    ),
  },
  {
    id: "order",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Order #" />
    ),
    cell: ({ row }) => {
      const invoice = row.original;
      if (!invoice.order_increment_id) {
        return <span className="text-muted-foreground">-</span>;
      }
      if (invoice.order_uuid) {
        return (
          <Link
            to={`/orders/${invoice.order_uuid}`}
            className="font-mono font-medium text-primary underline-offset-4 hover:underline"
          >
            {invoice.order_increment_id}
          </Link>
        );
      }
      return (
        <span className="font-mono font-medium">
          {invoice.order_increment_id}
        </span>
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
        <Badge variant={invoiceStatusVariant[status] ?? "outline"}>
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
    cell: ({ row }) => row.getValue<string>("grand_total"),
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
          <Link to={`/invoices/${row.original.uuid}`}>
            <Eye className="h-4 w-4" />
          </Link>
        </Button>
      );
    },
  },
];

export function InvoicesIndex() {
  const [invoices, setInvoices] = useState<InvoiceListItem[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [pagination, setPagination] = useState<PaginationState>({
    pageIndex: 0,
    pageSize: 15,
  });
  const [pageCount, setPageCount] = useState(0);

  const loadInvoices = useCallback(async () => {
    setIsLoading(true);

    try {
      const response = await api.get<{
        data: InvoiceListItem[];
        meta: { last_page: number };
      }>("/admin/order/invoices", {
        params: {
          page: pagination.pageIndex + 1,
          per_page: pagination.pageSize,
        },
      });

      setInvoices(response.data.data);
      setPageCount(response.data.meta.last_page);
    } catch {
      toast.error("Failed to load invoices");
    } finally {
      setIsLoading(false);
    }
  }, [pagination.pageIndex, pagination.pageSize]);

  useEffect(() => {
    loadInvoices();
  }, [loadInvoices]);

  if (isLoading && invoices.length === 0) {
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
        title="Invoices"
        description="View and manage invoices across all orders"
      />
      <DataTable
        columns={columns}
        data={invoices}
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
