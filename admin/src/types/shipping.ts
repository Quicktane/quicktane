export interface ShippingMethod {
  id: number;
  uuid: string;
  code: string;
  name: string;
  carrier_code: string;
  description: string | null;
  is_active: boolean;
  sort_order: number;
  created_at: string;
}

export interface ShippingZone {
  id: number;
  uuid: string;
  name: string;
  is_active: boolean;
  countries?: ShippingZoneCountry[];
  created_at: string;
}

export interface ShippingZoneCountry {
  id: number;
  country_id: number;
  region_id: number | null;
}

export interface ShippingRate {
  id: number;
  uuid: string;
  shipping_method_id: number;
  shipping_zone_id: number;
  method?: ShippingMethod;
  zone?: ShippingZone;
  price: string;
  is_active: boolean;
  created_at: string;
}
