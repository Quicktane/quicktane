import { useCallback, useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Loader2, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { ShippingZone, ShippingZoneCountry } from "@/types/shipping";
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

const shippingZoneSchema = z.object({
  name: z.string().min(1, "Name is required").max(255),
  is_active: z.boolean(),
});

type ShippingZoneFormValues = z.infer<typeof shippingZoneSchema>;

type CountryDraft = Omit<ShippingZoneCountry, "id">;

export function ShippingZonesEdit() {
  const { uuid } = useParams<{ uuid: string }>();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [countries, setCountries] = useState<CountryDraft[]>([]);

  const form = useForm<ShippingZoneFormValues>({
    resolver: zodResolver(shippingZoneSchema),
    defaultValues: {
      name: "",
      is_active: true,
    },
  });

  const loadShippingZone = useCallback(async () => {
    try {
      const response = await api.get<{ data: ShippingZone }>(
        `/admin/shipping/zones/${uuid}`,
      );
      const zone = response.data.data;

      form.reset({
        name: zone.name,
        is_active: zone.is_active,
      });

      setCountries(
        (zone.countries ?? []).map(({ country_id, region_id }) => ({
          country_id,
          region_id,
        })),
      );
    } catch {
      toast.error("Failed to load shipping zone");
      navigate("/shipping/zones");
    } finally {
      setIsLoading(false);
    }
  }, [uuid, form, navigate]);

  useEffect(() => {
    loadShippingZone();
  }, [loadShippingZone]);

  const addCountry = () => {
    setCountries((previous) => [
      ...previous,
      { country_id: 0, region_id: null },
    ]);
  };

  const removeCountry = (index: number) => {
    setCountries((previous) =>
      previous.filter((_, countryIndex) => countryIndex !== index),
    );
  };

  const updateCountry = (
    index: number,
    field: keyof CountryDraft,
    value: string,
  ) => {
    setCountries((previous) =>
      previous.map((country, countryIndex) => {
        if (countryIndex !== index) return country;

        if (field === "country_id") {
          return { ...country, country_id: Number(value) };
        }

        return { ...country, region_id: value ? Number(value) : null };
      }),
    );
  };

  const handleSubmit = async (values: ShippingZoneFormValues) => {
    setIsSubmitting(true);

    try {
      await api.put(`/admin/shipping/zones/${uuid}`, {
        ...values,
        countries,
      });
      toast.success("Shipping zone updated successfully");
      navigate("/shipping/zones");
    } catch {
      toast.error("Failed to update shipping zone");
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
        title="Edit Shipping Zone"
        description="Update shipping zone details"
      />

      <Form {...form}>
        <form
          onSubmit={form.handleSubmit(handleSubmit)}
          className="space-y-6"
        >
          <Card>
            <CardHeader>
              <CardTitle>Zone Details</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <FormField
                control={form.control}
                name="name"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Name</FormLabel>
                    <FormControl>
                      <Input placeholder="North America" {...field} />
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
                        Enable this shipping zone
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

          <Card>
            <CardHeader className="flex flex-row items-center justify-between">
              <CardTitle>Countries</CardTitle>
              <Button
                type="button"
                variant="outline"
                size="sm"
                onClick={addCountry}
              >
                <Plus className="mr-2 h-4 w-4" />
                Add Country
              </Button>
            </CardHeader>
            <CardContent className="space-y-4">
              {countries.length === 0 && (
                <p className="text-sm text-muted-foreground">
                  No countries added. Add countries to define this zone's coverage.
                </p>
              )}
              {countries.map((country, index) => (
                <div
                  key={index}
                  className="grid gap-3 rounded-lg border p-4 sm:grid-cols-2"
                >
                  <div className="space-y-1">
                    <label className="text-sm font-medium">Country ID</label>
                    <Input
                      type="number"
                      min={0}
                      placeholder="840"
                      value={country.country_id || ""}
                      onChange={(event) =>
                        updateCountry(index, "country_id", event.target.value)
                      }
                    />
                  </div>
                  <div className="space-y-1">
                    <label className="text-sm font-medium">Region ID</label>
                    <div className="flex gap-2">
                      <Input
                        type="number"
                        min={0}
                        placeholder="Optional"
                        value={country.region_id ?? ""}
                        onChange={(event) =>
                          updateCountry(index, "region_id", event.target.value)
                        }
                      />
                      <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        onClick={() => removeCountry(index)}
                      >
                        <Trash2 className="h-4 w-4 text-destructive" />
                      </Button>
                    </div>
                  </div>
                </div>
              ))}
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
              onClick={() => navigate("/shipping/zones")}
            >
              Cancel
            </Button>
          </div>
        </form>
      </Form>
    </div>
  );
}
