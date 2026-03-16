export interface Permission {
  id: number;
  name: string;
  slug: string;
  module: string;
  group_name: string;
}

export interface Role {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  is_system: boolean;
  permissions?: Permission[];
}

export interface User {
  uuid: string;
  first_name: string;
  last_name: string;
  email: string;
  is_active: boolean;
  last_login_at: string | null;
  created_at: string;
  updated_at: string;
  role?: Role;
}

export interface LoginResponse {
  token: string;
  user: User;
}
