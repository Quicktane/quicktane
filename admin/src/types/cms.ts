export interface Page {
  id: number;
  uuid: string;
  identifier: string;
  title: string;
  content: string | null;
  meta_title: string | null;
  meta_description: string | null;
  meta_keywords: string | null;
  is_active: boolean;
  sort_order: number;
  layout: string | null;
  store_view_ids?: number[];
  created_at: string;
  updated_at: string;
}

export interface Block {
  id: number;
  uuid: string;
  identifier: string;
  title: string;
  content: string | null;
  is_active: boolean;
  store_view_ids?: number[];
  created_at: string;
  updated_at: string;
}

export interface UrlRewrite {
  id: number;
  uuid: string;
  entity_type: string;
  entity_id: number | null;
  request_path: string;
  target_path: string;
  redirect_type: number | null;
  store_view_id: number;
  description: string | null;
  created_at: string;
  updated_at: string;
}
