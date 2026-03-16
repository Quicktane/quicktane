import { Separator } from "@/components/ui/separator";
import type { AttributeValue } from "@/types/catalog";

interface ProductAttributesProps {
  attributeValues: AttributeValue[];
}

export function ProductAttributes({ attributeValues }: ProductAttributesProps) {
  const visibleAttributes = attributeValues.filter(
    (attributeValue) => attributeValue.value !== null && attributeValue.attribute,
  );

  if (visibleAttributes.length === 0) {
    return null;
  }

  return (
    <div>
      <h3 className="font-semibold mb-3">Product Details</h3>
      <div className="space-y-0">
        {visibleAttributes.map((attributeValue, index) => (
          <div key={attributeValue.id}>
            <div className="flex justify-between py-2 text-sm">
              <span className="text-muted-foreground">
                {attributeValue.attribute?.name}
              </span>
              <span className="font-medium">{attributeValue.value}</span>
            </div>
            {index < visibleAttributes.length - 1 && <Separator />}
          </div>
        ))}
      </div>
    </div>
  );
}
