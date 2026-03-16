import { useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Loader2, Search, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { ConfigurationResponse, ConfigurationScope } from "@/types/store";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
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
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import { Badge } from "@/components/ui/badge";

const lookupSchema = z.object({
  path: z.string().min(1, "Path is required"),
  scope: z.enum(["global", "website", "store", "store_view"]),
  scope_id: z.coerce.number().int().min(0),
});

type LookupFormValues = z.infer<typeof lookupSchema>;

const editSchema = z.object({
  value: z.string(),
});

type EditFormValues = z.infer<typeof editSchema>;

const scopeOptions: { value: ConfigurationScope; label: string }[] = [
  { value: "global", label: "Global" },
  { value: "website", label: "Website" },
  { value: "store", label: "Store" },
  { value: "store_view", label: "Store View" },
];

export function ConfigurationIndex() {
  const [isLooking, setIsLooking] = useState(false);
  const [isSaving, setIsSaving] = useState(false);
  const [configResult, setConfigResult] = useState<ConfigurationResponse | null>(null);

  const lookupForm = useForm<LookupFormValues>({
    resolver: zodResolver(lookupSchema),
    defaultValues: {
      path: "",
      scope: "global",
      scope_id: 0,
    },
  });

  const editForm = useForm<EditFormValues>({
    resolver: zodResolver(editSchema),
    defaultValues: {
      value: "",
    },
  });

  const handleLookup = async (values: LookupFormValues) => {
    setIsLooking(true);
    setConfigResult(null);

    try {
      const response = await api.get<ConfigurationResponse>("/admin/store/config", {
        params: {
          path: values.path,
          scope: values.scope,
          scope_id: values.scope_id,
        },
      });

      setConfigResult(response.data);
      editForm.reset({ value: response.data.value ?? "" });
    } catch {
      toast.error("Failed to load configuration value");
    } finally {
      setIsLooking(false);
    }
  };

  const handleSave = async (values: EditFormValues) => {
    if (!configResult) return;

    setIsSaving(true);

    try {
      const response = await api.put<ConfigurationResponse>("/admin/store/config", {
        path: configResult.path,
        value: values.value || null,
        scope: configResult.scope,
        scope_id: configResult.scope_id,
      });

      setConfigResult(response.data);
      toast.success("Configuration saved successfully");
    } catch {
      toast.error("Failed to save configuration value");
    } finally {
      setIsSaving(false);
    }
  };

  const handleDelete = async () => {
    if (!configResult) return;

    try {
      await api.delete("/admin/store/config", {
        data: {
          path: configResult.path,
          scope: configResult.scope,
          scope_id: configResult.scope_id,
        },
      });

      setConfigResult(null);
      editForm.reset({ value: "" });
      toast.success("Configuration value deleted successfully");
    } catch {
      toast.error("Failed to delete configuration value");
    }
  };

  return (
    <div className="space-y-4">
      <PageHeader
        title="Configuration"
        description="View and edit store configuration values by path"
      />

      <Card>
        <CardHeader>
          <CardTitle>Lookup Configuration</CardTitle>
        </CardHeader>
        <CardContent>
          <Form {...lookupForm}>
            <form
              onSubmit={lookupForm.handleSubmit(handleLookup)}
              className="space-y-4"
            >
              <FormField
                control={lookupForm.control}
                name="path"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Configuration Path</FormLabel>
                    <FormControl>
                      <Input placeholder="general/store_information/name" {...field} />
                    </FormControl>
                    <FormDescription>
                      Dot or slash separated path to the configuration value
                    </FormDescription>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <div className="grid gap-4 sm:grid-cols-2">
                <FormField
                  control={lookupForm.control}
                  name="scope"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Scope</FormLabel>
                      <Select onValueChange={field.onChange} value={field.value}>
                        <FormControl>
                          <SelectTrigger>
                            <SelectValue placeholder="Select scope" />
                          </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                          {scopeOptions.map((option) => (
                            <SelectItem key={option.value} value={option.value}>
                              {option.label}
                            </SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                      <FormDescription>
                        Scope level for the configuration
                      </FormDescription>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={lookupForm.control}
                  name="scope_id"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Scope ID</FormLabel>
                      <FormControl>
                        <Input type="number" min={0} {...field} />
                      </FormControl>
                      <FormDescription>
                        ID of the scope entity (0 for global)
                      </FormDescription>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>
              <Button type="submit" disabled={isLooking}>
                {isLooking ? (
                  <>
                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                    Looking up...
                  </>
                ) : (
                  <>
                    <Search className="mr-2 h-4 w-4" />
                    Lookup
                  </>
                )}
              </Button>
            </form>
          </Form>
        </CardContent>
      </Card>

      {configResult !== null && (
        <Card>
          <CardHeader>
            <div className="flex items-start justify-between">
              <div className="space-y-1">
                <CardTitle>Edit Value</CardTitle>
                <div className="flex items-center gap-2 text-sm text-muted-foreground">
                  <code className="rounded bg-muted px-1 py-0.5 text-xs">
                    {configResult.path}
                  </code>
                  <Badge variant="outline">{configResult.scope}</Badge>
                  {configResult.scope_id > 0 && (
                    <span>ID: {configResult.scope_id}</span>
                  )}
                </div>
              </div>
              <AlertDialog>
                <AlertDialogTrigger asChild>
                  <Button variant="ghost" size="icon">
                    <Trash2 className="h-4 w-4 text-destructive" />
                  </Button>
                </AlertDialogTrigger>
                <AlertDialogContent>
                  <AlertDialogHeader>
                    <AlertDialogTitle>Delete Configuration Value</AlertDialogTitle>
                    <AlertDialogDescription>
                      Are you sure you want to delete the configuration value for
                      "{configResult.path}"? This will revert to the inherited or
                      default value.
                    </AlertDialogDescription>
                  </AlertDialogHeader>
                  <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction onClick={handleDelete}>
                      Delete
                    </AlertDialogAction>
                  </AlertDialogFooter>
                </AlertDialogContent>
              </AlertDialog>
            </div>
          </CardHeader>
          <CardContent>
            <Form {...editForm}>
              <form
                onSubmit={editForm.handleSubmit(handleSave)}
                className="space-y-4"
              >
                <FormField
                  control={editForm.control}
                  name="value"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Value</FormLabel>
                      <FormControl>
                        <Input placeholder="Enter value..." {...field} />
                      </FormControl>
                      <FormDescription>
                        Leave empty to store a null value
                      </FormDescription>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <Button type="submit" disabled={isSaving}>
                  {isSaving ? (
                    <>
                      <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                      Saving...
                    </>
                  ) : (
                    "Save Value"
                  )}
                </Button>
              </form>
            </Form>
          </CardContent>
        </Card>
      )}
    </div>
  );
}
