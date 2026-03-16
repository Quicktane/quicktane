import { useCallback, useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type {
  Order,
  OrderItem,
  OrderAddress,
  OrderHistoryEntry,
  Invoice,
  CreditMemo,
} from "@/types/order";
import { PageHeader } from "@/components/PageHeader";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { Label } from "@/components/ui/label";

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

const ORDER_STATUSES = [
  { value: "pending", label: "Pending" },
  { value: "processing", label: "Processing" },
  { value: "complete", label: "Complete" },
  { value: "closed", label: "Closed" },
  { value: "canceled", label: "Canceled" },
  { value: "holded", label: "On Hold" },
  { value: "payment_review", label: "Payment Review" },
];

function OrderItemsTab({ items }: { items: OrderItem[] }) {
  if (items.length === 0) {
    return (
      <p className="py-8 text-center text-sm text-muted-foreground">
        No items found.
      </p>
    );
  }

  return (
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>SKU</TableHead>
          <TableHead>Product</TableHead>
          <TableHead className="text-right">Qty</TableHead>
          <TableHead className="text-right">Unit Price</TableHead>
          <TableHead className="text-right">Tax</TableHead>
          <TableHead className="text-right">Discount</TableHead>
          <TableHead className="text-right">Row Total</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        {items.map((item) => (
          <TableRow key={item.uuid}>
            <TableCell className="font-mono text-sm">{item.sku}</TableCell>
            <TableCell>{item.name}</TableCell>
            <TableCell className="text-right">{item.quantity}</TableCell>
            <TableCell className="text-right">{item.unit_price}</TableCell>
            <TableCell className="text-right">{item.tax_amount}</TableCell>
            <TableCell className="text-right">{item.discount_amount}</TableCell>
            <TableCell className="text-right font-medium">
              {item.row_total}
            </TableCell>
          </TableRow>
        ))}
      </TableBody>
    </Table>
  );
}

function OrderAddressCard({ address }: { address: OrderAddress }) {
  return (
    <Card>
      <CardHeader className="pb-2">
        <CardTitle className="text-sm font-medium capitalize">
          {address.type} Address
        </CardTitle>
      </CardHeader>
      <CardContent className="text-sm space-y-1">
        <p className="font-medium">
          {address.first_name} {address.last_name}
        </p>
        {address.company && (
          <p className="text-muted-foreground">{address.company}</p>
        )}
        <p>{address.street_line_1}</p>
        {address.street_line_2 && <p>{address.street_line_2}</p>}
        <p>
          {address.city}
          {address.region_name ? `, ${address.region_name}` : ""}{" "}
          {address.postcode}
        </p>
        <p>{address.country_name}</p>
        {address.phone && (
          <p className="text-muted-foreground">{address.phone}</p>
        )}
      </CardContent>
    </Card>
  );
}

function OrderHistoryTab({ history }: { history: OrderHistoryEntry[] }) {
  if (history.length === 0) {
    return (
      <p className="py-8 text-center text-sm text-muted-foreground">
        No history entries.
      </p>
    );
  }

  return (
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>Status</TableHead>
          <TableHead>Comment</TableHead>
          <TableHead>Customer Notified</TableHead>
          <TableHead>Date</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        {history.map((entry) => (
          <TableRow key={entry.id}>
            <TableCell>
              <Badge variant={orderStatusVariant[entry.status] ?? "outline"}>
                {entry.status.charAt(0).toUpperCase() +
                  entry.status.slice(1).replace(/_/g, " ")}
              </Badge>
            </TableCell>
            <TableCell className="text-muted-foreground">
              {entry.comment ?? "-"}
            </TableCell>
            <TableCell>
              <Badge variant={entry.is_customer_notified ? "default" : "outline"}>
                {entry.is_customer_notified ? "Yes" : "No"}
              </Badge>
            </TableCell>
            <TableCell>
              {new Date(entry.created_at).toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit",
              })}
            </TableCell>
          </TableRow>
        ))}
      </TableBody>
    </Table>
  );
}

function InvoicesTab({ invoices }: { invoices: Invoice[] }) {
  if (invoices.length === 0) {
    return (
      <p className="py-8 text-center text-sm text-muted-foreground">
        No invoices yet.
      </p>
    );
  }

  return (
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>Invoice #</TableHead>
          <TableHead>Status</TableHead>
          <TableHead className="text-right">Grand Total</TableHead>
          <TableHead>Date</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        {invoices.map((invoice) => (
          <TableRow key={invoice.uuid}>
            <TableCell className="font-mono font-medium">
              {invoice.increment_id}
            </TableCell>
            <TableCell>
              <Badge variant="outline">{invoice.status}</Badge>
            </TableCell>
            <TableCell className="text-right">{invoice.grand_total}</TableCell>
            <TableCell>
              {new Date(invoice.created_at).toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
              })}
            </TableCell>
          </TableRow>
        ))}
      </TableBody>
    </Table>
  );
}

function CreditMemosTab({ creditMemos }: { creditMemos: CreditMemo[] }) {
  if (creditMemos.length === 0) {
    return (
      <p className="py-8 text-center text-sm text-muted-foreground">
        No credit memos yet.
      </p>
    );
  }

  return (
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>Credit Memo #</TableHead>
          <TableHead>Status</TableHead>
          <TableHead className="text-right">Grand Total</TableHead>
          <TableHead>Date</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        {creditMemos.map((memo) => (
          <TableRow key={memo.uuid}>
            <TableCell className="font-mono font-medium">
              {memo.increment_id}
            </TableCell>
            <TableCell>
              <Badge variant="outline">{memo.status}</Badge>
            </TableCell>
            <TableCell className="text-right">{memo.grand_total}</TableCell>
            <TableCell>
              {new Date(memo.created_at).toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
              })}
            </TableCell>
          </TableRow>
        ))}
      </TableBody>
    </Table>
  );
}

function ChangeStatusDialog({
  orderUuid,
  currentStatus,
  onStatusChanged,
}: {
  orderUuid: string;
  currentStatus: string;
  onStatusChanged: () => void;
}) {
  const [open, setOpen] = useState(false);
  const [selectedStatus, setSelectedStatus] = useState(currentStatus);
  const [comment, setComment] = useState("");
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleSubmit = async () => {
    setIsSubmitting(true);

    try {
      await api.post(`/admin/order/orders/${orderUuid}/status`, {
        status: selectedStatus,
        comment: comment || null,
      });
      toast.success("Order status updated");
      setOpen(false);
      setComment("");
      onStatusChanged();
    } catch {
      toast.error("Failed to update order status");
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <Button variant="outline">Change Status</Button>
      </DialogTrigger>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Change Order Status</DialogTitle>
        </DialogHeader>
        <div className="space-y-4">
          <div className="space-y-2">
            <Label>New Status</Label>
            <Select value={selectedStatus} onValueChange={setSelectedStatus}>
              <SelectTrigger>
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                {ORDER_STATUSES.map((statusOption) => (
                  <SelectItem key={statusOption.value} value={statusOption.value}>
                    {statusOption.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          <div className="space-y-2">
            <Label>Comment (optional)</Label>
            <Textarea
              placeholder="Add a comment about this status change..."
              value={comment}
              onChange={(event) => setComment(event.target.value)}
            />
          </div>
        </div>
        <DialogFooter>
          <Button variant="outline" onClick={() => setOpen(false)}>
            Cancel
          </Button>
          <Button onClick={handleSubmit} disabled={isSubmitting}>
            {isSubmitting ? "Updating..." : "Update Status"}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}

function CreateInvoiceButton({
  orderUuid,
  onCreated,
}: {
  orderUuid: string;
  onCreated: () => void;
}) {
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleCreate = async () => {
    setIsSubmitting(true);

    try {
      await api.post(`/admin/order/orders/${orderUuid}/invoice`);
      toast.success("Invoice created successfully");
      onCreated();
    } catch {
      toast.error("Failed to create invoice");
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <Button onClick={handleCreate} disabled={isSubmitting}>
      {isSubmitting ? "Creating..." : "Create Invoice"}
    </Button>
  );
}

function CreateCreditMemoButton({
  orderUuid,
  onCreated,
}: {
  orderUuid: string;
  onCreated: () => void;
}) {
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleCreate = async () => {
    setIsSubmitting(true);

    try {
      await api.post(`/admin/order/orders/${orderUuid}/credit-memo`);
      toast.success("Credit memo created successfully");
      onCreated();
    } catch {
      toast.error("Failed to create credit memo");
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <Button variant="outline" onClick={handleCreate} disabled={isSubmitting}>
      {isSubmitting ? "Creating..." : "Create Credit Memo"}
    </Button>
  );
}

export function OrdersShow() {
  const { uuid } = useParams<{ uuid: string }>();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(true);
  const [order, setOrder] = useState<Order | null>(null);

  const loadOrder = useCallback(async () => {
    try {
      const response = await api.get<{ data: Order }>(
        `/admin/order/orders/${uuid}`,
      );
      setOrder(response.data.data);
    } catch {
      toast.error("Failed to load order");
      navigate("/orders");
    } finally {
      setIsLoading(false);
    }
  }, [uuid, navigate]);

  useEffect(() => {
    loadOrder();
  }, [loadOrder]);

  if (isLoading || !order) {
    return (
      <div className="space-y-4">
        <Skeleton className="h-8 w-48" />
        <Skeleton className="h-32 w-full" />
        <Skeleton className="h-64 w-full" />
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <PageHeader
        title={`Order ${order.increment_id}`}
        description={`Placed on ${new Date(order.created_at).toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" })}`}
        actions={
          <div className="flex items-center gap-2">
            <Badge variant={orderStatusVariant[order.status] ?? "outline"}>
              {order.status.charAt(0).toUpperCase() +
                order.status.slice(1).replace(/_/g, " ")}
            </Badge>
            <ChangeStatusDialog
              orderUuid={order.uuid}
              currentStatus={order.status}
              onStatusChanged={loadOrder}
            />
            <CreateInvoiceButton
              orderUuid={order.uuid}
              onCreated={loadOrder}
            />
            <CreateCreditMemoButton
              orderUuid={order.uuid}
              onCreated={loadOrder}
            />
          </div>
        }
      />

      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              Grand Total
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-2xl font-bold">
              {order.currency_code} {order.grand_total}
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              Customer
            </CardTitle>
          </CardHeader>
          <CardContent>
            {order.customer ? (
              <div>
                <p className="font-medium">{order.customer.name}</p>
                <p className="text-sm text-muted-foreground">
                  {order.customer_email}
                </p>
              </div>
            ) : (
              <p className="text-sm text-muted-foreground">
                {order.customer_email}
              </p>
            )}
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              Payment
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p className="font-medium">
              {order.payment_method_label ?? "—"}
            </p>
            <p className="text-sm text-muted-foreground">
              Paid: {order.currency_code} {order.total_paid}
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              Shipping
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p className="font-medium">
              {order.shipping_method_label ?? "—"}
            </p>
            <p className="text-sm text-muted-foreground">
              {order.currency_code} {order.shipping_amount}
            </p>
          </CardContent>
        </Card>
      </div>

      <Tabs defaultValue="items">
        <TabsList>
          <TabsTrigger value="items">Items</TabsTrigger>
          <TabsTrigger value="addresses">Addresses</TabsTrigger>
          <TabsTrigger value="history">History</TabsTrigger>
          <TabsTrigger value="invoices">Invoices</TabsTrigger>
          <TabsTrigger value="credit-memos">Credit Memos</TabsTrigger>
        </TabsList>

        <TabsContent value="items">
          <Card>
            <CardHeader>
              <CardTitle>Order Items</CardTitle>
            </CardHeader>
            <CardContent>
              <OrderItemsTab items={order.items ?? []} />
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="addresses">
          <div className="grid gap-4 sm:grid-cols-2">
            {(order.addresses ?? []).map((address) => (
              <OrderAddressCard key={address.id} address={address} />
            ))}
            {(order.addresses ?? []).length === 0 && (
              <p className="col-span-2 py-8 text-center text-sm text-muted-foreground">
                No addresses found.
              </p>
            )}
          </div>
        </TabsContent>

        <TabsContent value="history">
          <Card>
            <CardHeader>
              <CardTitle>Order History</CardTitle>
            </CardHeader>
            <CardContent>
              <OrderHistoryTab history={order.history ?? []} />
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="invoices">
          <Card>
            <CardHeader>
              <CardTitle>Invoices</CardTitle>
            </CardHeader>
            <CardContent>
              <InvoicesTab invoices={order.invoices ?? []} />
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="credit-memos">
          <Card>
            <CardHeader>
              <CardTitle>Credit Memos</CardTitle>
            </CardHeader>
            <CardContent>
              <CreditMemosTab creditMemos={order.credit_memos ?? []} />
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>

      <div>
        <Button variant="outline" onClick={() => navigate("/orders")}>
          Back to Orders
        </Button>
      </div>
    </div>
  );
}
