import { useCallback, useEffect, useState } from "react";
import { useNavigate } from "react-router";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { ShippingMethod, ShippingZone } from "@/types/shipping";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Switch } from "@/components/ui/switch";
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

const shippingRateSchema = z.object({
  shipping_method_id: z.coerce
    .number()
    .int()
    .min(1, "Shipping method is required"),
  shipping_zone_id: z.coerce
    .number()
    .int()
    .min(1, "Shipping zone is required"),
  price: z.string().min(1, "Price is required"),
  is_active: z.boolean(),
});

type ShippingRateFormValues = z.infer<typeof shippingRateSchema>;

export function ShippingRatesCreate() {
  const navigate = useNavigate();
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [shippingMethods, setShippingMethods] = useState<ShippingMethod[]>([]);
  const [shippingZones, setShippingZones] = useState<ShippingZone[]>([]);

  const form = useForm<ShippingRateFormValues>({
    resolver: zodResolver(shippingRateSchema),
    defaultValues: {
      shipping_method_id: 0,
      shipping_zone_id: 0,
      price: "",
      is_active: true,
    },
  });

  const loadOptions = useCallback(async () => {
    try {
      const [methodsResponse, zonesResponse] = await Promise.all([
        api.get<{ data: ShippingMethod[] }>("/admin/shipping/methods"),
        api.get<{ data: ShippingZone[] }>("/admin/shipping/zones"),
      ]);

      setShippingMethods(methodsResponse.data.data);
      setShippingZones(zonesResponse.data.data);
    } catch {
      toast.error("Failed to load options");
    }
  }, []);

  useEffect(() => {
    loadOptions();
  }, [loadOptions]);

  const handleSubmit = async (values: ShippingRateFormValues) => {
    setIsSubmitting(true);

    try {
      await api.post("/admin/shipping/rates", values);
      toast.success("Shipping rate created successfully");
      navigate("/shipping/rates");
    } catch {
      toast.error("Failed to create shipping rate");
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="space-y-4">
      <PageHeader
        title="Create Shipping Rate"
        description="Add a price for a shipping method and zone combination"
      />

      <Form {...form}>
        <form
          onSubmit={form.handleSubmit(handleSubmit)}
          className="space-y-6"
        >
          <Card>
            <CardHeader>
              <CardTitle>Rate Details</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <FormField
                  control={form.control}
                  name="shipping_method_id"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Shipping Method</FormLabel>
                      <Select
                        value={field.value ? String(field.value) : ""}
                        onValueChange={(value) =>
                          field.onChange(Number(value))
                        }
                      >
                        <FormControl>
                          <SelectTrigger>
                            <SelectValue placeholder="Select a method" />
                          </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                          {shippingMethods.map((method) => (
                            <SelectItem
                              key={method.id}
                              value={String(method.id)}
                            >
                              {method.name}
                            </SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="shipping_zone_id"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Shipping Zone</FormLabel>
                      <Select
                        value={field.value ? String(field.value) : ""}
                        onValueChange={(value) =>
                          field.onChange(Number(value))
                        }
                      >
                        <FormControl>
                          <SelectTrigger>
                            <SelectValue placeholder="Select a zone" />
                          </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                          {shippingZones.map((zone) => (
                            <SelectItem key={zone.id} value={String(zone.id)}>
                              {zone.name}
                            </SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>
              <FormField
                control={form.control}
                name="price"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Price</FormLabel>
                    <FormControl>
                      <Input placeholder="9.99" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <FormField
                control={form.control}
                name="is_active"
                render={({ field }) => (
                  <FormItem className="flex items-center justify-between rounded-lg border p-4">
                    <div className="space-y-0.5">
                      <FormLabel className="text-base">Active</FormLabel>
                      <FormDescription>
                        Enable this shipping rate
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
                  Creating...
                </>
              ) : (
                "Create Rate"
              )}
            </Button>
            <Button
              type="button"
              variant="outline"
              onClick={() => navigate("/shipping/rates")}
            >
              Cancel
            </Button>
          </div>
        </form>
      </Form>
    </div>
  );
}
