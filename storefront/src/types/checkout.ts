export interface CheckoutAddress {
  first_name: string;
  last_name: string;
  company?: string;
  street_line_1: string;
  street_line_2?: string;
  city: string;
  region_id?: number | null;
  postcode: string;
  country_id: string;
  phone?: string;
}

export interface CheckoutSession {
  uuid: string;
  cart_id: number;
  customer_id: number | null;
  shipping_address: CheckoutAddress | null;
  billing_address: CheckoutAddress | null;
  shipping_method_code: string | null;
  shipping_method_label: string | null;
  shipping_amount: string | null;
  payment_method_code: string | null;
  coupon_code: string | null;
  totals: CheckoutTotals | null;
  step: string | null;
  expires_at: string;
}

export interface CheckoutTotals {
  subtotal: string;
  shipping_amount: string;
  discount_amount: string;
  tax_amount: string;
  grand_total: string;
  breakdown: TotalsBreakdownItem[];
  free_shipping: boolean;
}

export interface TotalsBreakdownItem {
  label: string;
  amount: string;
  type: string;
}

export interface ShippingRateOption {
  carrier_code: string;
  method_code: string;
  label: string;
  price: string;
  estimated_days: number | null;
}

export interface PaymentMethod {
  uuid: string;
  code: string;
  name: string;
  gateway_code: string;
  description: string | null;
  is_active: boolean;
  sort_order: number;
  min_order_amount: string | null;
  max_order_amount: string | null;
}

export interface PlaceOrderResult {
  success: boolean;
  order: Order | null;
  suspended: boolean;
  pipeline_token: string | null;
  redirect_url: string | null;
  errors: string[];
}

export interface Order {
  id: number;
  uuid: string;
  increment_id: string;
  store_id: number;
  customer_id: number | null;
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
  customer?: {
    uuid: string;
    email: string;
    name: string;
  };
  items?: OrderItem[];
  addresses?: OrderAddress[];
  created_at: string;
  updated_at: string;
}

export interface OrderItem {
  uuid: string;
  sku: string;
  name: string;
  quantity: number;
  unit_price: string;
  row_total: string;
  discount_amount: string;
  tax_amount: string;
  tax_rate: string;
  weight: string | null;
  options: Record<string, string> | null;
}

export interface OrderAddress {
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
