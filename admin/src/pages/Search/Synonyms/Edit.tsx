import { useCallback, useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { SearchSynonym } from "@/types/search";
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

const synonymSchema = z.object({
  term: z.string().min(1, "Term is required").max(255),
  synonyms: z.string().min(1, "Synonyms are required"),
  store_view_id: z.coerce.number().int().min(0),
  is_active: z.boolean(),
});

type SynonymFormValues = z.infer<typeof synonymSchema>;

export function SearchSynonymsEdit() {
  const { uuid } = useParams<{ uuid: string }>();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const form = useForm<SynonymFormValues>({
    resolver: zodResolver(synonymSchema),
    defaultValues: {
      term: "",
      synonyms: "",
      store_view_id: 0,
      is_active: true,
    },
  });

  const loadSynonym = useCallback(async () => {
    try {
      const response = await api.get<{ data: SearchSynonym }>(
        `/admin/search/synonyms/${uuid}`,
      );
      const synonym = response.data.data;

      form.reset({
        term: synonym.term,
        synonyms: synonym.synonyms.join(", "),
        store_view_id: synonym.store_view_id,
        is_active: synonym.is_active,
      });
    } catch {
      toast.error("Failed to load search synonym");
      navigate("/search/synonyms");
    } finally {
      setIsLoading(false);
    }
  }, [uuid, form, navigate]);

  useEffect(() => {
    loadSynonym();
  }, [loadSynonym]);

  const handleSubmit = async (values: SynonymFormValues) => {
    setIsSubmitting(true);

    try {
      await api.put(`/admin/search/synonyms/${uuid}`, {
        ...values,
        synonyms: values.synonyms
          .split(",")
          .map((s) => s.trim())
          .filter(Boolean),
      });
      toast.success("Search synonym updated successfully");
      navigate("/search/synonyms");
    } catch {
      toast.error("Failed to update search synonym");
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
        title="Edit Search Synonym"
        description="Update search term synonym mapping"
      />

      <Form {...form}>
        <form
          onSubmit={form.handleSubmit(handleSubmit)}
          className="space-y-6"
        >
          <Card>
            <CardHeader>
              <CardTitle>Synonym Details</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <FormField
                control={form.control}
                name="term"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Term</FormLabel>
                    <FormControl>
                      <Input placeholder="sneakers" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <FormField
                control={form.control}
                name="synonyms"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Synonyms</FormLabel>
                    <FormControl>
                      <Input
                        placeholder="trainers, running shoes, kicks"
                        {...field}
                      />
                    </FormControl>
                    <FormDescription>
                      Enter comma-separated synonyms for the term
                    </FormDescription>
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
                      Use 0 for all store views
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
                        Enable this synonym mapping
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
              onClick={() => navigate("/search/synonyms")}
            >
              Cancel
            </Button>
          </div>
        </form>
      </Form>
    </div>
  );
}
