import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from "react";
import { api, getAuthToken, setAuthToken, clearAuthToken } from "@/lib/api";
import { STORE_ID } from "@/lib/config";
import type { Customer, LoginResponse, RegisterResponse } from "@/types/customer";

interface AuthContextType {
  customer: Customer | null;
  isLoading: boolean;
  isAuthenticated: boolean;
  login: (email: string, password: string) => Promise<void>;
  register: (data: RegisterData) => Promise<void>;
  logout: () => Promise<void>;
  updateCustomer: (customer: Customer) => void;
}

interface RegisterData {
  first_name: string;
  last_name: string;
  email: string;
  password: string;
  password_confirmation: string;
}

const AuthContext = createContext<AuthContextType | null>(null);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [customer, setCustomer] = useState<Customer | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  const hydrate = useCallback(async () => {
    const token = getAuthToken();

    if (!token) {
      setIsLoading(false);
      return;
    }

    try {
      const response = await api.get<{ data: Customer }>("/customer/me");
      setCustomer(response.data.data);
    } catch {
      clearAuthToken();
      setCustomer(null);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    hydrate();
  }, [hydrate]);

  const login = useCallback(async (email: string, password: string) => {
    const response = await api.post<LoginResponse>("/customer/login", {
      store_id: STORE_ID,
      email,
      password,
    });

    setAuthToken(response.data.token);
    setCustomer(response.data.customer);
  }, []);

  const register = useCallback(async (data: RegisterData) => {
    const response = await api.post<RegisterResponse>("/customer/register", {
      store_id: STORE_ID,
      ...data,
    });

    setAuthToken(response.data.token);
    setCustomer(response.data.customer);
  }, []);

  const logout = useCallback(async () => {
    try {
      await api.post("/customer/logout");
    } catch {
      // Ignore logout errors
    } finally {
      clearAuthToken();
      setCustomer(null);
    }
  }, []);

  const updateCustomer = useCallback((updatedCustomer: Customer) => {
    setCustomer(updatedCustomer);
  }, []);

  const value = useMemo(
    () => ({
      customer,
      isLoading,
      isAuthenticated: customer !== null,
      login,
      register,
      logout,
      updateCustomer,
    }),
    [customer, isLoading, login, register, logout, updateCustomer],
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth(): AuthContextType {
  const context = useContext(AuthContext);

  if (!context) {
    throw new Error("useAuth must be used within an AuthProvider");
  }

  return context;
}
