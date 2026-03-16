import { useCallback, useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { TaxRule, TaxRate, TaxClass } from "@/types/tax";
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

const taxRuleSchema = z.object({
  name: z.string().min(1, "Name is required").max(255),
  tax_rate_id: z.coerce.number().int().min(1, "Tax rate is required"),
  product_tax_class_id: z.coerce.number().int().min(1, "Product tax class is required"),
  customer_tax_class_id: z.coerce.number().int().min(1, "Customer tax class is required"),
  priority: z.coerce.number().int().min(0),
  is_active: z.boolean(),
});

type TaxRuleFormValues = z.infer<typeof taxRuleSchema>;

export function TaxRulesEdit() {
  const { uuid } = useParams<{ uuid: string }>();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [taxRates, setTaxRates] = useState<TaxRate[]>([]);
  const [productTaxClasses, setProductTaxClasses] = useState<TaxClass[]>([]);
  const [customerTaxClasses, setCustomerTaxClasses] = useState<TaxClass[]>([]);

  const form = useForm<TaxRuleFormValues>({
    resolver: zodResolver(taxRuleSchema),
    defaultValues: {
      name: "",
      tax_rate_id: 0,
      product_tax_class_id: 0,
      customer_tax_class_id: 0,
      priority: 0,
      is_active: true,
    },
  });

  const loadData = useCallback(async () => {
    try {
      const [ruleResponse, ratesResponse, classesResponse] = await Promise.all([
        api.get<{ data: TaxRule }>(`/admin/tax/rules/${uuid}`),
        api.get<{ data: TaxRate[] }>("/admin/tax/rates"),
        api.get<{ data: TaxClass[] }>("/admin/tax/classes"),
      ]);

      const taxRule = ruleResponse.data.data;
      setTaxRates(ratesResponse.data.data);

      const allClasses = classesResponse.data.data;
      setProductTaxClasses(allClasses.filter((c) => c.type === "product"));
      setCustomerTaxClasses(allClasses.filter((c) => c.type === "customer"));

      form.reset({
        name: taxRule.name,
        tax_rate_id: taxRule.tax_rate_id,
        product_tax_class_id: taxRule.product_tax_class_id,
        customer_tax_class_id: taxRule.customer_tax_class_id,
        priority: taxRule.priority,
        is_active: taxRule.is_active,
      });
    } catch {
      toast.error("Failed to load tax rule");
      navigate("/tax/rules");
    } finally {
      setIsLoading(false);
    }
  }, [uuid, form, navigate]);

  useEffect(() => {
    loadData();
  }, [loadData]);

  const handleSubmit = async (values: TaxRuleFormValues) => {
    setIsSubmitting(true);

    try {
      await api.put(`/admin/tax/rules/${uuid}`, values);
      toast.success("Tax rule updated successfully");
      navigate("/tax/rules");
    } catch {
      toast.error("Failed to update tax rule");
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
        title="Edit Tax Rule"
        description="Update tax rule details"
      />

      <Form {...form}>
        <form
          onSubmit={form.handleSubmit(handleSubmit)}
          className="space-y-6"
        >
          <Card>
            <CardHeader>
              <CardTitle>Rule Details</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <FormField
                control={form.control}
                name="name"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Name</FormLabel>
                    <FormControl>
                      <Input placeholder="US Tax Rule" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <FormField
                control={form.control}
                name="tax_rate_id"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Tax Rate</FormLabel>
                    <Select
                      value={field.value ? String(field.value) : ""}
                      onValueChange={(value) => field.onChange(Number(value))}
                    >
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue placeholder="Select a tax rate" />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        {taxRates.map((rate) => (
                          <SelectItem key={rate.id} value={String(rate.id)}>
                            {rate.name} ({rate.rate}%)
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <div className="grid gap-4 sm:grid-cols-2">
                <FormField
                  control={form.control}
                  name="product_tax_class_id"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Product Tax Class</FormLabel>
                      <Select
                        value={field.value ? String(field.value) : ""}
                        onValueChange={(value) => field.onChange(Number(value))}
                      >
                        <FormControl>
                          <SelectTrigger>
                            <SelectValue placeholder="Select a class" />
                          </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                          {productTaxClasses.map((taxClass) => (
                            <SelectItem
                              key={taxClass.id}
                              value={String(taxClass.id)}
                            >
                              {taxClass.name}
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
                  name="customer_tax_class_id"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Customer Tax Class</FormLabel>
                      <Select
                        value={field.value ? String(field.value) : ""}
                        onValueChange={(value) => field.onChange(Number(value))}
                      >
                        <FormControl>
                          <SelectTrigger>
                            <SelectValue placeholder="Select a class" />
                          </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                          {customerTaxClasses.map((taxClass) => (
                            <SelectItem
                              key={taxClass.id}
                              value={String(taxClass.id)}
                            >
                              {taxClass.name}
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
                name="priority"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Priority</FormLabel>
                    <FormControl>
                      <Input type="number" min={0} {...field} />
                    </FormControl>
                    <FormDescription>
                      Lower values have higher priority.
                    </FormDescription>
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
                        Enable this tax rule
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
              onClick={() => navigate("/tax/rules")}
            >
              Cancel
            </Button>
          </div>
        </form>
      </Form>
    </div>
  );
}
