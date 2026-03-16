import { Link } from "react-router";
import { ImageOff } from "lucide-react";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { ProductPrice } from "./ProductPrice";
import { AddToCartButton } from "./AddToCartButton";
import type { Product } from "@/types/catalog";

interface ProductCardProps {
  product: Product;
}

export function ProductCard({ product }: ProductCardProps) {
  const mainImage = product.media?.find((media) => media.is_main) ?? product.media?.[0];

  return (
    <Card className="group overflow-hidden">
      <Link to={`/products/${product.slug}`}>
        <div className="aspect-square relative bg-muted overflow-hidden">
          {mainImage ? (
            <img
              src={mainImage.url}
              alt={mainImage.alt_text ?? product.name}
              className="object-cover w-full h-full group-hover:scale-105 transition-transform duration-300"
            />
          ) : (
            <div className="flex items-center justify-center w-full h-full text-muted-foreground">
              <ImageOff className="h-12 w-12" />
            </div>
          )}
          {product.is_on_sale && (
            <Badge className="absolute top-2 left-2" variant="destructive">
              Sale
            </Badge>
          )}
        </div>
      </Link>
      <CardContent className="p-4 space-y-2">
        <Link
          to={`/products/${product.slug}`}
          className="font-medium hover:text-primary transition-colors line-clamp-2"
        >
          {product.name}
        </Link>
        <ProductPrice product={product} />
        <AddToCartButton productUuid={product.uuid} productName={product.name} />
      </CardContent>
    </Card>
  );
}
