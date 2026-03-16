import { Separator } from "@/components/ui/separator";
import type { Cart } from "@/types/cart";

interface CartSummaryProps {
  cart: Cart;
}

export function CartSummary({ cart }: CartSummaryProps) {
  return (
    <div className="space-y-3">
      <div className="flex justify-between text-sm">
        <span className="text-muted-foreground">
          Items ({cart.items_count})
        </span>
        <span>${cart.subtotal}</span>
      </div>
      <Separator />
      <div className="flex justify-between font-semibold text-lg">
        <span>Subtotal</span>
        <span>${cart.subtotal}</span>
      </div>
    </div>
  );
}
