import { useState } from "react";
import { Minus, Plus, Trash2, Loader2 } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { useCart } from "@/contexts/CartContext";
import { toast } from "sonner";
import type { CartItem } from "@/types/cart";

interface CartItemRowProps {
  item: CartItem;
}

export function CartItemRow({ item }: CartItemRowProps) {
  const { updateItem, removeItem } = useCart();
  const [isUpdating, setIsUpdating] = useState(false);
  const [isRemoving, setIsRemoving] = useState(false);

  const handleUpdateQuantity = async (newQuantity: number) => {
    if (newQuantity < 1) return;
    setIsUpdating(true);
    try {
      await updateItem(item.uuid, newQuantity);
    } catch {
      toast.error("Failed to update quantity");
    } finally {
      setIsUpdating(false);
    }
  };

  const handleRemove = async () => {
    setIsRemoving(true);
    try {
      await removeItem(item.uuid);
      toast.success(`${item.name} removed from cart`);
    } catch {
      toast.error("Failed to remove item");
      setIsRemoving(false);
    }
  };

  return (
    <div className="flex items-center gap-4 py-4">
      <div className="flex-1 min-w-0">
        <h4 className="font-medium truncate">{item.name}</h4>
        <p className="text-sm text-muted-foreground">SKU: {item.sku}</p>
        <p className="text-sm text-muted-foreground">
          ${item.unit_price} each
        </p>
      </div>

      <div className="flex items-center border rounded-md">
        <Button
          variant="ghost"
          size="icon"
          className="h-8 w-8 rounded-r-none"
          onClick={() => handleUpdateQuantity(item.quantity - 1)}
          disabled={isUpdating || item.quantity <= 1}
        >
          <Minus className="h-3 w-3" />
        </Button>
        <Input
          type="number"
          min={1}
          value={item.quantity}
          onChange={(event) => {
            const value = parseInt(event.target.value, 10);
            if (!isNaN(value) && value >= 1) {
              handleUpdateQuantity(value);
            }
          }}
          disabled={isUpdating}
          className="h-8 w-14 border-0 text-center rounded-none text-sm [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
        />
        <Button
          variant="ghost"
          size="icon"
          className="h-8 w-8 rounded-l-none"
          onClick={() => handleUpdateQuantity(item.quantity + 1)}
          disabled={isUpdating}
        >
          <Plus className="h-3 w-3" />
        </Button>
      </div>

      <div className="w-24 text-right font-medium">${item.row_total}</div>

      <Button
        variant="ghost"
        size="icon"
        className="text-destructive hover:text-destructive"
        onClick={handleRemove}
        disabled={isRemoving}
      >
        {isRemoving ? (
          <Loader2 className="h-4 w-4 animate-spin" />
        ) : (
          <Trash2 className="h-4 w-4" />
        )}
      </Button>
    </div>
  );
}
