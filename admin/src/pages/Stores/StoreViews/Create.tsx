import { useEffect, useState } from "react";
import { useNavigate } from "react-router";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { Store } from "@/types/store";
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

const storeViewSchema = z.object({
  store_id: z.coerce.number().int().positive("Store is required"),
  code: z
    .string()
    .min(1, "Code is required")
    .max(255)
    .regex(
      /^[a-z0-9_-]+$/,
      "Code must contain only lowercase letters, numbers, hyphens, and underscores",
    ),
  name: z.string().min(1, "Name is required").max(255),
  locale: z.string().max(10).optional().or(z.literal("")),
  sort_order: z.coerce.number().int().min(0),
  is_active: z.boolean(),
});

type StoreViewFormValues = z.infer<typeof storeViewSchema>;

export function StoreViewsCreate() {
  const navigate = useNavigate();
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [stores, setStores] = useState<Store[]>([]);
  const [isLoadingStores, setIsLoadingStores] = useState(true);

  const form = useForm<StoreViewFormValues>({
    resolver: zodResolver(storeViewSchema),
    defaultValues: {
      store_id: 0,
      code: "",
      name: "",
      locale: "",
      sort_order: 0,
      is_active: true,
    },
  });

  useEffect(() => {
    const loadStores = async () => {
      try {
        const response = await api.get<{ data: Store[] }>("/admin/store/stores");
        setStores(response.data.data);
      } catch {
        toast.error("Failed to load stores");
      } finally {
        setIsLoadingStores(false);
      }
    };

    loadStores();
  }, []);

  const handleSubmit = async (values: StoreViewFormValues) => {
    setIsSubmitting(true);

    try {
      await api.post("/admin/store/store-views", {
        ...values,
        locale: values.locale || null,
      });
      toast.success("Store view created successfully");
      navigate("/stores/store-views");
    } catch {
      toast.error("Failed to create store view");
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="space-y-4">
      <PageHeader
        title="Create Store View"
        description="Add a new store view for a locale or storefront"
      />

      <Form {...form}>
        <form
          onSubmit={form.handleSubmit(handleSubmit)}
          className="space-y-6"
        >
          <Card>
            <CardHeader>
              <CardTitle>General Information</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <FormField
                control={form.control}
                name="store_id"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Store</FormLabel>
                    <Select
                      disabled={isLoadingStores}
                      onValueChange={(value) => field.onChange(Number(value))}
                      value={field.value ? String(field.value) : ""}
                    >
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue
                            placeholder={
                              isLoadingStores ? "Loading stores..." : "Select a store"
                            }
                          />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        {stores.map((store) => (
                          <SelectItem key={store.id} value={String(store.id)}>
                            {store.name} ({store.code})
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                    <FormDescription>
                      The store this view belongs to
                    </FormDescription>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <div className="grid gap-4 sm:grid-cols-2">
                <FormField
                  control={form.control}
                  name="name"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Name</FormLabel>
                      <FormControl>
                        <Input placeholder="Default Store View" {...field} />
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
                        <Input placeholder="default" {...field} />
                      </FormControl>
                      <FormDescription>
                        Unique identifier. Lowercase letters, numbers, hyphens,
                        underscores.
                      </FormDescription>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>
              <FormField
                control={form.control}
                name="locale"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Locale</FormLabel>
                    <FormControl>
                      <Input placeholder="en_US" {...field} />
                    </FormControl>
                    <FormDescription>
                      Locale code (e.g., en_US, fr_FR). Leave empty to use the
                      default.
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
                name="is_active"
                render={({ field }) => (
                  <FormItem className="flex items-center justify-between rounded-lg border p-4">
                    <div className="space-y-0.5">
                      <FormLabel className="text-base">Active</FormLabel>
                      <FormDescription>
                        Only active store views are accessible to customers
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
                      Lower values appear first.
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
                  Creating...
                </>
              ) : (
                "Create Store View"
              )}
            </Button>
            <Button
              type="button"
              variant="outline"
              onClick={() => navigate("/stores/store-views")}
            >
              Cancel
            </Button>
          </div>
        </form>
      </Form>
    </div>
  );
}
