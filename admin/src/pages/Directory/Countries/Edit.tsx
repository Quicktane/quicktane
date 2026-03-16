import { useCallback, useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { Country } from "@/types/directory";
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

const countrySchema = z.object({
  is_active: z.boolean(),
  sort_order: z.coerce.number().int().min(0),
});

type CountryFormValues = z.infer<typeof countrySchema>;

export function CountriesEdit() {
  const { iso2 } = useParams<{ iso2: string }>();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [country, setCountry] = useState<Country | null>(null);

  const form = useForm<CountryFormValues>({
    resolver: zodResolver(countrySchema),
    defaultValues: {
      is_active: true,
      sort_order: 0,
    },
  });

  const loadCountry = useCallback(async () => {
    try {
      const response = await api.get<{ data: Country }>(
        `/admin/directory/countries/${iso2}`,
      );
      const countryData = response.data.data;
      setCountry(countryData);

      form.reset({
        is_active: countryData.is_active,
        sort_order: countryData.sort_order,
      });
    } catch {
      toast.error("Failed to load country");
      navigate("/directory/countries");
    } finally {
      setIsLoading(false);
    }
  }, [iso2, form, navigate]);

  useEffect(() => {
    loadCountry();
  }, [loadCountry]);

  const handleSubmit = async (values: CountryFormValues) => {
    setIsSubmitting(true);

    try {
      await api.put(`/admin/directory/countries/${iso2}`, values);
      toast.success("Country updated successfully");
      navigate("/directory/countries");
    } catch {
      toast.error("Failed to update country");
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
        title={`Edit Country: ${country?.name ?? iso2}`}
        description="Update country settings"
      />

      <Form {...form}>
        <form
          onSubmit={form.handleSubmit(handleSubmit)}
          className="space-y-6"
        >
          <Card>
            <CardHeader>
              <CardTitle>Country Information</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <p className="text-sm font-medium leading-none">Name</p>
                  <p className="text-sm text-muted-foreground">
                    {country?.name}
                  </p>
                </div>
                <div className="space-y-2">
                  <p className="text-sm font-medium leading-none">ISO2</p>
                  <p className="text-sm text-muted-foreground">
                    {country?.iso2}
                  </p>
                </div>
                <div className="space-y-2">
                  <p className="text-sm font-medium leading-none">ISO3</p>
                  <p className="text-sm text-muted-foreground">
                    {country?.iso3}
                  </p>
                </div>
                <div className="space-y-2">
                  <p className="text-sm font-medium leading-none">
                    Phone Code
                  </p>
                  <p className="text-sm text-muted-foreground">
                    {country?.phone_code ?? "-"}
                  </p>
                </div>
                <div className="space-y-2">
                  <p className="text-sm font-medium leading-none">
                    Numeric Code
                  </p>
                  <p className="text-sm text-muted-foreground">
                    {country?.numeric_code ?? "-"}
                  </p>
                </div>
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
                        Only active countries are available for selection in
                        addresses
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
                      Lower values appear first in country lists.
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
              onClick={() => navigate("/directory/countries")}
            >
              Cancel
            </Button>
          </div>
        </form>
      </Form>
    </div>
  );
}
