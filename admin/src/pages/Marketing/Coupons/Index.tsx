import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef } from "@tanstack/react-table";
import { Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { Coupon } from "@/types/promotion";
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

function DeleteCouponButton({
  uuid,
  code,
  onDeleted,
}: {
  uuid: string;
  code: string;
  onDeleted: () => void;
}) {
  const [isDeleting, setIsDeleting] = useState(false);

  const handleDelete = async () => {
    setIsDeleting(true);

    try {
      await api.delete(`/admin/promotion/coupons/${uuid}`);
      toast.success(`Coupon "${code}" deleted`);
      onDeleted();
    } catch {
      toast.error("Failed to delete coupon");
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
          <AlertDialogTitle>Delete Coupon</AlertDialogTitle>
          <AlertDialogDescription>
            Are you sure you want to delete the coupon "{code}"? This action
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

export function MarketingCouponsIndex() {
  const [coupons, setCoupons] = useState<Coupon[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  const loadCoupons = useCallback(async () => {
    try {
      const response = await api.get<{ data: Coupon[] }>(
        "/admin/promotion/coupons",
      );
      setCoupons(response.data.data);
    } catch {
      toast.error("Failed to load coupons");
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadCoupons();
  }, [loadCoupons]);

  const columns: ColumnDef<Coupon>[] = [
    {
      accessorKey: "code",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Code" />
      ),
      cell: ({ row }) => (
        <span className="font-mono font-medium">
          {row.getValue<string>("code")}
        </span>
      ),
    },
    {
      id: "rule",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Rule" />
      ),
      cell: ({ row }) => row.original.rule?.name ?? "-",
    },
    {
      accessorKey: "usage_limit",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Usage Limit" />
      ),
      cell: ({ row }) =>
        row.getValue<number | null>("usage_limit") ?? "Unlimited",
    },
    {
      accessorKey: "times_used",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Times Used" />
      ),
    },
    {
      accessorKey: "is_active",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Active" />
      ),
      cell: ({ row }) => (
        <Badge variant={row.getValue("is_active") ? "default" : "outline"}>
          {row.getValue("is_active") ? "Active" : "Inactive"}
        </Badge>
      ),
    },
    {
      accessorKey: "expires_at",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Expires" />
      ),
      cell: ({ row }) => {
        const expiresAt = row.getValue<string | null>("expires_at");
        if (!expiresAt) return "Never";
        return new Date(expiresAt).toLocaleDateString("en-US", {
          year: "numeric",
          month: "short",
          day: "numeric",
        });
      },
    },
    {
      id: "actions",
      header: "Actions",
      cell: function ActionsCell({ row }) {
        const coupon = row.original;

        return (
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon" asChild>
              <Link to={`/marketing/coupons/${coupon.uuid}`}>
                <Pencil className="h-4 w-4" />
              </Link>
            </Button>
            <DeleteCouponButton
              uuid={coupon.uuid}
              code={coupon.code}
              onDeleted={loadCoupons}
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
        title="Coupons"
        description="Manage discount coupon codes"
        actions={
          <Button asChild>
            <Link to="/marketing/coupons/create">
              <Plus className="mr-2 h-4 w-4" />
              New Coupon
            </Link>
          </Button>
        }
      />
      <DataTable columns={columns} data={coupons} />
    </div>
  );
}
