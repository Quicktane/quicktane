import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef, type PaginationState } from "@tanstack/react-table";
import { Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { Product } from "@/types/catalog";
import { PageHeader } from "@/components/PageHeader";
import { DataTable } from "@/components/data-table/DataTable";
import { DataTableColumnHeader } from "@/components/data-table/DataTableColumnHeader";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
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

function DeleteProductButton({
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
      await api.delete(`/admin/catalog/products/${uuid}`);
      toast.success(`Product "${name}" deleted successfully`);
      window.dispatchEvent(new CustomEvent("product-deleted"));
    } catch {
      toast.error("Failed to delete product");
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
          <AlertDialogTitle>Delete Product</AlertDialogTitle>
          <AlertDialogDescription>
            Are you sure you want to delete the product "{name}"? This action
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

const columns: ColumnDef<Product>[] = [
  {
    accessorKey: "sku",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="SKU" />
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
    accessorKey: "base_price",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Price" />
    ),
    cell: ({ row }) => {
      const price = parseFloat(row.getValue("base_price"));
      return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
      }).format(price);
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
    id: "actions",
    header: "Actions",
    cell: function ActionsCell({ row }) {
      const product = row.original;

      return (
        <div className="flex items-center gap-2">
          <Button variant="ghost" size="icon" asChild>
            <Link to={`/catalog/products/${product.uuid}`}>
              <Pencil className="h-4 w-4" />
            </Link>
          </Button>
          <DeleteProductButton uuid={product.uuid} name={product.name} />
        </div>
      );
    },
  },
];

export function ProductsIndex() {
  const [products, setProducts] = useState<Product[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [typeFilter, setTypeFilter] = useState<string>("all");
  const [activeFilter, setActiveFilter] = useState<string>("all");
  const [pagination, setPagination] = useState<PaginationState>({
    pageIndex: 0,
    pageSize: 15,
  });
  const [pageCount, setPageCount] = useState(0);

  const loadProducts = useCallback(async () => {
    setIsLoading(true);

    try {
      const params: Record<string, string | number> = {
        page: pagination.pageIndex + 1,
        per_page: pagination.pageSize,
      };

      if (typeFilter !== "all") {
        params.type = typeFilter;
      }

      if (activeFilter !== "all") {
        params.is_active = activeFilter === "active" ? 1 : 0;
      }

      const response = await api.get<{
        data: Product[];
        meta: { last_page: number };
      }>("/admin/catalog/products", { params });

      setProducts(response.data.data);
      setPageCount(response.data.meta.last_page);
    } catch {
      toast.error("Failed to load products");
    } finally {
      setIsLoading(false);
    }
  }, [pagination.pageIndex, pagination.pageSize, typeFilter, activeFilter]);

  useEffect(() => {
    loadProducts();

    const handleDeleted = () => loadProducts();
    window.addEventListener("product-deleted", handleDeleted);

    return () => window.removeEventListener("product-deleted", handleDeleted);
  }, [loadProducts]);

  const handleTypeFilterChange = (value: string) => {
    setTypeFilter(value);
    setPagination((prev) => ({ ...prev, pageIndex: 0 }));
  };

  const handleActiveFilterChange = (value: string) => {
    setActiveFilter(value);
    setPagination((prev) => ({ ...prev, pageIndex: 0 }));
  };

  if (isLoading && products.length === 0) {
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
        title="Products"
        description="Manage your product catalog"
        actions={
          <Button asChild>
            <Link to="/catalog/products/create">
              <Plus className="mr-2 h-4 w-4" />
              New Product
            </Link>
          </Button>
        }
      />

      <div className="flex gap-3">
        <Select value={typeFilter} onValueChange={handleTypeFilterChange}>
          <SelectTrigger className="w-40">
            <SelectValue placeholder="All Types" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">All Types</SelectItem>
            <SelectItem value="simple">Simple</SelectItem>
            <SelectItem value="configurable">Configurable</SelectItem>
            <SelectItem value="virtual">Virtual</SelectItem>
            <SelectItem value="downloadable">Downloadable</SelectItem>
            <SelectItem value="grouped">Grouped</SelectItem>
            <SelectItem value="bundle">Bundle</SelectItem>
          </SelectContent>
        </Select>

        <Select value={activeFilter} onValueChange={handleActiveFilterChange}>
          <SelectTrigger className="w-40">
            <SelectValue placeholder="All Statuses" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">All Statuses</SelectItem>
            <SelectItem value="active">Active</SelectItem>
            <SelectItem value="inactive">Inactive</SelectItem>
          </SelectContent>
        </Select>
      </div>

      <DataTable
        columns={columns}
        data={products}
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
