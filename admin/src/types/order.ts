export interface Order {
  id: number;
  uuid: string;
  increment_id: string;
  customer_email: string;
  status: string;
  subtotal: string;
  shipping_amount: string;
  discount_amount: string;
  tax_amount: string;
  grand_total: string;
  total_paid: string;
  total_refunded: string;
  currency_code: string;
  shipping_method_label: string | null;
  payment_method_label: string | null;
  coupon_code: string | null;
  total_quantity: number;
  customer?: { uuid: string; email: string; name: string } | null;
  items?: OrderItem[];
  addresses?: OrderAddress[];
  history?: OrderHistoryEntry[];
  invoices?: Invoice[];
  credit_memos?: CreditMemo[];
  created_at: string;
  updated_at: string;
}

export interface OrderItem {
  id: number;
  uuid: string;
  sku: string;
  name: string;
  quantity: number;
  unit_price: string;
  row_total: string;
  discount_amount: string;
  tax_amount: string;
  options: Record<string, unknown> | null;
}

export interface OrderAddress {
  id: number;
  type: "shipping" | "billing";
  first_name: string;
  last_name: string;
  company: string | null;
  street_line_1: string;
  street_line_2: string | null;
  city: string;
  region_name: string | null;
  postcode: string;
  country_name: string;
  phone: string | null;
}

export interface OrderHistoryEntry {
  id: number;
  status: string;
  comment: string | null;
  is_customer_notified: boolean;
  created_at: string;
}

export interface Invoice {
  id: number;
  uuid: string;
  increment_id: string;
  status: string;
  grand_total: string;
  created_at: string;
}

export interface CreditMemo {
  id: number;
  uuid: string;
  increment_id: string;
  status: string;
  grand_total: string;
  created_at: string;
}

export interface InvoiceListItem {
  id: number;
  uuid: string;
  increment_id: string;
  status: string;
  subtotal: string;
  shipping_amount: string;
  discount_amount: string;
  tax_amount: string;
  grand_total: string;
  order_increment_id?: string;
  order_uuid?: string;
  created_at: string;
}
