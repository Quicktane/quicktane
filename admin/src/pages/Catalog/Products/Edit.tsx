import { useCallback, useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { GripVertical, ImagePlus, Star, X } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type {
  Attribute,
  AttributeSet,
  AttributeSetAttribute,
  Product,
} from "@/types/catalog";
import type { MediaFile } from "@/types/media";
import { PageHeader } from "@/components/PageHeader";
import { MediaPicker } from "@/components/media/MediaPicker";
import { CategoryTreeSelect } from "@/components/category/CategoryTreeSelect";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Switch } from "@/components/ui/switch";
import { Badge } from "@/components/ui/badge";
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
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Skeleton } from "@/components/ui/skeleton";

const productSchema = z.object({
  sku: z.string().min(1, "SKU is required"),
  name: z.string().min(1, "Name is required"),
  slug: z.string().min(1, "Slug is required"),
  description: z.string().optional(),
  short_description: z.string().optional(),
  is_active: z.boolean(),
  meta_title: z.string().optional(),
  meta_description: z.string().optional(),
  base_price: z.coerce.number().min(0, "Price must be non-negative"),
  special_price: z.coerce.number().min(0).optional().or(z.literal("")),
  special_price_from: z.string().optional(),
  special_price_to: z.string().optional(),
  cost: z.coerce.number().min(0).optional().or(z.literal("")),
  weight: z.coerce.number().min(0).optional().or(z.literal("")),
});

type ProductFormValues = z.infer<typeof productSchema>;

interface MediaItem {
  media_file_id: number;
  uuid: string;
  url: string;
  filename: string;
  position: number;
  label: string;
  is_main: boolean;
}

export function ProductsEdit() {
  const { uuid } = useParams<{ uuid: string }>();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [productType, setProductType] = useState("");
  const [attributeSetName, setAttributeSetName] = useState("");
  const [setAttributes, setSetAttributes] = useState<
    AttributeSetAttribute[]
  >([]);
  const [attributeValues, setAttributeValues] = useState<
    Record<string, string>
  >({});
  const [allAttributeDetails, setAllAttributeDetails] = useState<
    Record<string, Attribute>
  >({});
  const [mediaItems, setMediaItems] = useState<MediaItem[]>([]);
  const [categoryUuids, setCategoryUuids] = useState<string[]>([]);

  const form = useForm<ProductFormValues>({
    resolver: zodResolver(productSchema),
    defaultValues: {
      sku: "",
      name: "",
      slug: "",
      description: "",
      short_description: "",
      is_active: true,
      meta_title: "",
      meta_description: "",
      base_price: 0,
      special_price: "",
      special_price_from: "",
      special_price_to: "",
      cost: "",
      weight: "",
    },
  });

  const loadProduct = useCallback(async () => {
    try {
      const response = await api.get<{ data: Product }>(
        `/admin/catalog/products/${uuid}`,
      );
      const product = response.data.data;

      setProductType(product.type);
      setAttributeSetName(product.attribute_set?.name ?? "");

      form.reset({
        sku: product.sku,
        name: product.name,
        slug: product.slug,
        description: product.description ?? "",
        short_description: product.short_description ?? "",
        is_active: product.is_active,
        meta_title: product.meta_title ?? "",
        meta_description: product.meta_description ?? "",
        base_price: parseFloat(product.base_price),
        special_price: product.special_price
          ? parseFloat(product.special_price)
          : "",
        special_price_from: product.special_price_from ?? "",
        special_price_to: product.special_price_to ?? "",
        cost: product.cost ? parseFloat(product.cost) : "",
        weight: product.weight ? parseFloat(product.weight) : "",
      });

      if (product.media) {
        setMediaItems(
          product.media.map((media) => ({
            media_file_id: media.media_file_id,
            uuid: media.uuid,
            url: media.url,
            filename: media.filename,
            position: media.position,
            label: media.label ?? "",
            is_main: media.is_main,
          })),
        );
      }

      if (product.categories) {
        setCategoryUuids(
          product.categories.map((category) => category.uuid),
        );
      }

      if (product.attribute_values) {
        const values: Record<string, string> = {};
        for (const attributeValue of product.attribute_values) {
          if (attributeValue.attribute) {
            values[attributeValue.attribute.uuid] =
              attributeValue.value ?? "";
          }
        }
        setAttributeValues(values);
      }

      if (product.attribute_set?.uuid) {
        const setResponse = await api.get<{ data: AttributeSet }>(
          `/admin/catalog/attribute-sets/${product.attribute_set.uuid}`,
        );

        const attributes = setResponse.data.data.attributes ?? [];
        setSetAttributes(attributes);

        const detailPromises = attributes.map((attribute) =>
          api
            .get<{ data: Attribute }>(
              `/admin/catalog/attributes/${attribute.uuid}`,
            )
            .then((res) => res.data.data),
        );

        const details = await Promise.all(detailPromises);
        const detailsMap: Record<string, Attribute> = {};

        for (const detail of details) {
          detailsMap[detail.uuid] = detail;
        }

        setAllAttributeDetails(detailsMap);
      }
    } catch {
      toast.error("Failed to load product");
      navigate("/catalog/products");
    } finally {
      setIsLoading(false);
    }
  }, [uuid, form, navigate]);

  useEffect(() => {
    loadProduct();
  }, [loadProduct]);

  const handleMediaSelect = (files: MediaFile[]) => {
    const newItems: MediaItem[] = files.map((file, index) => ({
      media_file_id: parseInt(file.uuid, 10) || 0,
      uuid: file.uuid,
      url: file.url,
      filename: file.filename,
      position: mediaItems.length + index,
      label: "",
      is_main: mediaItems.length === 0 && index === 0,
    }));

    setMediaItems((prev) => [...prev, ...newItems]);
  };

  const handleRemoveMedia = (mediaUuid: string) => {
    setMediaItems((prev) => {
      const filtered = prev.filter((item) => item.uuid !== mediaUuid);
      if (filtered[0] && !filtered.some((item) => item.is_main)) {
        filtered[0].is_main = true;
      }
      return filtered;
    });
  };

  const handleSetMainMedia = (mediaUuid: string) => {
    setMediaItems((prev) =>
      prev.map((item) => ({
        ...item,
        is_main: item.uuid === mediaUuid,
      })),
    );
  };

  const handleUpdateMediaLabel = (mediaUuid: string, label: string) => {
    setMediaItems((prev) =>
      prev.map((item) =>
        item.uuid === mediaUuid ? { ...item, label } : item,
      ),
    );
  };

  const handleUpdateMediaPosition = (mediaUuid: string, position: number) => {
    setMediaItems((prev) =>
      prev.map((item) =>
        item.uuid === mediaUuid ? { ...item, position } : item,
      ),
    );
  };

  const onSubmit = async (values: ProductFormValues) => {
    setIsSubmitting(true);

    try {
      const payload = {
        ...values,
        special_price: values.special_price || null,
        cost: values.cost || null,
        weight: values.weight || null,
        special_price_from: values.special_price_from || null,
        special_price_to: values.special_price_to || null,
        attribute_values: Object.entries(attributeValues)
          .filter(([, value]) => value !== "")
          .map(([attributeUuid, value]) => ({
            attribute_uuid: attributeUuid,
            value,
          })),
        media: mediaItems.map((item) => ({
          media_file_uuid: item.uuid,
          position: item.position,
          label: item.label || null,
          is_main: item.is_main,
        })),
        category_uuids: categoryUuids,
      };

      await api.put(`/admin/catalog/products/${uuid}`, payload);
      toast.success("Product updated successfully");
      navigate("/catalog/products");
    } catch (error: unknown) {
      const axiosError = error as {
        response?: { data?: { message?: string } };
      };
      toast.error(
        axiosError.response?.data?.message ?? "Failed to update product",
      );
    } finally {
      setIsSubmitting(false);
    }
  };

  const renderAttributeField = (attribute: AttributeSetAttribute) => {
    const detail = allAttributeDetails[attribute.uuid];
    const value = attributeValues[attribute.uuid] ?? "";

    const handleChange = (newValue: string) => {
      setAttributeValues((prev) => ({
        ...prev,
        [attribute.uuid]: newValue,
      }));
    };

    switch (attribute.type) {
      case "textarea":
        return (
          <div key={attribute.uuid} className="space-y-2">
            <label className="text-sm font-medium">{attribute.name}</label>
            <Textarea
              value={value}
              onChange={(e) => handleChange(e.target.value)}
              rows={3}
            />
          </div>
        );

      case "boolean":
        return (
          <div
            key={attribute.uuid}
            className="flex items-center gap-2 space-y-0"
          >
            <Switch
              checked={value === "1"}
              onCheckedChange={(checked) =>
                handleChange(checked ? "1" : "0")
              }
            />
            <label className="text-sm font-medium">{attribute.name}</label>
          </div>
        );

      case "select":
        return (
          <div key={attribute.uuid} className="space-y-2">
            <label className="text-sm font-medium">{attribute.name}</label>
            <Select value={value} onValueChange={handleChange}>
              <SelectTrigger>
                <SelectValue placeholder={`Select ${attribute.name}`} />
              </SelectTrigger>
              <SelectContent>
                {detail?.options?.map((option) => (
                  <SelectItem key={option.value} value={option.value}>
                    {option.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        );

      case "multiselect":
        return (
          <div key={attribute.uuid} className="space-y-2">
            <label className="text-sm font-medium">{attribute.name}</label>
            <div className="space-y-1 rounded-md border p-2">
              {detail?.options?.map((option) => {
                const selectedValues = value ? value.split(",") : [];
                const isChecked = selectedValues.includes(option.value);

                return (
                  <label
                    key={option.value}
                    className="flex cursor-pointer items-center gap-2 rounded px-2 py-1 text-sm hover:bg-muted"
                  >
                    <input
                      type="checkbox"
                      checked={isChecked}
                      onChange={(e) => {
                        const updated = e.target.checked
                          ? [...selectedValues, option.value]
                          : selectedValues.filter((v) => v !== option.value);
                        handleChange(updated.join(","));
                      }}
                    />
                    {option.label}
                  </label>
                );
              })}
            </div>
          </div>
        );

      case "date":
        return (
          <div key={attribute.uuid} className="space-y-2">
            <label className="text-sm font-medium">{attribute.name}</label>
            <Input
              type="date"
              value={value}
              onChange={(e) => handleChange(e.target.value)}
            />
          </div>
        );

      case "number":
        return (
          <div key={attribute.uuid} className="space-y-2">
            <label className="text-sm font-medium">{attribute.name}</label>
            <Input
              type="number"
              value={value}
              onChange={(e) => handleChange(e.target.value)}
            />
          </div>
        );

      default:
        return (
          <div key={attribute.uuid} className="space-y-2">
            <label className="text-sm font-medium">{attribute.name}</label>
            <Input
              value={value}
              onChange={(e) => handleChange(e.target.value)}
            />
          </div>
        );
    }
  };

  const groupedSetAttributes = setAttributes.reduce<
    Record<string, AttributeSetAttribute[]>
  >((groups, attribute) => {
    const group = attribute.pivot.group_name || "General";
    if (!groups[group]) {
      groups[group] = [];
    }
    groups[group].push(attribute);
    return groups;
  }, {});

  if (isLoading) {
    return (
      <div className="space-y-4">
        <Skeleton className="h-8 w-48" />
        <Skeleton className="h-10 w-96" />
        <Skeleton className="h-96 w-full" />
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <PageHeader
        title="Edit Product"
        actions={
          <div className="flex items-center gap-2">
            <Badge variant="secondary">{productType}</Badge>
            {attributeSetName && (
              <Badge variant="outline">{attributeSetName}</Badge>
            )}
          </div>
        }
      />

      <Form {...form}>
        <form onSubmit={form.handleSubmit(onSubmit)}>
          <Tabs defaultValue="general" className="space-y-4">
            <TabsList>
              <TabsTrigger value="general">General</TabsTrigger>
              <TabsTrigger value="pricing">Pricing</TabsTrigger>
              <TabsTrigger value="attributes">Attributes</TabsTrigger>
              <TabsTrigger value="media">Media</TabsTrigger>
              <TabsTrigger value="categories">Categories</TabsTrigger>
            </TabsList>

            <TabsContent value="general">
              <Card>
                <CardHeader>
                  <CardTitle>General Information</CardTitle>
                  <CardDescription>
                    Basic product details
                  </CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div className="grid grid-cols-2 gap-4">
                    <FormField
                      control={form.control}
                      name="sku"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>SKU</FormLabel>
                          <FormControl>
                            <Input {...field} />
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

                  <FormField
                    control={form.control}
                    name="slug"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Slug</FormLabel>
                        <FormControl>
                          <Input {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="short_description"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Short Description</FormLabel>
                        <FormControl>
                          <Textarea rows={2} {...field} />
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
                          <Textarea rows={5} {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="is_active"
                    render={({ field }) => (
                      <FormItem className="flex items-center gap-2 space-y-0">
                        <FormControl>
                          <Switch
                            checked={field.value}
                            onCheckedChange={field.onChange}
                          />
                        </FormControl>
                        <FormLabel>Active</FormLabel>
                      </FormItem>
                    )}
                  />

                  <div className="grid grid-cols-2 gap-4">
                    <FormField
                      control={form.control}
                      name="meta_title"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Meta Title</FormLabel>
                          <FormControl>
                            <Input {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="meta_description"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Meta Description</FormLabel>
                          <FormControl>
                            <Input {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </div>
                </CardContent>
              </Card>
            </TabsContent>

            <TabsContent value="pricing">
              <Card>
                <CardHeader>
                  <CardTitle>Pricing</CardTitle>
                  <CardDescription>
                    Product pricing and cost information
                  </CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div className="grid grid-cols-2 gap-4">
                    <FormField
                      control={form.control}
                      name="base_price"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Base Price</FormLabel>
                          <FormControl>
                            <Input
                              type="number"
                              step="0.01"
                              min={0}
                              {...field}
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="special_price"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Special Price</FormLabel>
                          <FormControl>
                            <Input
                              type="number"
                              step="0.01"
                              min={0}
                              placeholder="Leave empty for no special price"
                              {...field}
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </div>
                  <div className="grid grid-cols-2 gap-4">
                    <FormField
                      control={form.control}
                      name="special_price_from"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Special Price From</FormLabel>
                          <FormControl>
                            <Input type="date" {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="special_price_to"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Special Price To</FormLabel>
                          <FormControl>
                            <Input type="date" {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </div>
                  <div className="grid grid-cols-2 gap-4">
                    <FormField
                      control={form.control}
                      name="cost"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Cost</FormLabel>
                          <FormControl>
                            <Input
                              type="number"
                              step="0.01"
                              min={0}
                              placeholder="Product cost"
                              {...field}
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="weight"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Weight</FormLabel>
                          <FormControl>
                            <Input
                              type="number"
                              step="0.01"
                              min={0}
                              placeholder="Product weight"
                              {...field}
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </div>
                </CardContent>
              </Card>
            </TabsContent>

            <TabsContent value="attributes">
              <Card>
                <CardHeader>
                  <CardTitle>Attributes</CardTitle>
                  <CardDescription>
                    Set attribute values for this product
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  {setAttributes.length === 0 ? (
                    <p className="py-4 text-center text-sm text-muted-foreground">
                      No attributes in this product's attribute set.
                    </p>
                  ) : (
                    <div className="space-y-6">
                      {Object.entries(groupedSetAttributes)
                        .sort(([a], [b]) => a.localeCompare(b))
                        .map(([groupName, attributes]) => (
                          <div key={groupName} className="space-y-4">
                            <h4 className="text-sm font-semibold text-muted-foreground">
                              {groupName}
                            </h4>
                            <div className="grid grid-cols-2 gap-4">
                              {attributes
                                .sort(
                                  (a, b) =>
                                    a.pivot.sort_order - b.pivot.sort_order,
                                )
                                .map(renderAttributeField)}
                            </div>
                          </div>
                        ))}
                    </div>
                  )}
                </CardContent>
              </Card>
            </TabsContent>

            <TabsContent value="media">
              <Card>
                <CardHeader>
                  <CardTitle>Media</CardTitle>
                  <CardDescription>
                    Product images and files
                  </CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <MediaPicker
                    onSelect={handleMediaSelect}
                    multiple
                    trigger={
                      <Button variant="outline" type="button">
                        <ImagePlus className="mr-2 h-4 w-4" />
                        Add Media
                      </Button>
                    }
                  />

                  {mediaItems.length === 0 ? (
                    <p className="py-8 text-center text-sm text-muted-foreground">
                      No media added yet. Click "Add Media" to select images.
                    </p>
                  ) : (
                    <div className="space-y-2">
                      {mediaItems
                        .sort((a, b) => a.position - b.position)
                        .map((item) => (
                          <div
                            key={item.uuid}
                            className="flex items-center gap-3 rounded-md border p-3"
                          >
                            <GripVertical className="h-4 w-4 shrink-0 text-muted-foreground" />
                            <img
                              src={item.url}
                              alt={item.label || item.filename}
                              className="h-16 w-16 rounded-md object-cover"
                            />
                            <div className="flex flex-1 flex-col gap-2">
                              <span className="text-sm font-medium">
                                {item.filename}
                              </span>
                              <Input
                                placeholder="Label (optional)"
                                value={item.label}
                                onChange={(e) =>
                                  handleUpdateMediaLabel(
                                    item.uuid,
                                    e.target.value,
                                  )
                                }
                                className="h-8"
                              />
                            </div>
                            <Input
                              type="number"
                              min={0}
                              value={item.position}
                              onChange={(e) =>
                                handleUpdateMediaPosition(
                                  item.uuid,
                                  Number(e.target.value),
                                )
                              }
                              className="h-8 w-20"
                            />
                            <Button
                              type="button"
                              variant={item.is_main ? "default" : "ghost"}
                              size="icon"
                              onClick={() => handleSetMainMedia(item.uuid)}
                              title={
                                item.is_main
                                  ? "Main image"
                                  : "Set as main image"
                              }
                            >
                              <Star
                                className={`h-4 w-4 ${item.is_main ? "fill-current" : ""}`}
                              />
                            </Button>
                            <Button
                              type="button"
                              variant="ghost"
                              size="icon"
                              onClick={() => handleRemoveMedia(item.uuid)}
                            >
                              <X className="h-4 w-4 text-destructive" />
                            </Button>
                          </div>
                        ))}
                    </div>
                  )}
                </CardContent>
              </Card>
            </TabsContent>

            <TabsContent value="categories">
              <Card>
                <CardHeader>
                  <CardTitle>Categories</CardTitle>
                  <CardDescription>
                    Select the categories this product belongs to
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <CategoryTreeSelect
                    selectedUuids={categoryUuids}
                    onChange={setCategoryUuids}
                  />
                </CardContent>
              </Card>
            </TabsContent>
          </Tabs>

          <div className="mt-6 flex gap-2">
            <Button type="submit" disabled={isSubmitting}>
              {isSubmitting ? "Saving..." : "Save Product"}
            </Button>
            <Button
              type="button"
              variant="outline"
              onClick={() => navigate("/catalog/products")}
            >
              Cancel
            </Button>
          </div>
        </form>
      </Form>
    </div>
  );
}
