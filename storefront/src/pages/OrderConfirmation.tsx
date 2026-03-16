import { useEffect, useState } from "react";
import { useParams, Link } from "react-router";
import { CheckCircle, Package, Loader2 } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { api } from "@/lib/api";
import { useAuth } from "@/contexts/AuthContext";
import type { Order } from "@/types/checkout";

export function OrderConfirmation() {
  const { uuid } = useParams<{ uuid: string }>();
  const { isAuthenticated } = useAuth();
  const [order, setOrder] = useState<Order | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    if (!uuid || !isAuthenticated) {
      setIsLoading(false);
      return;
    }

    api
      .get<{ data: Order }>(`/order/orders/${uuid}`)
      .then((response) => setOrder(response.data.data))
      .catch(() => {})
      .finally(() => setIsLoading(false));
  }, [uuid, isAuthenticated]);

  if (isLoading) {
    return (
      <div className="container mx-auto px-4 py-16 text-center">
        <Loader2 className="h-12 w-12 mx-auto animate-spin text-muted-foreground" />
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-16 max-w-2xl">
      <div className="text-center mb-8">
        <CheckCircle className="h-16 w-16 mx-auto mb-4 text-green-600" />
        <h1 className="text-3xl font-bold mb-2">Order Confirmed!</h1>
        <p className="text-muted-foreground">
          Thank you for your purchase.
          {order && (
            <>
              {" "}
              Your order number is{" "}
              <span className="font-semibold text-foreground">
                #{order.increment_id}
              </span>
            </>
          )}
        </p>
      </div>

      {order && (
        <Card className="mb-8">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Package className="h-5 w-5" />
              Order Details
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-2 gap-4 text-sm">
              <div>
                <p className="text-muted-foreground">Order Number</p>
                <p className="font-medium">#{order.increment_id}</p>
              </div>
              <div>
                <p className="text-muted-foreground">Status</p>
                <p className="font-medium capitalize">{order.status}</p>
              </div>
              <div>
                <p className="text-muted-foreground">Date</p>
                <p className="font-medium">
                  {new Date(order.created_at).toLocaleDateString()}
                </p>
              </div>
              <div>
                <p className="text-muted-foreground">Email</p>
                <p className="font-medium">{order.customer_email}</p>
              </div>
            </div>

            <Separator />

            {order.items && order.items.length > 0 && (
              <div>
                <h3 className="text-sm font-medium text-muted-foreground mb-2">
                  Items
                </h3>
                <div className="space-y-2">
                  {order.items.map((item) => (
                    <div
                      key={item.uuid}
                      className="flex justify-between text-sm"
                    >
                      <span>
                        {item.name}{" "}
                        <span className="text-muted-foreground">
                          x{item.quantity}
                        </span>
                      </span>
                      <span>${item.row_total}</span>
                    </div>
                  ))}
                </div>
              </div>
            )}

            <Separator />

            <div className="space-y-1 text-sm">
              <div className="flex justify-between">
                <span className="text-muted-foreground">Subtotal</span>
                <span>${order.subtotal}</span>
              </div>
              {parseFloat(order.shipping_amount) > 0 && (
                <div className="flex justify-between">
                  <span className="text-muted-foreground">
                    Shipping ({order.shipping_method_label})
                  </span>
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
                <span>Total</span>
                <span>${order.grand_total}</span>
              </div>
            </div>

            {order.shipping_method_label && (
              <>
                <Separator />
                <div className="text-sm">
                  <p className="text-muted-foreground">Shipping Method</p>
                  <p>{order.shipping_method_label}</p>
                </div>
              </>
            )}

            {order.payment_method_label && (
              <div className="text-sm">
                <p className="text-muted-foreground">Payment Method</p>
                <p>{order.payment_method_label}</p>
              </div>
            )}
          </CardContent>
        </Card>
      )}

      <div className="flex flex-col sm:flex-row gap-4 justify-center">
        {isAuthenticated && (
          <Button asChild variant="outline">
            <Link to="/account/orders">View All Orders</Link>
          </Button>
        )}
        <Button asChild>
          <Link to="/products">Continue Shopping</Link>
        </Button>
      </div>
    </div>
  );
}
