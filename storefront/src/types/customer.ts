export interface CustomerGroup {
  uuid: string;
  code: string;
  name: string;
  is_default: boolean;
  sort_order: number;
  created_at: string;
  updated_at: string;
}

export interface Customer {
  uuid: string;
  email: string;
  first_name: string;
  last_name: string;
  phone: string | null;
  date_of_birth: string | null;
  gender: "male" | "female" | "other" | null;
  is_active: boolean;
  group?: CustomerGroup;
  last_login_at: string | null;
  created_at: string;
  updated_at: string;
  addresses?: CustomerAddress[];
}

export interface CustomerAddress {
  uuid: string;
  first_name: string;
  last_name: string;
  company: string | null;
  street_line_1: string;
  street_line_2: string | null;
  city: string;
  region_id: number | null;
  postcode: string;
  country_id: string;
  phone: string | null;
  is_default_billing: boolean;
  is_default_shipping: boolean;
  created_at: string;
  updated_at: string;
}

export interface LoginResponse {
  token: string;
  customer: Customer;
}

export interface RegisterResponse {
  token: string;
  customer: Customer;
}
