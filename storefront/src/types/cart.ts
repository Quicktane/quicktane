export interface CartItem {
  uuid: string;
  product_uuid: string;
  product_type: string;
  sku: string;
  name: string;
  quantity: number;
  unit_price: string;
  row_total: string;
  options: Record<string, string> | null;
  snapshotted_at: string;
  created_at: string;
  updated_at: string;
}

export interface Cart {
  uuid: string;
  status: "active" | "converted" | "abandoned" | "merged";
  currency_code: string;
  items_count: number;
  subtotal: string;
  guest_token: string | null;
  customer: { uuid: string; email: string; name: string } | null;
  items: CartItem[];
  created_at: string;
  updated_at: string;
}
