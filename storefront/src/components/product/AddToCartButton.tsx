import { useState } from "react";
import { ShoppingCart, Minus, Plus, Loader2 } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { useCart } from "@/contexts/CartContext";
import { toast } from "sonner";

interface AddToCartButtonProps {
  productUuid: string;
  productName: string;
  showQuantity?: boolean;
}

export function AddToCartButton({
  productUuid,
  productName,
  showQuantity = false,
}: AddToCartButtonProps) {
  const [quantity, setQuantity] = useState(1);
  const [isAdding, setIsAdding] = useState(false);
  const { addItem } = useCart();

  const handleAdd = async () => {
    setIsAdding(true);
    try {
      await addItem(productUuid, quantity);
      toast.success(`${productName} added to cart`);
      setQuantity(1);
    } catch {
      toast.error("Failed to add item to cart");
    } finally {
      setIsAdding(false);
    }
  };

  if (!showQuantity) {
    return (
      <Button onClick={handleAdd} disabled={isAdding} size="sm" className="gap-2">
        {isAdding ? (
          <Loader2 className="h-4 w-4 animate-spin" />
        ) : (
          <ShoppingCart className="h-4 w-4" />
        )}
        Add to Cart
      </Button>
    );
  }

  return (
    <div className="flex items-center gap-3">
      <div className="flex items-center border rounded-md">
        <Button
          variant="ghost"
          size="icon"
          className="h-10 w-10 rounded-r-none"
          onClick={() => setQuantity((prev) => Math.max(1, prev - 1))}
          disabled={quantity <= 1}
        >
          <Minus className="h-4 w-4" />
        </Button>
        <Input
          type="number"
          min={1}
          value={quantity}
          onChange={(event) => {
            const value = parseInt(event.target.value, 10);
            if (!isNaN(value) && value >= 1) {
              setQuantity(value);
            }
          }}
          className="h-10 w-16 border-0 text-center rounded-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
        />
        <Button
          variant="ghost"
          size="icon"
          className="h-10 w-10 rounded-l-none"
          onClick={() => setQuantity((prev) => prev + 1)}
        >
          <Plus className="h-4 w-4" />
        </Button>
      </div>
      <Button onClick={handleAdd} disabled={isAdding} className="gap-2">
        {isAdding ? (
          <Loader2 className="h-4 w-4 animate-spin" />
        ) : (
          <ShoppingCart className="h-4 w-4" />
        )}
        Add to Cart
      </Button>
    </div>
  );
}
