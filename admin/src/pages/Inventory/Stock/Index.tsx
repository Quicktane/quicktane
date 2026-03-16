import { useCallback, useEffect, useState } from "react";
import { type ColumnDef } from "@tanstack/react-table";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { AlertTriangle, Check, Loader2, PackageMinus, X } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { StockItem } from "@/types/inventory";
import { PageHeader } from "@/components/PageHeader";
import { DataTable } from "@/components/data-table/DataTable";
import { DataTableColumnHeader } from "@/components/data-table/DataTableColumnHeader";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import {
  Form,
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";

const adjustStockSchema = z.object({
  quantity_change: z
    .number()
    .int("Must be a whole number")
    .refine((value) => value !== 0, "Quantity change cannot be zero"),
  reason: z.string().min(1, "Reason is required").max(500),
});

type AdjustStockFormValues = z.infer<typeof adjustStockSchema>;

function InlineQuantityEditor({
  stockItem,
  onSave,
}: {
  stockItem: StockItem;
  onSave: () => void;
}) {
  const [isEditing, setIsEditing] = useState(false);
  const [quantity, setQuantity] = useState(String(stockItem.quantity));
  const [reason, setReason] = useState("");
  const [isSaving, setIsSaving] = useState(false);

  const handleSave = async () => {
    const newQuantity = parseInt(quantity, 10);

    if (isNaN(newQuantity) || newQuantity < 0) {
      toast.error("Quantity must be a non-negative number");
      return;
    }

    if (!reason.trim()) {
      toast.error("Please provide a reason for the change");
      return;
    }

    setIsSaving(true);

    try {
      await api.put("/admin/inventory/stock", {
        product_id: stockItem.product_id,
        source_id: stockItem.source_id,
        quantity: newQuantity,
        reason: reason.trim(),
      });
      toast.success("Stock quantity updated");
      setIsEditing(false);
      setReason("");
      onSave();
    } catch {
      toast.error("Failed to update stock quantity");
    } finally {
      setIsSaving(false);
    }
  };

  const handleCancel = () => {
    setQuantity(String(stockItem.quantity));
    setReason("");
    setIsEditing(false);
  };

  if (!isEditing) {
    return (
      <button
        type="button"
        onClick={() => setIsEditing(true)}
        className="rounded px-2 py-1 text-left hover:bg-muted"
      >
        {stockItem.quantity}
      </button>
    );
  }

  return (
    <div className="flex items-center gap-1">
      <div className="space-y-1">
        <Input
          type="number"
          value={quantity}
          onChange={(event) => setQuantity(event.target.value)}
          className="h-8 w-20"
          min={0}
          autoFocus
        />
        <Input
          placeholder="Reason..."
          value={reason}
          onChange={(event) => setReason(event.target.value)}
          className="h-8 w-40"
        />
      </div>
      <div className="flex flex-col gap-1">
        <Button
          variant="ghost"
          size="icon"
          className="h-6 w-6"
          onClick={handleSave}
          disabled={isSaving}
        >
          {isSaving ? (
            <Loader2 className="h-3 w-3 animate-spin" />
          ) : (
            <Check className="h-3 w-3" />
          )}
        </Button>
        <Button
          variant="ghost"
          size="icon"
          className="h-6 w-6"
          onClick={handleCancel}
          disabled={isSaving}
        >
          <X className="h-3 w-3" />
        </Button>
      </div>
    </div>
  );
}

function AdjustStockDialog({
  stockItem,
  open,
  onOpenChange,
  onAdjusted,
}: {
  stockItem: StockItem;
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onAdjusted: () => void;
}) {
  const [isSubmitting, setIsSubmitting] = useState(false);

  const form = useForm<AdjustStockFormValues>({
    resolver: zodResolver(adjustStockSchema),
    defaultValues: {
      quantity_change: 0,
      reason: "",
    },
  });

  useEffect(() => {
    if (open) {
      form.reset({ quantity_change: 0, reason: "" });
    }
  }, [open, form]);

  const handleSubmit = async (values: AdjustStockFormValues) => {
    setIsSubmitting(true);

    try {
      await api.post("/admin/inventory/stock/adjust", {
        product_id: stockItem.product_id,
        source_id: stockItem.source_id,
        quantity_change: values.quantity_change,
        reason: values.reason,
      });
      toast.success("Stock adjusted successfully");
      onOpenChange(false);
      onAdjusted();
    } catch {
      toast.error("Failed to adjust stock");
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Adjust Stock</DialogTitle>
          <DialogDescription>
            Adjust stock for {stockItem.product?.name ?? "Product"} at{" "}
            {stockItem.source?.name ?? "Source"}. Current quantity:{" "}
            {stockItem.quantity}.
          </DialogDescription>
        </DialogHeader>

        <Form {...form}>
          <form
            onSubmit={form.handleSubmit(handleSubmit)}
            className="space-y-4"
          >
            <FormField
              control={form.control}
              name="quantity_change"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Quantity Change</FormLabel>
                  <FormControl>
                    <Input
                      type="number"
                      placeholder="e.g. 10 or -5"
                      {...field}
                    />
                  </FormControl>
                  <FormDescription>
                    Use positive numbers to add stock, negative to remove
                  </FormDescription>
                  <FormMessage />
                </FormItem>
              )}
            />
            <FormField
              control={form.control}
              name="reason"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Reason</FormLabel>
                  <FormControl>
                    <Input
                      placeholder="e.g. Received shipment, Inventory correction"
                      {...field}
                    />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <DialogFooter>
              <Button
                type="button"
                variant="outline"
                onClick={() => onOpenChange(false)}
              >
                Cancel
              </Button>
              <Button type="submit" disabled={isSubmitting}>
                {isSubmitting ? (
                  <>
                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                    Adjusting...
                  </>
                ) : (
                  "Adjust Stock"
                )}
              </Button>
            </DialogFooter>
          </form>
        </Form>
      </DialogContent>
    </Dialog>
  );
}

export function StockIndex() {
  const [stockItems, setStockItems] = useState<StockItem[]>([]);
  const [lowStockItems, setLowStockItems] = useState<StockItem[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [activeTab, setActiveTab] = useState("all");
  const [adjustingItem, setAdjustingItem] = useState<StockItem | null>(null);

  const loadStock = useCallback(async () => {
    try {
      const [allResponse, lowResponse] = await Promise.all([
        api.get<{ data: StockItem[] }>("/admin/inventory/stock"),
        api.get<{ data: StockItem[] }>("/admin/inventory/stock/low"),
      ]);
      setStockItems(allResponse.data.data);
      setLowStockItems(lowResponse.data.data);
    } catch {
      toast.error("Failed to load stock data");
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadStock();
  }, [loadStock]);

  const columns: ColumnDef<StockItem>[] = [
    {
      id: "product_sku",
      accessorFn: (row) => row.product?.sku,
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="SKU" />
      ),
      cell: ({ row }) => (
        <span className="font-mono text-sm">
          {row.original.product?.sku ?? "-"}
        </span>
      ),
    },
    {
      id: "product_name",
      accessorFn: (row) => row.product?.name,
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Product" />
      ),
      cell: ({ row }) => row.original.product?.name ?? "-",
    },
    {
      id: "source_name",
      accessorFn: (row) => row.source?.name,
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Source" />
      ),
      cell: ({ row }) => row.original.source?.name ?? "-",
    },
    {
      accessorKey: "quantity",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Quantity" />
      ),
      cell: function QuantityCell({ row }) {
        return (
          <InlineQuantityEditor
            stockItem={row.original}
            onSave={loadStock}
          />
        );
      },
    },
    {
      accessorKey: "reserved",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Reserved" />
      ),
    },
    {
      accessorKey: "salable_quantity",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Salable" />
      ),
      cell: ({ row }) => {
        const salable = row.getValue<number>("salable_quantity");
        return (
          <span className={salable <= 0 ? "font-medium text-destructive" : ""}>
            {salable}
          </span>
        );
      },
    },
    {
      accessorKey: "is_in_stock",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Status" />
      ),
      cell: ({ row }) => (
        <Badge
          variant={row.getValue("is_in_stock") ? "default" : "destructive"}
        >
          {row.getValue("is_in_stock") ? "In Stock" : "Out of Stock"}
        </Badge>
      ),
    },
    {
      id: "actions",
      header: "Actions",
      cell: function ActionsCell({ row }) {
        return (
          <Button
            variant="ghost"
            size="sm"
            onClick={() => setAdjustingItem(row.original)}
          >
            <PackageMinus className="mr-2 h-4 w-4" />
            Adjust
          </Button>
        );
      },
    },
  ];

  if (isLoading) {
    return (
      <div className="space-y-4">
        <Skeleton className="h-8 w-48" />
        <Skeleton className="h-10 w-64" />
        <Skeleton className="h-64 w-full" />
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <PageHeader
        title="Stock Management"
        description="View and manage product stock levels across sources"
      />

      <Tabs value={activeTab} onValueChange={setActiveTab}>
        <TabsList>
          <TabsTrigger value="all">All Stock</TabsTrigger>
          <TabsTrigger value="low" className="gap-2">
            <AlertTriangle className="h-4 w-4" />
            Low Stock
            {lowStockItems.length > 0 && (
              <Badge variant="destructive" className="ml-1 h-5 px-1.5">
                {lowStockItems.length}
              </Badge>
            )}
          </TabsTrigger>
        </TabsList>

        <TabsContent value="all">
          <DataTable columns={columns} data={stockItems} />
        </TabsContent>

        <TabsContent value="low">
          {lowStockItems.length === 0 ? (
            <div className="flex h-48 items-center justify-center rounded-lg border-2 border-dashed">
              <p className="text-sm text-muted-foreground">
                No low stock items. All products have sufficient inventory.
              </p>
            </div>
          ) : (
            <DataTable columns={columns} data={lowStockItems} />
          )}
        </TabsContent>
      </Tabs>

      {adjustingItem && (
        <AdjustStockDialog
          stockItem={adjustingItem}
          open={!!adjustingItem}
          onOpenChange={(open) => {
            if (!open) {
              setAdjustingItem(null);
            }
          }}
          onAdjusted={loadStock}
        />
      )}
    </div>
  );
}
