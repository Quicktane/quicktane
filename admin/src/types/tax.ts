export interface TaxClass {
  id: number;
  uuid: string;
  name: string;
  type: "product" | "customer";
  is_default: boolean;
  created_at: string;
}

export interface TaxZone {
  id: number;
  uuid: string;
  name: string;
  description: string | null;
  is_active: boolean;
  sort_order: number;
  rules?: TaxZoneRule[];
  created_at: string;
}

export interface TaxZoneRule {
  id: number;
  country_id: number;
  region_id: number | null;
  postcode_from: string | null;
  postcode_to: string | null;
}

export interface TaxRate {
  id: number;
  uuid: string;
  name: string;
  tax_zone_id: number;
  zone?: TaxZone;
  rate: string;
  priority: number;
  is_compound: boolean;
  is_active: boolean;
  created_at: string;
}

export interface TaxRule {
  id: number;
  uuid: string;
  name: string;
  tax_rate_id: number;
  tax_rate?: TaxRate;
  product_tax_class_id: number;
  product_tax_class?: TaxClass;
  customer_tax_class_id: number;
  customer_tax_class?: TaxClass;
  priority: number;
  is_active: boolean;
  created_at: string;
}
