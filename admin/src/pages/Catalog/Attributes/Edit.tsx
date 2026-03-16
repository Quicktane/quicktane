import { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router";
import { useFieldArray, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { Attribute } from "@/types/catalog";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Switch } from "@/components/ui/switch";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";

const optionSchema = z.object({
  label: z.string().min(1, "Label is required"),
  value: z.string().min(1, "Value is required"),
  sort_order: z.coerce.number().int().min(0),
});

const attributeSchema = z.object({
  code: z.string().min(1, "Code is required"),
  name: z.string().min(1, "Name is required"),
  is_required: z.boolean(),
  is_filterable: z.boolean(),
  is_visible: z.boolean(),
  sort_order: z.coerce.number().int().min(0),
  options: z.array(optionSchema).optional(),
});

type AttributeFormValues = z.infer<typeof attributeSchema>;

export function AttributesEdit() {
  const { uuid } = useParams<{ uuid: string }>();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [attributeType, setAttributeType] = useState("");

  const form = useForm<AttributeFormValues>({
    resolver: zodResolver(attributeSchema),
    defaultValues: {
      code: "",
      name: "",
      is_required: false,
      is_filterable: false,
      is_visible: true,
      sort_order: 0,
      options: [],
    },
  });

  const { fields, append, remove } = useFieldArray({
    control: form.control,
    name: "options",
  });

  const hasOptions =
    attributeType === "select" || attributeType === "multiselect";

  useEffect(() => {
    const loadAttribute = async () => {
      try {
        const response = await api.get<{ data: Attribute }>(
          `/admin/catalog/attributes/${uuid}`,
        );
        const attribute = response.data.data;

        setAttributeType(attribute.type);

        form.reset({
          code: attribute.code,
          name: attribute.name,
          is_required: attribute.is_required,
          is_filterable: attribute.is_filterable,
          is_visible: attribute.is_visible,
          sort_order: attribute.sort_order,
          options: attribute.options ?? [],
        });
      } catch {
        toast.error("Failed to load attribute");
        navigate("/catalog/attributes");
      } finally {
        setIsLoading(false);
      }
    };

    loadAttribute();
  }, [uuid, form, navigate]);

  const onSubmit = async (values: AttributeFormValues) => {
    setIsSubmitting(true);

    try {
      const payload = {
        ...values,
        options: hasOptions ? values.options : undefined,
      };

      await api.put(`/admin/catalog/attributes/${uuid}`, payload);
      toast.success("Attribute updated successfully");
      navigate("/catalog/attributes");
    } catch (error: unknown) {
      const axiosError = error as { response?: { data?: { message?: string } } };
      toast.error(axiosError.response?.data?.message ?? "Failed to update attribute");
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
      <PageHeader title="Edit Attribute" />
      <Form {...form}>
        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle>General</CardTitle>
              <CardDescription>
                Basic attribute configuration
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <FormField
                  control={form.control}
                  name="code"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Code</FormLabel>
                      <FormControl>
                        <Input {...field} disabled />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="name"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Name</FormLabel>
                      <FormControl>
                        <Input {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <FormLabel>Type</FormLabel>
                  <div>
                    <Badge variant="secondary" className="text-sm">
                      {attributeType}
                    </Badge>
                  </div>
                </div>
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
              <div className="flex gap-8">
                <FormField
                  control={form.control}
                  name="is_required"
                  render={({ field }) => (
                    <FormItem className="flex items-center gap-2 space-y-0">
                      <FormControl>
                        <Switch
                          checked={field.value}
                          onCheckedChange={field.onChange}
                        />
                      </FormControl>
                      <FormLabel>Required</FormLabel>
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="is_filterable"
                  render={({ field }) => (
                    <FormItem className="flex items-center gap-2 space-y-0">
                      <FormControl>
                        <Switch
                          checked={field.value}
                          onCheckedChange={field.onChange}
                        />
                      </FormControl>
                      <FormLabel>Filterable</FormLabel>
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="is_visible"
                  render={({ field }) => (
                    <FormItem className="flex items-center gap-2 space-y-0">
                      <FormControl>
                        <Switch
                          checked={field.value}
                          onCheckedChange={field.onChange}
                        />
                      </FormControl>
                      <FormLabel>Visible</FormLabel>
                    </FormItem>
                  )}
                />
              </div>
            </CardContent>
          </Card>

          {hasOptions && (
            <Card>
              <CardHeader>
                <CardTitle>Options</CardTitle>
                <CardDescription>
                  Define the available options for this attribute
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                {fields.map((field, index) => (
                  <div key={field.id} className="flex items-end gap-3">
                    <FormField
                      control={form.control}
                      name={`options.${index}.label`}
                      render={({ field }) => (
                        <FormItem className="flex-1">
                          {index === 0 && <FormLabel>Label</FormLabel>}
                          <FormControl>
                            <Input placeholder="Label" {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name={`options.${index}.value`}
                      render={({ field }) => (
                        <FormItem className="flex-1">
                          {index === 0 && <FormLabel>Value</FormLabel>}
                          <FormControl>
                            <Input placeholder="Value" {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name={`options.${index}.sort_order`}
                      render={({ field }) => (
                        <FormItem className="w-24">
                          {index === 0 && <FormLabel>Order</FormLabel>}
                          <FormControl>
                            <Input type="number" min={0} {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <Button
                      type="button"
                      variant="ghost"
                      size="icon"
                      onClick={() => remove(index)}
                    >
                      <Trash2 className="h-4 w-4 text-destructive" />
                    </Button>
                  </div>
                ))}
                <Button
                  type="button"
                  variant="outline"
                  size="sm"
                  onClick={() =>
                    append({ label: "", value: "", sort_order: fields.length })
                  }
                >
                  <Plus className="mr-2 h-4 w-4" />
                  Add Option
                </Button>
              </CardContent>
            </Card>
          )}

          <div className="flex gap-2">
            <Button type="submit" disabled={isSubmitting}>
              {isSubmitting ? "Saving..." : "Save Attribute"}
            </Button>
            <Button
              type="button"
              variant="outline"
              onClick={() => navigate("/catalog/attributes")}
            >
              Cancel
            </Button>
          </div>
        </form>
      </Form>
    </div>
  );
}
