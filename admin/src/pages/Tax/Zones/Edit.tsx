import { useCallback, useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Loader2, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { TaxZone, TaxZoneRule } from "@/types/tax";
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

const taxZoneSchema = z.object({
  name: z.string().min(1, "Name is required").max(255),
  description: z.string().optional().or(z.literal("")),
  is_active: z.boolean(),
  sort_order: z.coerce.number().int().min(0),
});

type TaxZoneFormValues = z.infer<typeof taxZoneSchema>;

type ZoneRuleDraft = Omit<TaxZoneRule, "id">;

export function TaxZonesEdit() {
  const { uuid } = useParams<{ uuid: string }>();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [rules, setRules] = useState<ZoneRuleDraft[]>([]);

  const form = useForm<TaxZoneFormValues>({
    resolver: zodResolver(taxZoneSchema),
    defaultValues: {
      name: "",
      description: "",
      is_active: true,
      sort_order: 0,
    },
  });

  const loadTaxZone = useCallback(async () => {
    try {
      const response = await api.get<{ data: TaxZone }>(
        `/admin/tax/zones/${uuid}`,
      );
      const taxZone = response.data.data;

      form.reset({
        name: taxZone.name,
        description: taxZone.description ?? "",
        is_active: taxZone.is_active,
        sort_order: taxZone.sort_order,
      });

      setRules(
        (taxZone.rules ?? []).map(({ country_id, region_id, postcode_from, postcode_to }) => ({
          country_id,
          region_id,
          postcode_from,
          postcode_to,
        })),
      );
    } catch {
      toast.error("Failed to load tax zone");
      navigate("/tax/zones");
    } finally {
      setIsLoading(false);
    }
  }, [uuid, form, navigate]);

  useEffect(() => {
    loadTaxZone();
  }, [loadTaxZone]);

  const addRule = () => {
    setRules((previous) => [
      ...previous,
      { country_id: 0, region_id: null, postcode_from: null, postcode_to: null },
    ]);
  };

  const removeRule = (index: number) => {
    setRules((previous) => previous.filter((_, ruleIndex) => ruleIndex !== index));
  };

  const updateRule = (
    index: number,
    field: keyof ZoneRuleDraft,
    value: string,
  ) => {
    setRules((previous) =>
      previous.map((rule, ruleIndex) => {
        if (ruleIndex !== index) return rule;

        if (field === "country_id") {
          return { ...rule, country_id: Number(value) };
        }

        if (field === "region_id") {
          return { ...rule, region_id: value ? Number(value) : null };
        }

        return { ...rule, [field]: value || null };
      }),
    );
  };

  const handleSubmit = async (values: TaxZoneFormValues) => {
    setIsSubmitting(true);

    try {
      await api.put(`/admin/tax/zones/${uuid}`, {
        ...values,
        description: values.description || null,
        rules,
      });
      toast.success("Tax zone updated successfully");
      navigate("/tax/zones");
    } catch {
      toast.error("Failed to update tax zone");
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
        title="Edit Tax Zone"
        description="Update geographic tax zone details"
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
                      <Input placeholder="United States" {...field} />
                    </FormControl>
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
                      <Textarea placeholder="Optional description..." {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <div className="grid gap-4 sm:grid-cols-2">
                <FormField
                  control={form.control}
                  name="is_active"
                  render={({ field }) => (
                    <FormItem className="flex items-center justify-between rounded-lg border p-4">
                      <div className="space-y-0.5">
                        <FormLabel className="text-base">Active</FormLabel>
                        <FormDescription>
                          Enable this tax zone
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
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between">
              <CardTitle>Zone Rules</CardTitle>
              <Button type="button" variant="outline" size="sm" onClick={addRule}>
                <Plus className="mr-2 h-4 w-4" />
                Add Rule
              </Button>
            </CardHeader>
            <CardContent className="space-y-4">
              {rules.length === 0 && (
                <p className="text-sm text-muted-foreground">
                  No rules added. Add geographic rules to define which areas this zone covers.
                </p>
              )}
              {rules.map((rule, index) => (
                <div
                  key={index}
                  className="grid gap-3 rounded-lg border p-4 sm:grid-cols-4"
                >
                  <div className="space-y-1">
                    <label className="text-sm font-medium">Country ID</label>
                    <Input
                      type="number"
                      min={0}
                      placeholder="840"
                      value={rule.country_id || ""}
                      onChange={(event) =>
                        updateRule(index, "country_id", event.target.value)
                      }
                    />
                  </div>
                  <div className="space-y-1">
                    <label className="text-sm font-medium">Region ID</label>
                    <Input
                      type="number"
                      min={0}
                      placeholder="Optional"
                      value={rule.region_id ?? ""}
                      onChange={(event) =>
                        updateRule(index, "region_id", event.target.value)
                      }
                    />
                  </div>
                  <div className="space-y-1">
                    <label className="text-sm font-medium">Postcode From</label>
                    <Input
                      placeholder="10000"
                      value={rule.postcode_from ?? ""}
                      onChange={(event) =>
                        updateRule(index, "postcode_from", event.target.value)
                      }
                    />
                  </div>
                  <div className="space-y-1">
                    <label className="text-sm font-medium">Postcode To</label>
                    <div className="flex gap-2">
                      <Input
                        placeholder="99999"
                        value={rule.postcode_to ?? ""}
                        onChange={(event) =>
                          updateRule(index, "postcode_to", event.target.value)
                        }
                      />
                      <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        onClick={() => removeRule(index)}
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
              onClick={() => navigate("/tax/zones")}
            >
              Cancel
            </Button>
          </div>
        </form>
      </Form>
    </div>
  );
}
