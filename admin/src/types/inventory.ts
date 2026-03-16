export interface InventorySource {
  uuid: string;
  code: string;
  name: string;
  description: string | null;
  country_code: string | null;
  city: string | null;
  address: string | null;
  is_active: boolean;
  sort_order: number;
  created_at: string;
  updated_at: string;
}

export interface StockItem {
  product_id: number;
  source_id: number;
  quantity: number;
  reserved: number;
  notify_quantity: number;
  is_in_stock: boolean;
  salable_quantity: number;
  created_at: string;
  updated_at: string;
  source?: InventorySource;
  product?: {
    uuid: string;
    sku: string;
    name: string;
  };
}

export interface StockInfo {
  product_id: number;
  total_quantity: number;
  total_reserved: number;
  salable_quantity: number;
  is_in_stock: boolean;
  sources: StockItem[];
}
