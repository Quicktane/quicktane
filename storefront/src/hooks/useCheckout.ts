import { useCallback, useState } from "react";
import { api } from "@/lib/api";
import type { Cart } from "@/types/cart";
import type {
  CheckoutSession,
  CheckoutTotals,
  CheckoutAddress,
  ShippingRateOption,
  PaymentMethod,
  PlaceOrderResult,
} from "@/types/checkout";

export type CheckoutStep =
  | "shipping_address"
  | "shipping_method"
  | "payment"
  | "review";

interface UseCheckoutReturn {
  session: CheckoutSession | null;
  totals: CheckoutTotals | null;
  shippingRates: ShippingRateOption[];
  paymentMethods: PaymentMethod[];
  currentStep: CheckoutStep;
  isLoading: boolean;
  error: string | null;
  startCheckout: (cartUuid: string) => Promise<void>;
  setShippingAddress: (address: CheckoutAddress) => Promise<void>;
  setBillingAddress: (address: CheckoutAddress) => Promise<void>;
  setShippingMethod: (
    carrierCode: string,
    methodCode: string,
  ) => Promise<void>;
  setPaymentMethod: (paymentMethodCode: string) => Promise<void>;
  applyCoupon: (couponCode: string) => Promise<void>;
  removeCoupon: () => Promise<void>;
  fetchTotals: () => Promise<void>;
  placeOrder: () => Promise<PlaceOrderResult>;
  setCurrentStep: (step: CheckoutStep) => void;
  fetchShippingRates: (address: CheckoutAddress) => Promise<void>;
  fetchPaymentMethods: () => Promise<void>;
}

export function useCheckout(): UseCheckoutReturn {
  const [session, setSession] = useState<CheckoutSession | null>(null);
  const [totals, setTotals] = useState<CheckoutTotals | null>(null);
  const [shippingRates, setShippingRates] = useState<ShippingRateOption[]>([]);
  const [paymentMethods, setPaymentMethods] = useState<PaymentMethod[]>([]);
  const [currentStep, setCurrentStep] =
    useState<CheckoutStep>("shipping_address");
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const startCheckout = useCallback(async (cartUuid: string) => {
    setIsLoading(true);
    setError(null);
    try {
      const response = await api.post<{ data: CheckoutSession }>(
        "/checkout/start",
        { cart_uuid: cartUuid },
      );
      setSession(response.data.data);
      setCurrentStep("shipping_address");
    } catch (err: unknown) {
      const message =
        err instanceof Error ? err.message : "Failed to start checkout";
      setError(message);
      throw err;
    } finally {
      setIsLoading(false);
    }
  }, []);

  const setShippingAddress = useCallback(
    async (address: CheckoutAddress) => {
      if (!session) return;
      setIsLoading(true);
      setError(null);
      try {
        const response = await api.put<{ data: CheckoutSession }>(
          "/checkout/shipping-address",
          { session_uuid: session.uuid, ...address },
        );
        setSession(response.data.data);
      } catch (err: unknown) {
        const message =
          err instanceof Error
            ? err.message
            : "Failed to set shipping address";
        setError(message);
        throw err;
      } finally {
        setIsLoading(false);
      }
    },
    [session],
  );

  const setBillingAddress = useCallback(
    async (address: CheckoutAddress) => {
      if (!session) return;
      setIsLoading(true);
      setError(null);
      try {
        const response = await api.put<{ data: CheckoutSession }>(
          "/checkout/billing-address",
          { session_uuid: session.uuid, ...address },
        );
        setSession(response.data.data);
      } catch (err: unknown) {
        const message =
          err instanceof Error
            ? err.message
            : "Failed to set billing address";
        setError(message);
        throw err;
      } finally {
        setIsLoading(false);
      }
    },
    [session],
  );

  const fetchShippingRates = useCallback(
    async (address: CheckoutAddress) => {
      if (!session) return;
      try {
        // Fetch cart to get items for shipping estimation
        const cartResponse = await api.get<{ data: Cart | null }>("/cart");
        const cart = cartResponse.data.data;
        const items =
          cart?.items.map((item) => ({
            product_id: item.product_uuid,
            quantity: item.quantity,
          })) ?? [];

        const response = await api.post<{ data: ShippingRateOption[] }>(
          "/shipping/estimate",
          {
            country_id: address.country_id,
            region_id: address.region_id ?? null,
            items,
            subtotal: cart?.subtotal ?? "0",
            currency_code: "USD",
          },
        );
        setShippingRates(response.data.data);
      } catch {
        setShippingRates([]);
      }
    },
    [session],
  );

  const setShippingMethod = useCallback(
    async (carrierCode: string, methodCode: string) => {
      if (!session) return;
      setIsLoading(true);
      setError(null);
      try {
        const response = await api.put<{ data: CheckoutSession }>(
          "/checkout/shipping-method",
          {
            session_uuid: session.uuid,
            carrier_code: carrierCode,
            method_code: methodCode,
          },
        );
        setSession(response.data.data);
      } catch (err: unknown) {
        const message =
          err instanceof Error
            ? err.message
            : "Failed to set shipping method";
        setError(message);
        throw err;
      } finally {
        setIsLoading(false);
      }
    },
    [session],
  );

  const fetchPaymentMethods = useCallback(async () => {
    try {
      const response = await api.get<{ data: PaymentMethod[] }>(
        "/payment/methods",
      );
      setPaymentMethods(response.data.data);
    } catch {
      setPaymentMethods([]);
    }
  }, []);

  const setPaymentMethod = useCallback(
    async (paymentMethodCode: string) => {
      if (!session) return;
      setIsLoading(true);
      setError(null);
      try {
        const response = await api.put<{ data: CheckoutSession }>(
          "/checkout/payment-method",
          {
            session_uuid: session.uuid,
            payment_method_code: paymentMethodCode,
          },
        );
        setSession(response.data.data);
      } catch (err: unknown) {
        const message =
          err instanceof Error
            ? err.message
            : "Failed to set payment method";
        setError(message);
        throw err;
      } finally {
        setIsLoading(false);
      }
    },
    [session],
  );

  const applyCoupon = useCallback(
    async (couponCode: string) => {
      if (!session) return;
      setIsLoading(true);
      setError(null);
      try {
        const response = await api.post<{ data: CheckoutSession }>(
          "/checkout/coupon",
          { session_uuid: session.uuid, coupon_code: couponCode },
        );
        setSession(response.data.data);
      } catch (err: unknown) {
        const message =
          err instanceof Error ? err.message : "Invalid coupon code";
        setError(message);
        throw err;
      } finally {
        setIsLoading(false);
      }
    },
    [session],
  );

  const removeCoupon = useCallback(async () => {
    if (!session) return;
    setIsLoading(true);
    setError(null);
    try {
      const response = await api.delete<{ data: CheckoutSession }>(
        "/checkout/coupon",
        { data: { session_uuid: session.uuid } },
      );
      setSession(response.data.data);
    } catch (err: unknown) {
      const message =
        err instanceof Error ? err.message : "Failed to remove coupon";
      setError(message);
      throw err;
    } finally {
      setIsLoading(false);
    }
  }, [session]);

  const fetchTotals = useCallback(async () => {
    if (!session) return;
    try {
      const response = await api.get<{ data: CheckoutTotals }>(
        `/checkout/totals?session_uuid=${session.uuid}`,
      );
      setTotals(response.data.data);
    } catch {
      // Totals not available yet
    }
  }, [session]);

  const placeOrder = useCallback(async (): Promise<PlaceOrderResult> => {
    if (!session) throw new Error("No checkout session");
    setIsLoading(true);
    setError(null);
    try {
      const response = await api.post<{ data: PlaceOrderResult }>(
        "/checkout/place-order",
        { session_uuid: session.uuid },
      );
      return response.data.data;
    } catch (err: unknown) {
      const message =
        err instanceof Error ? err.message : "Failed to place order";
      setError(message);
      throw err;
    } finally {
      setIsLoading(false);
    }
  }, [session]);

  return {
    session,
    totals,
    shippingRates,
    paymentMethods,
    currentStep,
    isLoading,
    error,
    startCheckout,
    setShippingAddress,
    setBillingAddress,
    setShippingMethod,
    setPaymentMethod,
    applyCoupon,
    removeCoupon,
    fetchTotals,
    placeOrder,
    setCurrentStep,
    fetchShippingRates,
    fetchPaymentMethods,
  };
}
