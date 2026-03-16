export interface Region {
  id: number;
  country_id: number;
  code: string;
  name: string;
  sort_order: number;
  is_active: boolean;
}

export interface Country {
  id: number;
  iso2: string;
  iso3: string;
  name: string;
  numeric_code: string | null;
  phone_code: string | null;
  is_active: boolean;
  sort_order: number;
  regions?: Region[];
}

export interface Currency {
  id: number;
  code: string;
  name: string;
  symbol: string;
  decimal_places: number;
  is_active: boolean;
  sort_order: number;
}
