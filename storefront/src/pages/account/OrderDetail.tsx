import { useEffect, useState } from "react";
import { useParams, Link } from "react-router";
import { ArrowLeft, Package } from "lucide-react";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { Skeleton } from "@/components/ui/skeleton";
import { api } from "@/lib/api";
import type { Order, OrderAddress, OrderItem } from "@/types/checkout";

interface OrderDetail extends Order {
  items: OrderItem[];
  addresses: OrderAddress[];
  history: OrderHistoryEntry[];
}

interface OrderHistoryEntry {
  status: string;
  comment: string | null;
  is_customer_notified: boolean;
  created_at: string;
}

const STATUS_COLORS: Record<string, string> = {
  pending: "bg-yellow-100 text-yellow-800",
  processing: "bg-blue-100 text-blue-800",
  on_hold: "bg-orange-100 text-orange-800",
  shipped: "bg-purple-100 text-purple-800",
  delivered: "bg-green-100 text-green-800",
  completed: "bg-green-100 text-green-800",
  canceled: "bg-red-100 text-red-800",
  refunded: "bg-gray-100 text-gray-800",
  returned: "bg-gray-100 text-gray-800",
};

export function OrderDetail() {
  const { uuid } = useParams<{ uuid: string }>();
  const [order, setOrder] = useState<OrderDetail | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    if (!uuid) return;

    api
      .get<{ data: OrderDetail }>(`/order/orders/${uuid}`)
      .then((response) => setOrder(response.data.data))
      .catch(() => {})
      .finally(() => setIsLoading(false));
  }, [uuid]);

  if (isLoading) {
    return (
      <div className="space-y-4">
        <Skeleton className="h-8 w-48" />
        <Skeleton className="h-64 w-full" />
      </div>
    );
  }

  if (!order) {
    return (
      <div className="text-center py-12">
        <Package className="h-16 w-16 mx-auto mb-4 text-muted-foreground" />
        <h2 className="text-xl font-bold mb-2">Order not found</h2>
        <Button asChild variant="outline">
          <Link to="/account/orders">Back to Orders</Link>
        </Button>
      </div>
    );
  }

  const shippingAddress = order.addresses?.find(
    (address) => address.type === "shipping",
  );
  const billingAddress = order.addresses?.find(
    (address) => address.type === "billing",
  );

  return (
    <div>
      <div className="flex items-center gap-4 mb-6">
        <Button asChild variant="ghost" size="sm">
          <Link to="/account/orders">
            <ArrowLeft className="h-4 w-4 mr-1" />
            Back
          </Link>
        </Button>
        <h2 className="text-2xl font-bold">Order #{order.increment_id}</h2>
        <Badge
          className={
            STATUS_COLORS[order.status] ?? "bg-gray-100 text-gray-800"
          }
          variant="secondary"
        >
          {order.status.replace("_", " ")}
        </Badge>
      </div>

      <div className="grid gap-6">
        {/* Order info */}
        <Card>
          <CardHeader>
            <CardTitle>Order Information</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
              <div>
                <p className="text-muted-foreground">Order Date</p>
                <p className="font-medium">
                  {new Date(order.created_at).toLocaleDateString()}
                </p>
              </div>
              <div>
                <p className="text-muted-foreground">Shipping</p>
                <p className="font-medium">
                  {order.shipping_method_label ?? "-"}
                </p>
              </div>
              <div>
                <p className="text-muted-foreground">Payment</p>
                <p className="font-medium">
                  {order.payment_method_label ?? "-"}
                </p>
              </div>
              {order.coupon_code && (
                <div>
                  <p className="text-muted-foreground">Coupon</p>
                  <p className="font-medium">{order.coupon_code}</p>
                </div>
              )}
            </div>
          </CardContent>
        </Card>

        {/* Addresses */}
        <div className="grid md:grid-cols-2 gap-6">
          {shippingAddress && (
            <Card>
              <CardHeader>
                <CardTitle className="text-base">Shipping Address</CardTitle>
              </CardHeader>
              <CardContent className="text-sm space-y-0.5">
                <p className="font-medium">
                  {shippingAddress.first_name} {shippingAddress.last_name}
                </p>
                {shippingAddress.company && <p>{shippingAddress.company}</p>}
                <p>{shippingAddress.street_line_1}</p>
                {shippingAddress.street_line_2 && (
                  <p>{shippingAddress.street_line_2}</p>
                )}
                <p>
                  {shippingAddress.city},{" "}
                  {shippingAddress.region_name
                    ? `${shippingAddress.region_name}, `
                    : ""}
                  {shippingAddress.postcode}
                </p>
                <p>{shippingAddress.country_name}</p>
                {shippingAddress.phone && <p>{shippingAddress.phone}</p>}
              </CardContent>
            </Card>
          )}

          {billingAddress && (
            <Card>
              <CardHeader>
                <CardTitle className="text-base">Billing Address</CardTitle>
              </CardHeader>
              <CardContent className="text-sm space-y-0.5">
                <p className="font-medium">
                  {billingAddress.first_name} {billingAddress.last_name}
                </p>
                {billingAddress.company && <p>{billingAddress.company}</p>}
                <p>{billingAddress.street_line_1}</p>
                {billingAddress.street_line_2 && (
                  <p>{billingAddress.street_line_2}</p>
                )}
                <p>
                  {billingAddress.city},{" "}
                  {billingAddress.region_name
                    ? `${billingAddress.region_name}, `
                    : ""}
                  {billingAddress.postcode}
                </p>
                <p>{billingAddress.country_name}</p>
                {billingAddress.phone && <p>{billingAddress.phone}</p>}
              </CardContent>
            </Card>
          )}
        </div>

        {/* Items */}
        <Card>
          <CardHeader>
            <CardTitle>Items</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              {order.items.map((item) => (
                <div key={item.uuid}>
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="font-medium">{item.name}</p>
                      <p className="text-sm text-muted-foreground">
                        SKU: {item.sku} &middot; Qty: {item.quantity} &middot;
                        ${item.unit_price} each
                      </p>
                    </div>
                    <span className="font-medium">${item.row_total}</span>
                  </div>
                </div>
              ))}
            </div>

            <Separator className="my-4" />

            <div className="space-y-1 text-sm">
              <div className="flex justify-between">
                <span className="text-muted-foreground">Subtotal</span>
                <span>${order.subtotal}</span>
              </div>
              {parseFloat(order.shipping_amount) > 0 && (
                <div className="flex justify-between">
                  <span className="text-muted-foreground">Shipping</span>
                  <span>${order.shipping_amount}</span>
                </div>
              )}
              {parseFloat(order.discount_amount) > 0 && (
                <div className="flex justify-between text-green-600">
                  <span>Discount</span>
                  <span>-${order.discount_amount}</span>
                </div>
              )}
              {parseFloat(order.tax_amount) > 0 && (
                <div className="flex justify-between">
                  <span className="text-muted-foreground">Tax</span>
                  <span>${order.tax_amount}</span>
                </div>
              )}
              <Separator />
              <div className="flex justify-between font-semibold text-base pt-1">
                <span>Grand Total</span>
                <span>${order.grand_total}</span>
              </div>
              {parseFloat(order.total_paid) > 0 && (
                <div className="flex justify-between text-sm">
                  <span className="text-muted-foreground">Total Paid</span>
                  <span>${order.total_paid}</span>
                </div>
              )}
              {parseFloat(order.total_refunded) > 0 && (
                <div className="flex justify-between text-sm text-red-600">
                  <span>Total Refunded</span>
                  <span>${order.total_refunded}</span>
                </div>
              )}
            </div>
          </CardContent>
        </Card>

        {/* Order History */}
        {order.history && order.history.length > 0 && (
          <Card>
            <CardHeader>
              <CardTitle>Order History</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {order.history.map((entry, index) => (
                  <div key={index} className="flex gap-4">
                    <div className="flex flex-col items-center">
                      <div className="w-2 h-2 rounded-full bg-primary mt-2" />
                      {index < order.history.length - 1 && (
                        <div className="w-px flex-1 bg-border mt-1" />
                      )}
                    </div>
                    <div className="pb-4">
                      <p className="text-sm font-medium capitalize">
                        {entry.status.replace("_", " ")}
                      </p>
                      {entry.comment && (
                        <p className="text-sm text-muted-foreground">
                          {entry.comment}
                        </p>
                      )}
                      <p className="text-xs text-muted-foreground mt-1">
                        {new Date(entry.created_at).toLocaleString()}
                      </p>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        )}
      </div>
    </div>
  );
}
