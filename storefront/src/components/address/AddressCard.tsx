import { Pencil, Trash2 } from "lucide-react";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import type { CustomerAddress } from "@/types/customer";

interface AddressCardProps {
  address: CustomerAddress;
  onEdit: (address: CustomerAddress) => void;
  onDelete: (uuid: string) => void;
}

export function AddressCard({ address, onEdit, onDelete }: AddressCardProps) {
  return (
    <Card>
      <CardContent className="p-4 space-y-2">
        <div className="flex items-start justify-between">
          <div>
            <p className="font-medium">
              {address.first_name} {address.last_name}
            </p>
            {address.company && (
              <p className="text-sm text-muted-foreground">{address.company}</p>
            )}
          </div>
          <div className="flex gap-1">
            <Button variant="ghost" size="icon" className="h-8 w-8" onClick={() => onEdit(address)}>
              <Pencil className="h-4 w-4" />
            </Button>
            <Button
              variant="ghost"
              size="icon"
              className="h-8 w-8 text-destructive hover:text-destructive"
              onClick={() => onDelete(address.uuid)}
            >
              <Trash2 className="h-4 w-4" />
            </Button>
          </div>
        </div>

        <div className="text-sm space-y-0.5">
          <p>{address.street_line_1}</p>
          {address.street_line_2 && <p>{address.street_line_2}</p>}
          <p>
            {address.city}, {address.postcode}
          </p>
          <p>{address.country_id}</p>
          {address.phone && <p>{address.phone}</p>}
        </div>

        <div className="flex gap-2 pt-1">
          {address.is_default_billing && (
            <Badge variant="secondary">Default Billing</Badge>
          )}
          {address.is_default_shipping && (
            <Badge variant="secondary">Default Shipping</Badge>
          )}
        </div>
      </CardContent>
    </Card>
  );
}
