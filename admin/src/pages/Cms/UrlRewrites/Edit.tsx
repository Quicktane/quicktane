import { useCallback, useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { UrlRewrite } from "@/types/cms";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
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

const urlRewriteSchema = z.object({
  request_path: z.string().min(1, "Request path is required").max(255),
  target_path: z.string().min(1, "Target path is required").max(255),
  entity_type: z.string().min(1, "Entity type is required"),
  entity_id: z.coerce.number().int().optional(),
  redirect_type: z.coerce.number().int().nullable(),
  store_view_id: z.coerce.number().int().min(0),
  description: z.string().max(500).optional(),
});

type UrlRewriteFormValues = z.infer<typeof urlRewriteSchema>;

export function CmsUrlRewritesEdit() {
  const { uuid } = useParams<{ uuid: string }>();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const form = useForm<UrlRewriteFormValues>({
    resolver: zodResolver(urlRewriteSchema),
    defaultValues: {
      request_path: "",
      target_path: "",
      entity_type: "custom",
      entity_id: undefined,
      redirect_type: null,
      store_view_id: 0,
      description: "",
    },
  });

  const loadUrlRewrite = useCallback(async () => {
    try {
      const response = await api.get<{ data: UrlRewrite }>(
        `/admin/cms/url-rewrites/${uuid}`,
      );
      const urlRewrite = response.data.data;

      form.reset({
        request_path: urlRewrite.request_path,
        target_path: urlRewrite.target_path,
        entity_type: urlRewrite.entity_type,
        entity_id: urlRewrite.entity_id ?? undefined,
        redirect_type: urlRewrite.redirect_type,
        store_view_id: urlRewrite.store_view_id,
        description: urlRewrite.description ?? "",
      });
    } catch {
      toast.error("Failed to load URL rewrite");
      navigate("/cms/url-rewrites");
    } finally {
      setIsLoading(false);
    }
  }, [uuid, form, navigate]);

  useEffect(() => {
    loadUrlRewrite();
  }, [loadUrlRewrite]);

  const handleSubmit = async (values: UrlRewriteFormValues) => {
    setIsSubmitting(true);

    try {
      await api.put(`/admin/cms/url-rewrites/${uuid}`, values);
      toast.success("URL rewrite updated successfully");
      navigate("/cms/url-rewrites");
    } catch {
      toast.error("Failed to update URL rewrite");
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
        title="Edit URL Rewrite"
        description="Update URL rewrite details"
      />

      <Form {...form}>
        <form
          onSubmit={form.handleSubmit(handleSubmit)}
          className="space-y-6"
        >
          <Card>
            <CardHeader>
              <CardTitle>Rewrite Details</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <FormField
                  control={form.control}
                  name="request_path"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Request Path</FormLabel>
                      <FormControl>
                        <Input placeholder="old-page.html" {...field} />
                      </FormControl>
                      <FormDescription>
                        The URL path that visitors will request
                      </FormDescription>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="target_path"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Target Path</FormLabel>
                      <FormControl>
                        <Input placeholder="new-page" {...field} />
                      </FormControl>
                      <FormDescription>
                        The URL path to redirect or rewrite to
                      </FormDescription>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>
              <div className="grid gap-4 sm:grid-cols-2">
                <FormField
                  control={form.control}
                  name="entity_type"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Entity Type</FormLabel>
                      <Select
                        value={field.value}
                        onValueChange={field.onChange}
                      >
                        <FormControl>
                          <SelectTrigger>
                            <SelectValue placeholder="Select entity type" />
                          </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                          <SelectItem value="custom">Custom</SelectItem>
                          <SelectItem value="product">Product</SelectItem>
                          <SelectItem value="category">Category</SelectItem>
                          <SelectItem value="cms-page">CMS Page</SelectItem>
                        </SelectContent>
                      </Select>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="entity_id"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Entity ID</FormLabel>
                      <FormControl>
                        <Input
                          type="number"
                          min={0}
                          placeholder="Optional"
                          {...field}
                          value={field.value ?? ""}
                        />
                      </FormControl>
                      <FormDescription>
                        Associated entity ID (if applicable)
                      </FormDescription>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>
              <div className="grid gap-4 sm:grid-cols-2">
                <FormField
                  control={form.control}
                  name="redirect_type"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Redirect Type</FormLabel>
                      <Select
                        value={field.value !== null ? String(field.value) : "0"}
                        onValueChange={(value) =>
                          field.onChange(value === "0" ? null : Number(value))
                        }
                      >
                        <FormControl>
                          <SelectTrigger>
                            <SelectValue placeholder="Select redirect type" />
                          </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                          <SelectItem value="0">Internal (No Redirect)</SelectItem>
                          <SelectItem value="301">301 Permanent</SelectItem>
                          <SelectItem value="302">302 Temporary</SelectItem>
                        </SelectContent>
                      </Select>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="store_view_id"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Store View ID</FormLabel>
                      <FormControl>
                        <Input type="number" min={0} {...field} />
                      </FormControl>
                      <FormDescription>
                        0 for all store views
                      </FormDescription>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>
              <FormField
                control={form.control}
                name="description"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Description</FormLabel>
                    <FormControl>
                      <Textarea
                        placeholder="Optional description for this rewrite"
                        {...field}
                      />
                    </FormControl>
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
              onClick={() => navigate("/cms/url-rewrites")}
            >
              Cancel
            </Button>
          </div>
        </form>
      </Form>
    </div>
  );
}
