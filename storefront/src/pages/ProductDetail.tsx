import { useEffect, useState } from "react";
import { useParams, Link } from "react-router";
import { ChevronRight } from "lucide-react";
import { api } from "@/lib/api";
import { Separator } from "@/components/ui/separator";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";
import { ProductGallery } from "@/components/product/ProductGallery";
import { ProductPrice } from "@/components/product/ProductPrice";
import { ProductAttributes } from "@/components/product/ProductAttributes";
import { AddToCartButton } from "@/components/product/AddToCartButton";
import type { Product } from "@/types/catalog";

export function ProductDetail() {
  const { slug } = useParams<{ slug: string }>();
  const [product, setProduct] = useState<Product | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!slug) return;

    setIsLoading(true);
    setError(null);
    api
      .get<{ data: Product }>(`/catalog/products/${slug}`)
      .then((response) => setProduct(response.data.data))
      .catch(() => setError("Product not found"))
      .finally(() => setIsLoading(false));
  }, [slug]);

  if (isLoading) {
    return (
      <div className="container mx-auto px-4 py-8">
        <div className="grid md:grid-cols-2 gap-8">
          <Skeleton className="aspect-square rounded-lg" />
          <div className="space-y-4">
            <Skeleton className="h-8 w-3/4" />
            <Skeleton className="h-6 w-1/4" />
            <Skeleton className="h-24 w-full" />
          </div>
        </div>
      </div>
    );
  }

  if (error || !product) {
    return (
      <div className="container mx-auto px-4 py-16 text-center">
        <h1 className="text-2xl font-bold mb-2">Product Not Found</h1>
        <p className="text-muted-foreground mb-4">
          The product you are looking for does not exist.
        </p>
        <Link to="/products" className="text-primary hover:underline">
          Browse all products
        </Link>
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-8">
      {product.categories && product.categories.length > 0 && (
        <nav className="flex items-center gap-1 text-sm text-muted-foreground mb-6">
          <Link to="/" className="hover:text-foreground">
            Home
          </Link>
          <ChevronRight className="h-3 w-3" />
          <Link
            to={`/categories/${product.categories[0]?.slug}`}
            className="hover:text-foreground"
          >
            {product.categories[0]?.name}
          </Link>
          <ChevronRight className="h-3 w-3" />
          <span className="text-foreground">{product.name}</span>
        </nav>
      )}

      <div className="grid md:grid-cols-2 gap-8">
        <ProductGallery media={product.media ?? []} productName={product.name} />

        <div className="space-y-6">
          <div>
            <div className="flex items-center gap-2 mb-2">
              {product.is_on_sale && (
                <Badge variant="destructive">Sale</Badge>
              )}
              <span className="text-sm text-muted-foreground">
                SKU: {product.sku}
              </span>
            </div>
            <h1 className="text-3xl font-bold">{product.name}</h1>
          </div>

          <ProductPrice product={product} className="text-2xl" />

          {product.short_description && (
            <p className="text-muted-foreground">
              {product.short_description}
            </p>
          )}

          <AddToCartButton
            productUuid={product.uuid}
            productName={product.name}
            showQuantity
          />

          <Separator />

          {product.description && (
            <div>
              <h3 className="font-semibold mb-2">Description</h3>
              <p className="text-muted-foreground whitespace-pre-line">
                {product.description}
              </p>
            </div>
          )}

          {product.attribute_values && product.attribute_values.length > 0 && (
            <>
              <Separator />
              <ProductAttributes attributeValues={product.attribute_values} />
            </>
          )}
        </div>
      </div>
    </div>
  );
}
