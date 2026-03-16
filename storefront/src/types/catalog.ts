export interface AttributeOption {
  id: number;
  label: string;
  value: string;
  sort_order: number;
}

export interface Attribute {
  uuid: string;
  code: string;
  name: string;
  type: string;
  is_required: boolean;
  is_filterable?: boolean;
  is_visible?: boolean;
  sort_order: number;
  validation_rules: Record<string, unknown> | null;
  created_at: string;
  updated_at: string;
  options?: AttributeOption[];
}

export interface AttributeSetAttribute {
  uuid: string;
  code: string;
  name: string;
  type: string;
  is_required: boolean;
  group_name: string;
  sort_order: number;
}

export interface AttributeSet {
  uuid: string;
  name: string;
  sort_order: number;
  created_at: string;
  updated_at: string;
  attributes?: AttributeSetAttribute[];
}

export interface AttributeValue {
  id: number;
  attribute_id: number;
  value: string | null;
  attribute?: Attribute;
}

export interface ProductMedia {
  media_file_id: number;
  uuid: string;
  filename: string;
  mime_type: string;
  url: string;
  alt_text: string | null;
  position: number;
  label: string | null;
  is_main: boolean;
}

export interface Category {
  uuid: string;
  parent_id: number | null;
  name: string;
  slug: string;
  description: string | null;
  path: string;
  level: number;
  position: number;
  is_active: boolean;
  include_in_menu: boolean;
  meta_title: string | null;
  meta_description: string | null;
  created_at: string;
  updated_at: string;
  children?: Category[];
  products?: Product[];
}

export interface Product {
  uuid: string;
  type: string;
  sku: string;
  name: string;
  slug: string;
  description: string | null;
  short_description: string | null;
  base_price: string;
  special_price: string | null;
  special_price_from: string | null;
  special_price_to: string | null;
  cost: string | null;
  weight: string | null;
  is_active: boolean;
  meta_title: string | null;
  meta_description: string | null;
  created_at: string;
  updated_at: string;
  resolved_price?: string;
  is_on_sale?: boolean;
  attribute_set?: AttributeSet;
  attribute_values?: AttributeValue[];
  categories?: Category[];
  media?: ProductMedia[];
}

export interface PaginationMeta {
  current_page: number;
  from: number | null;
  last_page: number;
  per_page: number;
  to: number | null;
  total: number;
}

export interface PaginationLinks {
  first: string | null;
  last: string | null;
  prev: string | null;
  next: string | null;
}

export interface PaginatedResponse<T> {
  data: T[];
  links: PaginationLinks;
  meta: PaginationMeta;
}
