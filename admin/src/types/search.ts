export interface SearchSynonym {
  id: number;
  uuid: string;
  term: string;
  synonyms: string[];
  store_view_id: number;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}
