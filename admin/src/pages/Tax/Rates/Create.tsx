import { useCallback, useEffect, useState } from "react";
import { useNavigate } from "react-router";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { TaxZone } from "@/types/tax";
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

const taxRateSchema = z.object({
  name: z.string().min(1, "Name is required").max(255),
  tax_zone_id: z.coerce.number().int().min(1, "Zone is required"),
  rate: z.string().min(1, "Rate is required"),
  priority: z.coerce.number().int().min(0),
  is_compound: z.boolean(),
  is_active: z.boolean(),
});

type TaxRateFormValues = z.infer<typeof taxRateSchema>;

export function TaxRatesCreate() {
  const navigate = useNavigate();
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [taxZones, setTaxZones] = useState<TaxZone[]>([]);

  const form = useForm<TaxRateFormValues>({
    resolver: zodResolver(taxRateSchema),
    defaultValues: {
      name: "",
      tax_zone_id: 0,
      rate: "",
      priority: 0,
      is_compound: false,
      is_active: true,
    },
  });

  const loadTaxZones = useCallback(async () => {
    try {
      const response = await api.get<{ data: TaxZone[] }>("/admin/tax/zones");
      setTaxZones(response.data.data);
    } catch {
      toast.error("Failed to load tax zones");
    }
  }, []);

  useEffect(() => {
    loadTaxZones();
  }, [loadTaxZones]);

  const handleSubmit = async (values: TaxRateFormValues) => {
    setIsSubmitting(true);

    try {
      await api.post("/admin/tax/rates", values);
      toast.success("Tax rate created successfully");
      navigate("/tax/rates");
    } catch {
      toast.error("Failed to create tax rate");
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="space-y-4">
      <PageHeader
        title="Create Tax Rate"
        description="Add a new tax rate for a geographic zone"
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
              <FormField
                control={form.control}
                name="name"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Name</FormLabel>
                    <FormControl>
                      <Input placeholder="US Standard Rate" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <div className="grid gap-4 sm:grid-cols-2">
                <FormField
                  control={form.control}
                  name="tax_zone_id"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Tax Zone</FormLabel>
                      <Select
                        value={field.value ? String(field.value) : ""}
                        onValueChange={(value) => field.onChange(Number(value))}
                      >
                        <FormControl>
                          <SelectTrigger>
                            <SelectValue placeholder="Select a zone" />
                          </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                          {taxZones.map((zone) => (
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
                <FormField
                  control={form.control}
                  name="rate"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Rate (%)</FormLabel>
                      <FormControl>
                        <Input placeholder="8.25" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>
              <FormField
                control={form.control}
                name="priority"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Priority</FormLabel>
                    <FormControl>
                      <Input type="number" min={0} {...field} />
                    </FormControl>
                    <FormDescription>
                      Lower values are applied first when multiple rates apply.
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
            <CardContent className="space-y-4">
              <FormField
                control={form.control}
                name="is_compound"
                render={({ field }) => (
                  <FormItem className="flex items-center justify-between rounded-lg border p-4">
                    <div className="space-y-0.5">
                      <FormLabel className="text-base">Compound</FormLabel>
                      <FormDescription>
                        Apply this rate on top of other tax rates
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
                name="is_active"
                render={({ field }) => (
                  <FormItem className="flex items-center justify-between rounded-lg border p-4">
                    <div className="space-y-0.5">
                      <FormLabel className="text-base">Active</FormLabel>
                      <FormDescription>
                        Enable this tax rate
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
                "Create Tax Rate"
              )}
            </Button>
            <Button
              type="button"
              variant="outline"
              onClick={() => navigate("/tax/rates")}
            >
              Cancel
            </Button>
          </div>
        </form>
      </Form>
    </div>
  );
}
