import { useCallback, useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { TaxClass } from "@/types/tax";
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

const taxClassSchema = z.object({
  name: z.string().min(1, "Name is required").max(255),
  type: z.enum(["product", "customer"]),
  is_default: z.boolean(),
});

type TaxClassFormValues = z.infer<typeof taxClassSchema>;

export function TaxClassesEdit() {
  const { uuid } = useParams<{ uuid: string }>();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const form = useForm<TaxClassFormValues>({
    resolver: zodResolver(taxClassSchema),
    defaultValues: {
      name: "",
      type: "product",
      is_default: false,
    },
  });

  const loadTaxClass = useCallback(async () => {
    try {
      const response = await api.get<{ data: TaxClass }>(
        `/admin/tax/classes/${uuid}`,
      );
      const taxClass = response.data.data;

      form.reset({
        name: taxClass.name,
        type: taxClass.type,
        is_default: taxClass.is_default,
      });
    } catch {
      toast.error("Failed to load tax class");
      navigate("/tax/classes");
    } finally {
      setIsLoading(false);
    }
  }, [uuid, form, navigate]);

  useEffect(() => {
    loadTaxClass();
  }, [loadTaxClass]);

  const handleSubmit = async (values: TaxClassFormValues) => {
    setIsSubmitting(true);

    try {
      await api.put(`/admin/tax/classes/${uuid}`, values);
      toast.success("Tax class updated successfully");
      navigate("/tax/classes");
    } catch {
      toast.error("Failed to update tax class");
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
        title="Edit Tax Class"
        description="Update tax classification details"
      />

      <Form {...form}>
        <form
          onSubmit={form.handleSubmit(handleSubmit)}
          className="space-y-6"
        >
          <Card>
            <CardHeader>
              <CardTitle>Tax Class Details</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <FormField
                control={form.control}
                name="name"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Name</FormLabel>
                    <FormControl>
                      <Input placeholder="Taxable Goods" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <FormField
                control={form.control}
                name="type"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Type</FormLabel>
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
                        <SelectItem value="product">Product</SelectItem>
                        <SelectItem value="customer">Customer</SelectItem>
                      </SelectContent>
                    </Select>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <FormField
                control={form.control}
                name="is_default"
                render={({ field }) => (
                  <FormItem className="flex items-center justify-between rounded-lg border p-4">
                    <div className="space-y-0.5">
                      <FormLabel className="text-base">Default</FormLabel>
                      <FormDescription>
                        Use this class as the default for its type
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
              onClick={() => navigate("/tax/classes")}
            >
              Cancel
            </Button>
          </div>
        </form>
      </Form>
    </div>
  );
}
