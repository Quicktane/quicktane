import { useState } from "react";
import { ImageOff } from "lucide-react";
import { cn } from "@/lib/utils";
import type { ProductMedia } from "@/types/catalog";

interface ProductGalleryProps {
  media: ProductMedia[];
  productName: string;
}

export function ProductGallery({ media, productName }: ProductGalleryProps) {
  const sorted = [...media].sort((a, b) => a.position - b.position);
  const [selectedIndex, setSelectedIndex] = useState(0);
  const selectedImage = sorted[selectedIndex];

  if (sorted.length === 0) {
    return (
      <div className="aspect-square bg-muted rounded-lg flex items-center justify-center text-muted-foreground">
        <ImageOff className="h-24 w-24" />
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <div className="aspect-square bg-muted rounded-lg overflow-hidden">
        {selectedImage && (
          <img
            src={selectedImage.url}
            alt={selectedImage.alt_text ?? productName}
            className="w-full h-full object-cover"
          />
        )}
      </div>
      {sorted.length > 1 && (
        <div className="flex gap-2 overflow-x-auto">
          {sorted.map((image, index) => (
            <button
              key={image.uuid}
              onClick={() => setSelectedIndex(index)}
              className={cn(
                "w-20 h-20 rounded-md overflow-hidden border-2 flex-shrink-0 transition-colors",
                index === selectedIndex
                  ? "border-primary"
                  : "border-transparent hover:border-muted-foreground/30",
              )}
            >
              <img
                src={image.url}
                alt={image.alt_text ?? `${productName} ${index + 1}`}
                className="w-full h-full object-cover"
              />
            </button>
          ))}
        </div>
      )}
    </div>
  );
}
