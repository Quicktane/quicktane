import { PackageOpen } from "lucide-react";
import { ProductCard } from "./ProductCard";
import type { Product } from "@/types/catalog";

interface ProductGridProps {
  products: Product[];
}

export function ProductGrid({ products }: ProductGridProps) {
  if (products.length === 0) {
    return (
      <div className="flex flex-col items-center justify-center py-16 text-muted-foreground">
        <PackageOpen className="h-16 w-16 mb-4" />
        <p className="text-lg font-medium">No products found</p>
      </div>
    );
  }

  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      {products.map((product) => (
        <ProductCard key={product.uuid} product={product} />
      ))}
    </div>
  );
}
