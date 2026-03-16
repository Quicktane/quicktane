import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from "react";
import { api } from "@/lib/api";
import { STORE_ID, CURRENCY_CODE } from "@/lib/config";
import type { Cart } from "@/types/cart";
import { useAuth } from "./AuthContext";

interface CartContextType {
  cart: Cart | null;
  isLoading: boolean;
  itemCount: number;
  addItem: (productUuid: string, quantity: number) => Promise<void>;
  updateItem: (itemUuid: string, quantity: number) => Promise<void>;
  removeItem: (itemUuid: string) => Promise<void>;
  clearCart: () => Promise<void>;
  refreshCart: () => Promise<void>;
}

const CartContext = createContext<CartContextType | null>(null);

export function CartProvider({ children }: { children: ReactNode }) {
  const [cart, setCart] = useState<Cart | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const { isAuthenticated } = useAuth();

  const fetchCart = useCallback(async () => {
    try {
      const response = await api.get<{ data: Cart | null }>("/cart");
      setCart(response.data.data);
    } catch {
      setCart(null);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchCart();
  }, [fetchCart, isAuthenticated]);

  const addItem = useCallback(
    async (productUuid: string, quantity: number) => {
      const response = await api.post<{ data: Cart }>("/cart/items", {
        product_uuid: productUuid,
        quantity,
        store_id: STORE_ID,
        currency_code: CURRENCY_CODE,
      });
      setCart(response.data.data);
    },
    [],
  );

  const updateItem = useCallback(
    async (itemUuid: string, quantity: number) => {
      await api.put(`/cart/items/${itemUuid}`, { quantity });
      await fetchCart();
    },
    [fetchCart],
  );

  const removeItem = useCallback(
    async (itemUuid: string) => {
      await api.delete(`/cart/items/${itemUuid}`);
      await fetchCart();
    },
    [fetchCart],
  );

  const clearCart = useCallback(async () => {
    await api.delete("/cart");
    setCart(null);
  }, []);

  const value = useMemo(
    () => ({
      cart,
      isLoading,
      itemCount: cart?.items_count ?? 0,
      addItem,
      updateItem,
      removeItem,
      clearCart,
      refreshCart: fetchCart,
    }),
    [cart, isLoading, addItem, updateItem, removeItem, clearCart, fetchCart],
  );

  return <CartContext.Provider value={value}>{children}</CartContext.Provider>;
}

export function useCart(): CartContextType {
  const context = useContext(CartContext);

  if (!context) {
    throw new Error("useCart must be used within a CartProvider");
  }

  return context;
}
