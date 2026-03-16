export interface CartPriceRule {
  id: number;
  uuid: string;
  name: string;
  description: string | null;
  is_active: boolean;
  from_date: string | null;
  to_date: string | null;
  priority: number;
  stop_further_processing: boolean;
  action_type: "by_percent" | "by_fixed" | "buy_x_get_y" | "free_shipping";
  action_amount: string | null;
  max_discount_amount: string | null;
  apply_to_shipping: boolean;
  times_used: number;
  conditions?: RuleCondition[];
  coupons?: Coupon[];
  created_at: string;
}

export interface RuleCondition {
  id?: number;
  type: "combine" | "cart_attribute" | "product_attribute" | "customer_attribute";
  attribute?: string | null;
  operator?: string | null;
  value?: string | null;
  aggregator?: "all" | "any" | null;
  is_inverted: boolean;
  children?: RuleCondition[];
}

export interface Coupon {
  id: number;
  uuid: string;
  code: string;
  cart_price_rule_id: number;
  rule?: CartPriceRule;
  usage_limit: number | null;
  usage_per_customer: number | null;
  times_used: number;
  is_active: boolean;
  expires_at: string | null;
  created_at: string;
}
