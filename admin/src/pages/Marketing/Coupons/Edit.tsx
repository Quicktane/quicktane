import { useCallback, useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { Coupon, CartPriceRule } from "@/types/promotion";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Switch } from "@/components/ui/switch";
import { Skeleton } from "@/components/ui/skeleton";
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import {
  Form,
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";

const couponSchema = z.object({
  code: z.string().min(1, "Code is required").max(255),
  cart_price_rule_id: z.coerce.number().int().min(1, "Price rule is required"),
  usage_limit: z.coerce.number().int().min(0).nullable(),
  usage_per_customer: z.coerce.number().int().min(0).nullable(),
  is_active: z.boolean(),
  expires_at: z.string().optional().or(z.literal("")),
});

type CouponFormValues = z.infer<typeof couponSchema>;

export function MarketingCouponsEdit() {
  const { uuid } = useParams<{ uuid: string }>();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [priceRules, setPriceRules] = useState<CartPriceRule[]>([]);

  const form = useForm<CouponFormValues>({
    resolver: zodResolver(couponSchema),
    defaultValues: {
      code: "",
      cart_price_rule_id: 0,
      usage_limit: null,
      usage_per_customer: null,
      is_active: true,
      expires_at: "",
    },
  });

  const loadData = useCallback(async () => {
    try {
      const [couponResponse, rulesResponse] = await Promise.all([
        api.get<{ data: Coupon }>(`/admin/promotion/coupons/${uuid}`),
        api.get<{ data: CartPriceRule[] }>("/admin/promotion/rules"),
      ]);

      const coupon = couponResponse.data.data;
      setPriceRules(rulesResponse.data.data);

      form.reset({
        code: coupon.code,
        cart_price_rule_id: coupon.cart_price_rule_id,
        usage_limit: coupon.usage_limit,
        usage_per_customer: coupon.usage_per_customer,
        is_active: coupon.is_active,
        expires_at: coupon.expires_at
          ? coupon.expires_at.substring(0, 10)
          : "",
      });
    } catch {
      toast.error("Failed to load coupon");
      navigate("/marketing/coupons");
    } finally {
      setIsLoading(false);
    }
  }, [uuid, form, navigate]);

  useEffect(() => {
    loadData();
  }, [loadData]);

  const handleSubmit = async (values: CouponFormValues) => {
    setIsSubmitting(true);

    try {
      await api.put(`/admin/promotion/coupons/${uuid}`, {
        ...values,
        expires_at: values.expires_at || null,
      });
      toast.success("Coupon updated successfully");
      navigate("/marketing/coupons");
    } catch {
      toast.error("Failed to update coupon");
    } finally {
      setIsSubmitting(false);
    }
  };

  if (isLoading) {
    return (
      <div className="space-y-4">
        <Skeleton className="h-8 w-48" />
        <Skeleton className="h-64 w-full" />
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <PageHeader
        title="Edit Coupon"
        description="Update coupon code details"
      />

      <Form {...form}>
        <form
          onSubmit={form.handleSubmit(handleSubmit)}
          className="space-y-6"
        >
          <Card>
            <CardHeader>
              <CardTitle>Coupon Details</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <FormField
                control={form.control}
                name="code"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Code</FormLabel>
                    <FormControl>
                      <Input
                        placeholder="SUMMER20"
                        className="uppercase"
                        {...field}
                        onChange={(event) =>
                          field.onChange(event.target.value.toUpperCase())
                        }
                      />
                    </FormControl>
                    <FormDescription>
                      Customers enter this code at checkout.
                    </FormDescription>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <FormField
                control={form.control}
                name="cart_price_rule_id"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Price Rule</FormLabel>
                    <Select
                      value={field.value ? String(field.value) : ""}
                      onValueChange={(value) => field.onChange(Number(value))}
                    >
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue placeholder="Select a price rule" />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        {priceRules.map((rule) => (
                          <SelectItem key={rule.id} value={String(rule.id)}>
                            {rule.name}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Usage Limits</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <FormField
                  control={form.control}
                  name="usage_limit"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Total Usage Limit</FormLabel>
                      <FormControl>
                        <Input
                          type="number"
                          min={0}
                          placeholder="Unlimited"
                          value={field.value ?? ""}
                          onChange={(event) =>
                            field.onChange(
                              event.target.value
                                ? Number(event.target.value)
                                : null,
                            )
                          }
                        />
                      </FormControl>
                      <FormDescription>
                        Leave empty for unlimited uses.
                      </FormDescription>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="usage_per_customer"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Uses Per Customer</FormLabel>
                      <FormControl>
                        <Input
                          type="number"
                          min={0}
                          placeholder="Unlimited"
                          value={field.value ?? ""}
                          onChange={(event) =>
                            field.onChange(
                              event.target.value
                                ? Number(event.target.value)
                                : null,
                            )
                          }
                        />
                      </FormControl>
                      <FormDescription>
                        Leave empty for unlimited uses per customer.
                      </FormDescription>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>
              <FormField
                control={form.control}
                name="expires_at"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Expiration Date</FormLabel>
                    <FormControl>
                      <Input type="date" {...field} />
                    </FormControl>
                    <FormDescription>
                      Leave empty for no expiration.
                    </FormDescription>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Settings</CardTitle>
            </CardHeader>
            <CardContent>
              <FormField
                control={form.control}
                name="is_active"
                render={({ field }) => (
                  <FormItem className="flex items-center justify-between rounded-lg border p-4">
                    <div className="space-y-0.5">
                      <FormLabel className="text-base">Active</FormLabel>
                      <FormDescription>
                        Allow customers to use this coupon
                      </FormDescription>
                    </div>
                    <FormControl>
                      <Switch
                        checked={field.value}
                        onCheckedChange={field.onChange}
                      />
                    </FormControl>
                  </FormItem>
                )}
              />
            </CardContent>
          </Card>

          <div className="flex gap-2">
            <Button type="submit" disabled={isSubmitting}>
              {isSubmitting ? (
                <>
                  <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                  Saving...
                </>
              ) : (
                "Save Changes"
              )}
            </Button>
            <Button
              type="button"
              variant="outline"
              onClick={() => navigate("/marketing/coupons")}
            >
              Cancel
            </Button>
          </div>
        </form>
      </Form>
    </div>
  );
}
