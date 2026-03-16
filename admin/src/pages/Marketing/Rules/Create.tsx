import { useState } from "react";
import { useNavigate } from "react-router";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
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
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";

const ruleSchema = z.object({
  name: z.string().min(1, "Name is required").max(255),
  description: z.string().optional().or(z.literal("")),
  is_active: z.boolean(),
  from_date: z.string().optional().or(z.literal("")),
  to_date: z.string().optional().or(z.literal("")),
  priority: z.coerce.number().int().min(0),
  stop_further_processing: z.boolean(),
  action_type: z.enum(["by_percent", "by_fixed", "buy_x_get_y", "free_shipping"]),
  action_amount: z.string().optional().or(z.literal("")),
  max_discount_amount: z.string().optional().or(z.literal("")),
  apply_to_shipping: z.boolean(),
  conditions_json: z.string().optional().or(z.literal("")),
});

type RuleFormValues = z.infer<typeof ruleSchema>;

export function MarketingRulesCreate() {
  const navigate = useNavigate();
  const [isSubmitting, setIsSubmitting] = useState(false);

  const form = useForm<RuleFormValues>({
    resolver: zodResolver(ruleSchema),
    defaultValues: {
      name: "",
      description: "",
      is_active: true,
      from_date: "",
      to_date: "",
      priority: 0,
      stop_further_processing: false,
      action_type: "by_percent",
      action_amount: "",
      max_discount_amount: "",
      apply_to_shipping: false,
      conditions_json: "",
    },
  });

  const handleSubmit = async (values: RuleFormValues) => {
    setIsSubmitting(true);

    let conditions = undefined;
    if (values.conditions_json) {
      try {
        conditions = JSON.parse(values.conditions_json);
      } catch {
        toast.error("Invalid JSON in conditions field");
        setIsSubmitting(false);
        return;
      }
    }

    try {
      await api.post("/admin/promotion/rules", {
        name: values.name,
        description: values.description || null,
        is_active: values.is_active,
        from_date: values.from_date || null,
        to_date: values.to_date || null,
        priority: values.priority,
        stop_further_processing: values.stop_further_processing,
        action_type: values.action_type,
        action_amount: values.action_amount || null,
        max_discount_amount: values.max_discount_amount || null,
        apply_to_shipping: values.apply_to_shipping,
        conditions,
      });
      toast.success("Price rule created successfully");
      navigate("/marketing/rules");
    } catch {
      toast.error("Failed to create price rule");
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="space-y-4">
      <PageHeader
        title="Create Cart Price Rule"
        description="Set up a new promotional discount rule"
      />

      <Form {...form}>
        <form
          onSubmit={form.handleSubmit(handleSubmit)}
          className="space-y-6"
        >
          <Tabs defaultValue="general">
            <TabsList>
              <TabsTrigger value="general">General</TabsTrigger>
              <TabsTrigger value="conditions">Conditions</TabsTrigger>
            </TabsList>

            <TabsContent value="general" className="space-y-6 mt-4">
              <Card>
                <CardHeader>
                  <CardTitle>Rule Information</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  <FormField
                    control={form.control}
                    name="name"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Name</FormLabel>
                        <FormControl>
                          <Input placeholder="Summer Sale 20% Off" {...field} />
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
                          <Textarea
                            placeholder="Optional description..."
                            {...field}
                          />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  <div className="grid gap-4 sm:grid-cols-2">
                    <FormField
                      control={form.control}
                      name="from_date"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>From Date</FormLabel>
                          <FormControl>
                            <Input type="date" {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="to_date"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>To Date</FormLabel>
                          <FormControl>
                            <Input type="date" {...field} />
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
                          Lower values are processed first.
                        </FormDescription>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle>Action</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  <FormField
                    control={form.control}
                    name="action_type"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Action Type</FormLabel>
                        <Select
                          value={field.value}
                          onValueChange={field.onChange}
                        >
                          <FormControl>
                            <SelectTrigger>
                              <SelectValue />
                            </SelectTrigger>
                          </FormControl>
                          <SelectContent>
                            <SelectItem value="by_percent">
                              Percent Discount
                            </SelectItem>
                            <SelectItem value="by_fixed">
                              Fixed Discount
                            </SelectItem>
                            <SelectItem value="buy_x_get_y">
                              Buy X Get Y
                            </SelectItem>
                            <SelectItem value="free_shipping">
                              Free Shipping
                            </SelectItem>
                          </SelectContent>
                        </Select>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  <div className="grid gap-4 sm:grid-cols-2">
                    <FormField
                      control={form.control}
                      name="action_amount"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Discount Amount</FormLabel>
                          <FormControl>
                            <Input placeholder="20" {...field} />
                          </FormControl>
                          <FormDescription>
                            Percent or fixed amount depending on action type.
                          </FormDescription>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="max_discount_amount"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Max Discount Amount</FormLabel>
                          <FormControl>
                            <Input placeholder="100.00" {...field} />
                          </FormControl>
                          <FormDescription>
                            Leave empty for no maximum.
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
                            Enable this price rule
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
                    name="stop_further_processing"
                    render={({ field }) => (
                      <FormItem className="flex items-center justify-between rounded-lg border p-4">
                        <div className="space-y-0.5">
                          <FormLabel className="text-base">
                            Stop Further Processing
                          </FormLabel>
                          <FormDescription>
                            Prevent other rules from being applied after this one
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
                    name="apply_to_shipping"
                    render={({ field }) => (
                      <FormItem className="flex items-center justify-between rounded-lg border p-4">
                        <div className="space-y-0.5">
                          <FormLabel className="text-base">
                            Apply to Shipping
                          </FormLabel>
                          <FormDescription>
                            Include shipping amount in the discount calculation
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
            </TabsContent>

            <TabsContent value="conditions" className="mt-4">
              <Card>
                <CardHeader>
                  <CardTitle>Conditions</CardTitle>
                </CardHeader>
                <CardContent>
                  <FormField
                    control={form.control}
                    name="conditions_json"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Conditions (JSON)</FormLabel>
                        <FormControl>
                          <Textarea
                            placeholder='{"type":"combine","aggregator":"all","is_inverted":false,"children":[]}'
                            className="min-h-48 font-mono text-sm"
                            {...field}
                          />
                        </FormControl>
                        <FormDescription>
                          Define conditions as a JSON condition tree. Leave empty to apply to all carts.
                        </FormDescription>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                </CardContent>
              </Card>
            </TabsContent>
          </Tabs>

          <div className="flex gap-2">
            <Button type="submit" disabled={isSubmitting}>
              {isSubmitting ? (
                <>
                  <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                  Creating...
                </>
              ) : (
                "Create Rule"
              )}
            </Button>
            <Button
              type="button"
              variant="outline"
              onClick={() => navigate("/marketing/rules")}
            >
              Cancel
            </Button>
          </div>
        </form>
      </Form>
    </div>
  );
}
