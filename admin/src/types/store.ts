export interface Website {
  uuid: string;
  code: string;
  name: string;
  sort_order: number;
  is_active: boolean;
  created_at: string;
  updated_at: string;
  stores?: Store[];
}

export interface Store {
  id: number;
  uuid: string;
  code: string;
  name: string;
  website_id: number;
  root_category_id: number | null;
  sort_order: number;
  is_active: boolean;
  created_at: string;
  updated_at: string;
  website?: Website;
  store_views?: StoreView[];
}

export interface StoreView {
  uuid: string;
  code: string;
  name: string;
  store_id: number;
  locale: string | null;
  sort_order: number;
  is_active: boolean;
  created_at: string;
  updated_at: string;
  store?: Store;
}

export interface Configuration {
  id: number;
  scope: ConfigurationScope;
  scope_id: number;
  path: string;
  value: string | null;
  created_at: string;
  updated_at: string;
}

export type ConfigurationScope = "global" | "website" | "store" | "store_view";

export interface ConfigurationResponse {
  path: string;
  value: string | null;
  scope: ConfigurationScope;
  scope_id: number;
}
