import { useCallback, useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { Currency } from "@/types/directory";
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

const currencySchema = z.object({
  symbol: z.string().min(1, "Symbol is required").max(10),
  decimal_places: z.coerce.number().int().min(0).max(6),
  is_active: z.boolean(),
  sort_order: z.coerce.number().int().min(0),
});

type CurrencyFormValues = z.infer<typeof currencySchema>;

export function CurrenciesEdit() {
  const { code } = useParams<{ code: string }>();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [currency, setCurrency] = useState<Currency | null>(null);

  const form = useForm<CurrencyFormValues>({
    resolver: zodResolver(currencySchema),
    defaultValues: {
      symbol: "",
      decimal_places: 2,
      is_active: true,
      sort_order: 0,
    },
  });

  const loadCurrency = useCallback(async () => {
    try {
      const response = await api.get<{ data: Currency }>(
        `/admin/directory/currencies/${code}`,
      );
      const currencyData = response.data.data;
      setCurrency(currencyData);

      form.reset({
        symbol: currencyData.symbol,
        decimal_places: currencyData.decimal_places,
        is_active: currencyData.is_active,
        sort_order: currencyData.sort_order,
      });
    } catch {
      toast.error("Failed to load currency");
      navigate("/directory/currencies");
    } finally {
      setIsLoading(false);
    }
  }, [code, form, navigate]);

  useEffect(() => {
    loadCurrency();
  }, [loadCurrency]);

  const handleSubmit = async (values: CurrencyFormValues) => {
    setIsSubmitting(true);

    try {
      await api.put(`/admin/directory/currencies/${code}`, values);
      toast.success("Currency updated successfully");
      navigate("/directory/currencies");
    } catch {
      toast.error("Failed to update currency");
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
        title={`Edit Currency: ${currency?.name ?? code}`}
        description="Update currency settings"
      />

      <Form {...form}>
        <form
          onSubmit={form.handleSubmit(handleSubmit)}
          className="space-y-6"
        >
          <Card>
            <CardHeader>
              <CardTitle>Currency Information</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <p className="text-sm font-medium leading-none">Name</p>
                  <p className="text-sm text-muted-foreground">
                    {currency?.name}
                  </p>
                </div>
                <div className="space-y-2">
                  <p className="text-sm font-medium leading-none">Code</p>
                  <p className="text-sm text-muted-foreground">
                    {currency?.code}
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Display</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <FormField
                  control={form.control}
                  name="symbol"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Symbol</FormLabel>
                      <FormControl>
                        <Input placeholder="$" maxLength={10} {...field} />
                      </FormControl>
                      <FormDescription>
                        Currency symbol shown to customers (e.g. $, €, £).
                      </FormDescription>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="decimal_places"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Decimal Places</FormLabel>
                      <FormControl>
                        <Input type="number" min={0} max={6} {...field} />
                      </FormControl>
                      <FormDescription>
                        Number of decimal places for price display (0–6).
                      </FormDescription>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Settings</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <FormField
                control={form.control}
                name="is_active"
                render={({ field }) => (
                  <FormItem className="flex items-center justify-between rounded-lg border p-4">
                    <div className="space-y-0.5">
                      <FormLabel className="text-base">Active</FormLabel>
                      <FormDescription>
                        Only active currencies can be assigned to store views
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
              <FormField
                control={form.control}
                name="sort_order"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Sort Order</FormLabel>
                    <FormControl>
                      <Input type="number" min={0} {...field} />
                    </FormControl>
                    <FormDescription>
                      Lower values appear first in currency lists.
                    </FormDescription>
                    <FormMessage />
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
              onClick={() => navigate("/directory/currencies")}
            >
              Cancel
            </Button>
          </div>
        </form>
      </Form>
    </div>
  );
}
