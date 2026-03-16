import { useCallback, useEffect, useState } from "react";
import { useNavigate, Link } from "react-router";
import {
  Check,
  ChevronRight,
  Loader2,
  MapPin,
  Truck,
  CreditCard,
  ClipboardList,
  Tag,
  X,
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Separator } from "@/components/ui/separator";
import { Skeleton } from "@/components/ui/skeleton";
import { useCart } from "@/contexts/CartContext";
import { useAuth } from "@/contexts/AuthContext";
import { useCheckout, type CheckoutStep } from "@/hooks/useCheckout";
import { CheckoutAddressForm } from "@/components/checkout/CheckoutAddressForm";
import type { CheckoutAddress } from "@/types/checkout";
import type { CustomerAddress } from "@/types/customer";
import { api } from "@/lib/api";
import { toast } from "sonner";
import { cn } from "@/lib/utils";

const STEPS: { key: CheckoutStep; label: string; icon: typeof MapPin }[] = [
  { key: "shipping_address", label: "Shipping", icon: MapPin },
  { key: "shipping_method", label: "Delivery", icon: Truck },
  { key: "payment", label: "Payment", icon: CreditCard },
  { key: "review", label: "Review", icon: ClipboardList },
];

export function Checkout() {
  const navigate = useNavigate();
  const { cart, refreshCart } = useCart();
  const { isAuthenticated, customer } = useAuth();
  const checkout = useCheckout();
  const [isStarting, setIsStarting] = useState(true);
  const [savedAddresses, setSavedAddresses] = useState<CustomerAddress[]>([]);
  const [useSameAddress, setUseSameAddress] = useState(true);
  const [couponInput, setCouponInput] = useState("");
  const [isApplyingCoupon, setIsApplyingCoupon] = useState(false);
  const [isPlacingOrder, setIsPlacingOrder] = useState(false);

  // Start checkout session
  useEffect(() => {
    if (!cart || cart.items.length === 0) {
      navigate("/cart");
      return;
    }

    checkout
      .startCheckout(cart.uuid)
      .then(() => setIsStarting(false))
      .catch(() => {
        toast.error("Failed to start checkout");
        navigate("/cart");
      });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  // Load saved addresses for authenticated customers
  useEffect(() => {
    if (isAuthenticated) {
      api
        .get<{ data: CustomerAddress[] }>("/customer/me/addresses")
        .then((response) => setSavedAddresses(response.data.data))
        .catch(() => {});
    }
  }, [isAuthenticated]);

  // Fetch payment methods when reaching payment step
  useEffect(() => {
    if (checkout.currentStep === "payment") {
      checkout.fetchPaymentMethods();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [checkout.currentStep]);

  // Fetch totals when reaching review step
  useEffect(() => {
    if (checkout.currentStep === "review") {
      checkout.fetchTotals();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [checkout.currentStep]);

  const currentStepIndex = STEPS.findIndex(
    (step) => step.key === checkout.currentStep,
  );

  const handleShippingAddressSubmit = useCallback(
    async (address: CheckoutAddress) => {
      await checkout.setShippingAddress(address);
      if (useSameAddress) {
        await checkout.setBillingAddress(address);
      }
      await checkout.fetchShippingRates(address);
      checkout.setCurrentStep("shipping_method");
    },
    [checkout, useSameAddress],
  );

  const handleSelectSavedAddress = useCallback(
    async (savedAddress: CustomerAddress) => {
      const address: CheckoutAddress = {
        first_name: savedAddress.first_name,
        last_name: savedAddress.last_name,
        company: savedAddress.company ?? undefined,
        street_line_1: savedAddress.street_line_1,
        street_line_2: savedAddress.street_line_2 ?? undefined,
        city: savedAddress.city,
        region_id: savedAddress.region_id,
        postcode: savedAddress.postcode,
        country_id: savedAddress.country_id,
        phone: savedAddress.phone ?? undefined,
      };
      await handleShippingAddressSubmit(address);
    },
    [handleShippingAddressSubmit],
  );

  const handleShippingMethodSelect = useCallback(
    async (carrierCode: string, methodCode: string) => {
      await checkout.setShippingMethod(carrierCode, methodCode);
      checkout.setCurrentStep("payment");
    },
    [checkout],
  );

  const handlePaymentMethodSelect = useCallback(
    async (paymentMethodCode: string) => {
      await checkout.setPaymentMethod(paymentMethodCode);
      checkout.setCurrentStep("review");
    },
    [checkout],
  );

  const handleApplyCoupon = useCallback(async () => {
    if (!couponInput.trim()) return;
    setIsApplyingCoupon(true);
    try {
      await checkout.applyCoupon(couponInput.trim());
      await checkout.fetchTotals();
      setCouponInput("");
      toast.success("Coupon applied");
    } catch {
      toast.error("Invalid coupon code");
    } finally {
      setIsApplyingCoupon(false);
    }
  }, [checkout, couponInput]);

  const handleRemoveCoupon = useCallback(async () => {
    try {
      await checkout.removeCoupon();
      await checkout.fetchTotals();
      toast.success("Coupon removed");
    } catch {
      toast.error("Failed to remove coupon");
    }
  }, [checkout]);

  const handlePlaceOrder = useCallback(async () => {
    setIsPlacingOrder(true);
    try {
      const result = await checkout.placeOrder();
      if (result.success && result.order) {
        await refreshCart();
        navigate(`/order-confirmation/${result.order.uuid}`);
      } else if (result.suspended && result.redirect_url) {
        window.location.href = result.redirect_url;
      } else if (result.errors.length > 0) {
        toast.error(result.errors[0]);
      }
    } catch {
      toast.error("Failed to place order. Please try again.");
    } finally {
      setIsPlacingOrder(false);
    }
  }, [checkout, navigate, refreshCart]);

  if (isStarting) {
    return (
      <div className="container mx-auto px-4 py-8 max-w-4xl">
        <Skeleton className="h-8 w-48 mb-8" />
        <div className="space-y-4">
          <Skeleton className="h-64 w-full" />
          <Skeleton className="h-48 w-full" />
        </div>
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-8 max-w-5xl">
      <h1 className="text-3xl font-bold mb-8">Checkout</h1>

      {/* Step indicator */}
      <div className="flex items-center mb-8">
        {STEPS.map((step, index) => {
          const isCompleted = index < currentStepIndex;
          const isCurrent = index === currentStepIndex;
          const Icon = step.icon;

          return (
            <div key={step.key} className="flex items-center flex-1 last:flex-none">
              <button
                onClick={() => isCompleted && checkout.setCurrentStep(step.key)}
                disabled={!isCompleted}
                className={cn(
                  "flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium transition-colors",
                  isCompleted && "bg-primary text-primary-foreground cursor-pointer hover:bg-primary/90",
                  isCurrent && "bg-primary text-primary-foreground",
                  !isCompleted && !isCurrent && "bg-muted text-muted-foreground",
                )}
              >
                {isCompleted ? (
                  <Check className="h-4 w-4" />
                ) : (
                  <Icon className="h-4 w-4" />
                )}
                <span className="hidden sm:inline">{step.label}</span>
              </button>
              {index < STEPS.length - 1 && (
                <ChevronRight className="h-4 w-4 mx-2 text-muted-foreground flex-shrink-0" />
              )}
            </div>
          );
        })}
      </div>

      <div className="grid lg:grid-cols-3 gap-8">
        {/* Main content */}
        <div className="lg:col-span-2">
          {checkout.error && (
            <div className="bg-destructive/10 text-destructive rounded-lg p-4 mb-4">
              {checkout.error}
            </div>
          )}

          {/* Step: Shipping Address */}
          {checkout.currentStep === "shipping_address" && (
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <MapPin className="h-5 w-5" />
                  Shipping Address
                </CardTitle>
              </CardHeader>
              <CardContent>
                {savedAddresses.length > 0 && (
                  <div className="mb-6">
                    <h3 className="text-sm font-medium mb-3">
                      Saved Addresses
                    </h3>
                    <div className="grid gap-3">
                      {savedAddresses.map((address) => (
                        <button
                          key={address.uuid}
                          onClick={() => handleSelectSavedAddress(address)}
                          disabled={checkout.isLoading}
                          className="w-full text-left p-3 border rounded-lg hover:border-primary hover:bg-accent transition-colors"
                        >
                          <p className="font-medium">
                            {address.first_name} {address.last_name}
                          </p>
                          <p className="text-sm text-muted-foreground">
                            {address.street_line_1}, {address.city},{" "}
                            {address.postcode}
                          </p>
                        </button>
                      ))}
                    </div>
                    <Separator className="my-4" />
                    <h3 className="text-sm font-medium mb-3">
                      Or enter a new address
                    </h3>
                  </div>
                )}

                <div className="mb-4">
                  <label className="flex items-center gap-2 text-sm">
                    <input
                      type="checkbox"
                      checked={useSameAddress}
                      onChange={(e) => setUseSameAddress(e.target.checked)}
                      className="rounded"
                    />
                    Billing address same as shipping
                  </label>
                </div>

                <CheckoutAddressForm
                  onSubmit={handleShippingAddressSubmit}
                  isLoading={checkout.isLoading}
                  customer={customer}
                  initialAddress={checkout.session?.shipping_address}
                />
              </CardContent>
            </Card>
          )}

          {/* Step: Shipping Method */}
          {checkout.currentStep === "shipping_method" && (
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Truck className="h-5 w-5" />
                  Shipping Method
                </CardTitle>
              </CardHeader>
              <CardContent>
                {checkout.shippingRates.length === 0 ? (
                  <div className="text-center py-8 text-muted-foreground">
                    <Truck className="h-10 w-10 mx-auto mb-3 opacity-50" />
                    <p>No shipping methods available for this address.</p>
                    <Button
                      variant="link"
                      onClick={() =>
                        checkout.setCurrentStep("shipping_address")
                      }
                    >
                      Change address
                    </Button>
                  </div>
                ) : (
                  <div className="space-y-3">
                    {checkout.shippingRates.map((rate) => (
                      <button
                        key={`${rate.carrier_code}-${rate.method_code}`}
                        onClick={() =>
                          handleShippingMethodSelect(
                            rate.carrier_code,
                            rate.method_code,
                          )
                        }
                        disabled={checkout.isLoading}
                        className={cn(
                          "w-full flex items-center justify-between p-4 border rounded-lg transition-colors",
                          "hover:border-primary hover:bg-accent",
                          checkout.session?.shipping_method_code ===
                            rate.method_code &&
                            "border-primary bg-accent",
                        )}
                      >
                        <div className="text-left">
                          <p className="font-medium">{rate.label}</p>
                          {rate.estimated_days && (
                            <p className="text-sm text-muted-foreground">
                              Estimated {rate.estimated_days} business days
                            </p>
                          )}
                        </div>
                        <span className="font-semibold">
                          {parseFloat(rate.price) === 0
                            ? "Free"
                            : `$${rate.price}`}
                        </span>
                      </button>
                    ))}
                  </div>
                )}
              </CardContent>
            </Card>
          )}

          {/* Step: Payment Method */}
          {checkout.currentStep === "payment" && (
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <CreditCard className="h-5 w-5" />
                  Payment Method
                </CardTitle>
              </CardHeader>
              <CardContent>
                {checkout.paymentMethods.length === 0 ? (
                  <div className="text-center py-8 text-muted-foreground">
                    <Loader2 className="h-8 w-8 mx-auto animate-spin mb-3" />
                    <p>Loading payment methods...</p>
                  </div>
                ) : (
                  <div className="space-y-3">
                    {checkout.paymentMethods.map((method) => (
                      <button
                        key={method.code}
                        onClick={() => handlePaymentMethodSelect(method.code)}
                        disabled={checkout.isLoading}
                        className={cn(
                          "w-full flex items-start p-4 border rounded-lg transition-colors",
                          "hover:border-primary hover:bg-accent",
                          checkout.session?.payment_method_code ===
                            method.code && "border-primary bg-accent",
                        )}
                      >
                        <div className="text-left">
                          <p className="font-medium">{method.name}</p>
                          {method.description && (
                            <p className="text-sm text-muted-foreground mt-1">
                              {method.description}
                            </p>
                          )}
                        </div>
                      </button>
                    ))}
                  </div>
                )}
              </CardContent>
            </Card>
          )}

          {/* Step: Review */}
          {checkout.currentStep === "review" && (
            <div className="space-y-4">
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <ClipboardList className="h-5 w-5" />
                    Order Review
                  </CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  {/* Shipping address summary */}
                  {checkout.session?.shipping_address && (
                    <div>
                      <div className="flex items-center justify-between">
                        <h3 className="text-sm font-medium text-muted-foreground">
                          Shipping Address
                        </h3>
                        <Button
                          variant="link"
                          size="sm"
                          className="h-auto p-0"
                          onClick={() =>
                            checkout.setCurrentStep("shipping_address")
                          }
                        >
                          Edit
                        </Button>
                      </div>
                      <p className="text-sm mt-1">
                        {checkout.session.shipping_address.first_name}{" "}
                        {checkout.session.shipping_address.last_name}
                        <br />
                        {checkout.session.shipping_address.street_line_1},{" "}
                        {checkout.session.shipping_address.city},{" "}
                        {checkout.session.shipping_address.postcode}
                      </p>
                    </div>
                  )}

                  <Separator />

                  {/* Shipping method summary */}
                  <div>
                    <div className="flex items-center justify-between">
                      <h3 className="text-sm font-medium text-muted-foreground">
                        Shipping Method
                      </h3>
                      <Button
                        variant="link"
                        size="sm"
                        className="h-auto p-0"
                        onClick={() =>
                          checkout.setCurrentStep("shipping_method")
                        }
                      >
                        Edit
                      </Button>
                    </div>
                    <p className="text-sm mt-1">
                      {checkout.session?.shipping_method_label ?? "Not selected"}
                      {checkout.session?.shipping_amount &&
                        ` — $${checkout.session.shipping_amount}`}
                    </p>
                  </div>

                  <Separator />

                  {/* Payment method summary */}
                  <div>
                    <div className="flex items-center justify-between">
                      <h3 className="text-sm font-medium text-muted-foreground">
                        Payment Method
                      </h3>
                      <Button
                        variant="link"
                        size="sm"
                        className="h-auto p-0"
                        onClick={() => checkout.setCurrentStep("payment")}
                      >
                        Edit
                      </Button>
                    </div>
                    <p className="text-sm mt-1">
                      {checkout.paymentMethods.find(
                        (m) =>
                          m.code === checkout.session?.payment_method_code,
                      )?.name ?? "Not selected"}
                    </p>
                  </div>

                  <Separator />

                  {/* Cart items */}
                  <div>
                    <h3 className="text-sm font-medium text-muted-foreground mb-2">
                      Items ({cart?.items_count ?? 0})
                    </h3>
                    <div className="space-y-2">
                      {cart?.items.map((item) => (
                        <div
                          key={item.uuid}
                          className="flex justify-between text-sm"
                        >
                          <span>
                            {item.name}{" "}
                            <span className="text-muted-foreground">
                              x{item.quantity}
                            </span>
                          </span>
                          <span>${item.row_total}</span>
                        </div>
                      ))}
                    </div>
                  </div>
                </CardContent>
              </Card>

              {/* Coupon */}
              <Card>
                <CardContent className="pt-6">
                  {checkout.session?.coupon_code ? (
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-2">
                        <Tag className="h-4 w-4 text-green-600" />
                        <span className="text-sm font-medium">
                          Coupon: {checkout.session.coupon_code}
                        </span>
                      </div>
                      <Button
                        variant="ghost"
                        size="sm"
                        onClick={handleRemoveCoupon}
                      >
                        <X className="h-4 w-4" />
                      </Button>
                    </div>
                  ) : (
                    <div className="flex gap-2">
                      <Input
                        placeholder="Coupon code"
                        value={couponInput}
                        onChange={(e) =>
                          setCouponInput(e.target.value.toUpperCase())
                        }
                        onKeyDown={(e) => e.key === "Enter" && handleApplyCoupon()}
                      />
                      <Button
                        variant="outline"
                        onClick={handleApplyCoupon}
                        disabled={isApplyingCoupon || !couponInput.trim()}
                      >
                        {isApplyingCoupon ? (
                          <Loader2 className="h-4 w-4 animate-spin" />
                        ) : (
                          "Apply"
                        )}
                      </Button>
                    </div>
                  )}
                </CardContent>
              </Card>

              {/* Place order */}
              <Button
                size="lg"
                className="w-full"
                onClick={handlePlaceOrder}
                disabled={isPlacingOrder || checkout.isLoading}
              >
                {isPlacingOrder ? (
                  <>
                    <Loader2 className="h-4 w-4 animate-spin mr-2" />
                    Placing Order...
                  </>
                ) : (
                  `Place Order — $${checkout.totals?.grand_total ?? checkout.session?.totals?.grand_total ?? "0.00"}`
                )}
              </Button>
            </div>
          )}
        </div>

        {/* Order summary sidebar */}
        <div>
          <Card className="sticky top-4">
            <CardHeader>
              <CardTitle>Order Summary</CardTitle>
            </CardHeader>
            <CardContent className="space-y-3">
              <div className="flex justify-between text-sm">
                <span className="text-muted-foreground">
                  Subtotal ({cart?.items_count ?? 0} items)
                </span>
                <span>${cart?.subtotal ?? "0.00"}</span>
              </div>

              {(checkout.totals || checkout.session?.totals) && (
                <>
                  {parseFloat(
                    checkout.totals?.shipping_amount ??
                      checkout.session?.totals?.shipping_amount ??
                      "0",
                  ) > 0 && (
                    <div className="flex justify-between text-sm">
                      <span className="text-muted-foreground">Shipping</span>
                      <span>
                        $
                        {checkout.totals?.shipping_amount ??
                          checkout.session?.totals?.shipping_amount}
                      </span>
                    </div>
                  )}

                  {parseFloat(
                    checkout.totals?.discount_amount ??
                      checkout.session?.totals?.discount_amount ??
                      "0",
                  ) > 0 && (
                    <div className="flex justify-between text-sm text-green-600">
                      <span>Discount</span>
                      <span>
                        -$
                        {checkout.totals?.discount_amount ??
                          checkout.session?.totals?.discount_amount}
                      </span>
                    </div>
                  )}

                  {parseFloat(
                    checkout.totals?.tax_amount ??
                      checkout.session?.totals?.tax_amount ??
                      "0",
                  ) > 0 && (
                    <div className="flex justify-between text-sm">
                      <span className="text-muted-foreground">Tax</span>
                      <span>
                        $
                        {checkout.totals?.tax_amount ??
                          checkout.session?.totals?.tax_amount}
                      </span>
                    </div>
                  )}

                  <Separator />

                  <div className="flex justify-between font-semibold text-lg">
                    <span>Total</span>
                    <span>
                      $
                      {checkout.totals?.grand_total ??
                        checkout.session?.totals?.grand_total ??
                        cart?.subtotal ??
                        "0.00"}
                    </span>
                  </div>
                </>
              )}

              {!checkout.totals && !checkout.session?.totals && (
                <>
                  <Separator />
                  <div className="flex justify-between font-semibold text-lg">
                    <span>Subtotal</span>
                    <span>${cart?.subtotal ?? "0.00"}</span>
                  </div>
                </>
              )}

              <div className="pt-2">
                <Link
                  to="/cart"
                  className="text-sm text-muted-foreground hover:underline"
                >
                  Return to cart
                </Link>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
}
