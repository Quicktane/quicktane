import { cn } from "@/lib/utils";
import type { Product } from "@/types/catalog";

interface ProductPriceProps {
  product: Product;
  className?: string;
}

export function ProductPrice({ product, className }: ProductPriceProps) {
  const resolvedPrice = product.resolved_price ?? product.base_price;
  const isOnSale = product.is_on_sale ?? false;

  return (
    <div className={cn("flex items-center gap-2", className)}>
      <span
        className={cn(
          "font-semibold",
          isOnSale ? "text-destructive" : "text-foreground",
        )}
      >
        ${resolvedPrice}
      </span>
      {isOnSale && (
        <span className="text-sm text-muted-foreground line-through">
          ${product.base_price}
        </span>
      )}
    </div>
  );
}
