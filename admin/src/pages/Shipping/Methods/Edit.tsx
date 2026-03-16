import { useCallback, useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { ShippingMethod } from "@/types/shipping";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
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

const shippingMethodSchema = z.object({
  code: z
    .string()
    .min(1, "Code is required")
    .max(100)
    .regex(
      /^[a-z0-9_-]+$/,
      "Code must contain only lowercase letters, numbers, hyphens, and underscores",
    ),
  name: z.string().min(1, "Name is required").max(255),
  carrier_code: z
    .string()
    .min(1, "Carrier code is required")
    .max(100)
    .regex(
      /^[a-z0-9_-]+$/,
      "Carrier code must contain only lowercase letters, numbers, hyphens, and underscores",
    ),
  description: z.string().optional().or(z.literal("")),
  is_active: z.boolean(),
  sort_order: z.coerce.number().int().min(0),
});

type ShippingMethodFormValues = z.infer<typeof shippingMethodSchema>;

export function ShippingMethodsEdit() {
  const { uuid } = useParams<{ uuid: string }>();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const form = useForm<ShippingMethodFormValues>({
    resolver: zodResolver(shippingMethodSchema),
    defaultValues: {
      code: "",
      name: "",
      carrier_code: "",
      description: "",
      is_active: true,
      sort_order: 0,
    },
  });

  const loadShippingMethod = useCallback(async () => {
    try {
      const response = await api.get<{ data: ShippingMethod }>(
        `/admin/shipping/methods/${uuid}`,
      );
      const method = response.data.data;

      form.reset({
        code: method.code,
        name: method.name,
        carrier_code: method.carrier_code,
        description: method.description ?? "",
        is_active: method.is_active,
        sort_order: method.sort_order,
      });
    } catch {
      toast.error("Failed to load shipping method");
      navigate("/shipping/methods");
    } finally {
      setIsLoading(false);
    }
  }, [uuid, form, navigate]);

  useEffect(() => {
    loadShippingMethod();
  }, [loadShippingMethod]);

  const handleSubmit = async (values: ShippingMethodFormValues) => {
    setIsSubmitting(true);

    try {
      await api.put(`/admin/shipping/methods/${uuid}`, {
        ...values,
        description: values.description || null,
      });
      toast.success("Shipping method updated successfully");
      navigate("/shipping/methods");
    } catch {
      toast.error("Failed to update shipping method");
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
        title="Edit Shipping Method"
        description="Update shipping method details"
      />

      <Form {...form}>
        <form
          onSubmit={form.handleSubmit(handleSubmit)}
          className="space-y-6"
        >
          <Card>
            <CardHeader>
              <CardTitle>Method Details</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <FormField
                  control={form.control}
                  name="name"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Name</FormLabel>
                      <FormControl>
                        <Input placeholder="Standard Shipping" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="code"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Code</FormLabel>
                      <FormControl>
                        <Input placeholder="standard_shipping" {...field} />
                      </FormControl>
                      <FormDescription>
                        Unique identifier. Lowercase, numbers, hyphens, underscores.
                      </FormDescription>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>
              <FormField
                control={form.control}
                name="carrier_code"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Carrier Code</FormLabel>
                    <FormControl>
                      <Input placeholder="flatrate" {...field} />
                    </FormControl>
                    <FormDescription>
                      The carrier identifier (e.g., flatrate, ups, fedex).
                    </FormDescription>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <FormField
                control={form.control}
                name="description"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Description</FormLabel>
                    <FormControl>
                      <Textarea
                        placeholder="Optional description..."
                        {...field}
                      />
                    </FormControl>
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
            <CardContent className="space-y-4">
              <FormField
                control={form.control}
                name="is_active"
                render={({ field }) => (
                  <FormItem className="flex items-center justify-between rounded-lg border p-4">
                    <div className="space-y-0.5">
                      <FormLabel className="text-base">Active</FormLabel>
                      <FormDescription>
                        Make this method available at checkout
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
                      Lower values appear first in checkout.
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
              onClick={() => navigate("/shipping/methods")}
            >
              Cancel
            </Button>
          </div>
        </form>
      </Form>
    </div>
  );
}
