import { useCallback, useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { CartDetail, CartItem } from "@/types/cart";
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

const statusVariant: Record<string, "default" | "secondary" | "outline" | "destructive"> = {
  active: "default",
  converted: "secondary",
  abandoned: "destructive",
  merged: "outline",
};

function CartItemsTable({ items }: { items: CartItem[] }) {
  if (items.length === 0) {
    return (
      <p className="py-8 text-center text-sm text-muted-foreground">
        No items in this cart.
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
          <TableHead className="text-right">Row Total</TableHead>
          <TableHead>Price Snapshot</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        {items.map((item) => (
          <TableRow key={item.uuid}>
            <TableCell className="font-mono text-sm">{item.sku}</TableCell>
            <TableCell>{item.name}</TableCell>
            <TableCell className="text-right">{item.quantity}</TableCell>
            <TableCell className="text-right">{item.unit_price}</TableCell>
            <TableCell className="text-right font-medium">
              {item.row_total}
            </TableCell>
            <TableCell className="text-sm text-muted-foreground">
              {new Date(item.snapshotted_at).toLocaleDateString("en-US", {
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

export function CartsShow() {
  const { uuid } = useParams<{ uuid: string }>();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(true);
  const [cart, setCart] = useState<CartDetail | null>(null);

  const loadCart = useCallback(async () => {
    try {
      const response = await api.get<{ data: CartDetail }>(
        `/admin/cart/carts/${uuid}`,
      );
      setCart(response.data.data);
    } catch {
      toast.error("Failed to load cart");
      navigate("/carts");
    } finally {
      setIsLoading(false);
    }
  }, [uuid, navigate]);

  useEffect(() => {
    loadCart();
  }, [loadCart]);

  if (isLoading || !cart) {
    return (
      <div className="space-y-4">
        <Skeleton className="h-8 w-48" />
        <Skeleton className="h-32 w-full" />
        <Skeleton className="h-64 w-full" />
      </div>
    );
  }

  const status = cart.status;

  return (
    <div className="space-y-4">
      <PageHeader
        title="Cart Detail"
        description={`Cart ${cart.uuid}`}
        actions={
          <Badge variant={statusVariant[status] ?? "outline"}>
            {status.charAt(0).toUpperCase() + status.slice(1)}
          </Badge>
        }
      />

      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              Customer
            </CardTitle>
          </CardHeader>
          <CardContent>
            {cart.customer ? (
              <div>
                <p className="font-medium">{cart.customer.name}</p>
                <p className="text-sm text-muted-foreground">
                  {cart.customer.email}
                </p>
              </div>
            ) : (
              <p className="text-muted-foreground">Guest</p>
            )}
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              Items
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-2xl font-bold">{cart.items_count}</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              Subtotal
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-2xl font-bold">
              {cart.currency_code} {cart.subtotal}
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              Created
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p className="font-medium">
              {new Date(cart.created_at).toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
              })}
            </p>
            <p className="text-sm text-muted-foreground">
              Updated:{" "}
              {new Date(cart.updated_at).toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
              })}
            </p>
          </CardContent>
        </Card>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Cart Items</CardTitle>
        </CardHeader>
        <CardContent>
          <CartItemsTable items={cart.items} />
        </CardContent>
      </Card>

      <div>
        <Button variant="outline" onClick={() => navigate("/carts")}>
          Back to Carts
        </Button>
      </div>
    </div>
  );
}
