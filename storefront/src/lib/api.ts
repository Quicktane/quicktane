import axios from "axios";

const AUTH_TOKEN_KEY = "quicktane_customer_token";
const CART_TOKEN_KEY = "quicktane_cart_token";
const API_BASE_URL = import.meta.env.VITE_API_URL || "";

const api = axios.create({
  baseURL: `${API_BASE_URL}/api/v1`,
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
});

api.interceptors.request.use((config) => {
  const authToken = localStorage.getItem(AUTH_TOKEN_KEY);
  if (authToken) {
    config.headers.Authorization = `Bearer ${authToken}`;
  }

  const cartToken = localStorage.getItem(CART_TOKEN_KEY);
  if (cartToken) {
    config.headers["X-Cart-Token"] = cartToken;
  }

  return config;
});

api.interceptors.response.use(
  (response) => {
    const cartToken = response.headers["x-cart-token"];
    if (cartToken) {
      localStorage.setItem(CART_TOKEN_KEY, cartToken);
    }
    return response;
  },
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem(AUTH_TOKEN_KEY);
    }
    return Promise.reject(error);
  },
);

export function getAuthToken(): string | null {
  return localStorage.getItem(AUTH_TOKEN_KEY);
}

export function setAuthToken(token: string): void {
  localStorage.setItem(AUTH_TOKEN_KEY, token);
}

export function clearAuthToken(): void {
  localStorage.removeItem(AUTH_TOKEN_KEY);
}

export function getCartToken(): string | null {
  return localStorage.getItem(CART_TOKEN_KEY);
}

export function clearCartToken(): void {
  localStorage.removeItem(CART_TOKEN_KEY);
}

export { api };
