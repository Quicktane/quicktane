import { useCallback, useEffect, useState } from "react";
import { useNavigate } from "react-router";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { Permission } from "@/types/auth";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Checkbox } from "@/components/ui/checkbox";
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

const roleSchema = z.object({
  name: z.string().min(1, "Name is required").max(255),
  slug: z
    .string()
    .min(1, "Slug is required")
    .max(255)
    .regex(
      /^[a-z0-9_-]+$/,
      "Slug must contain only lowercase letters, numbers, hyphens, and underscores",
    ),
  description: z.string().optional().or(z.literal("")),
  permissions: z.array(z.number()),
});

type RoleFormValues = z.infer<typeof roleSchema>;

interface GroupedPermissions {
  [module: string]: {
    [groupName: string]: Permission[];
  };
}

function generateSlug(name: string): string {
  return name
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-|-$/g, "");
}

function groupPermissions(permissions: Permission[]): GroupedPermissions {
  return permissions.reduce<GroupedPermissions>((groups, permission) => {
    const module = permission.module;
    const groupName = permission.group_name;

    if (!groups[module]) {
      groups[module] = {};
    }

    if (!groups[module][groupName]) {
      groups[module][groupName] = [];
    }

    groups[module][groupName].push(permission);
    return groups;
  }, {});
}

export function RolesCreate() {
  const navigate = useNavigate();
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [allPermissions, setAllPermissions] = useState<Permission[]>([]);
  const [permissionsLoading, setPermissionsLoading] = useState(true);

  const form = useForm<RoleFormValues>({
    resolver: zodResolver(roleSchema),
    defaultValues: {
      name: "",
      slug: "",
      description: "",
      permissions: [],
    },
  });

  const watchedName = form.watch("name");

  useEffect(() => {
    if (watchedName && !form.getFieldState("slug").isDirty) {
      form.setValue("slug", generateSlug(watchedName));
    }
  }, [watchedName, form]);

  const loadPermissions = useCallback(async () => {
    try {
      const response = await api.get<{ data: Permission[] }>(
        "/admin/user/permissions",
      );
      setAllPermissions(response.data.data);
    } catch {
      toast.error("Failed to load permissions");
    } finally {
      setPermissionsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadPermissions();
  }, [loadPermissions]);

  const handleSubmit = async (values: RoleFormValues) => {
    setIsSubmitting(true);

    try {
      await api.post("/admin/user/roles", {
        name: values.name,
        slug: values.slug,
        description: values.description || null,
        permissions: values.permissions,
      });
      toast.success("Role created successfully");
      navigate("/system/roles");
    } catch {
      toast.error("Failed to create role");
    } finally {
      setIsSubmitting(false);
    }
  };

  const groupedPermissions = groupPermissions(allPermissions);

  return (
    <div className="space-y-4">
      <PageHeader
        title="Create Role"
        description="Define a new admin panel role with specific permissions"
      />

      <Form {...form}>
        <form
          onSubmit={form.handleSubmit(handleSubmit)}
          className="space-y-6"
        >
          <Card>
            <CardHeader>
              <CardTitle>Role Details</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <FormField
                  control={form.control}
                  name="name"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Name</FormLabel>
                      <FormControl>
                        <Input placeholder="Store Manager" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="slug"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Slug</FormLabel>
                      <FormControl>
                        <Input placeholder="store-manager" {...field} />
                      </FormControl>
                      <FormDescription>
                        Unique identifier. Auto-generated from name.
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
                        placeholder="Optional description of this role..."
                        {...field}
                      />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Permissions</CardTitle>
            </CardHeader>
            <CardContent>
              {permissionsLoading ? (
                <div className="space-y-4">
                  <Skeleton className="h-6 w-32" />
                  <Skeleton className="h-20 w-full" />
                  <Skeleton className="h-6 w-32" />
                  <Skeleton className="h-20 w-full" />
                </div>
              ) : allPermissions.length === 0 ? (
                <p className="py-4 text-center text-sm text-muted-foreground">
                  No permissions available.
                </p>
              ) : (
                <FormField
                  control={form.control}
                  name="permissions"
                  render={({ field }) => (
                    <FormItem>
                      <div className="space-y-6">
                        {Object.entries(groupedPermissions)
                          .sort(([a], [b]) => a.localeCompare(b))
                          .map(([module, groups]) => (
                            <div key={module} className="space-y-3">
                              <h4 className="text-sm font-semibold capitalize text-foreground">
                                {module}
                              </h4>
                              <div className="space-y-4 pl-2">
                                {Object.entries(groups)
                                  .sort(([a], [b]) => a.localeCompare(b))
                                  .map(([groupName, permissions]) => (
                                    <div key={groupName} className="space-y-2">
                                      <p className="text-xs font-medium uppercase tracking-wide text-muted-foreground">
                                        {groupName}
                                      </p>
                                      <div className="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                        {permissions.map((permission) => (
                                          <label
                                            key={permission.id}
                                            className="flex cursor-pointer items-center gap-2 rounded-md border p-3 hover:bg-muted"
                                          >
                                            <FormControl>
                                              <Checkbox
                                                checked={field.value.includes(
                                                  permission.id,
                                                )}
                                                onCheckedChange={(checked) => {
                                                  if (checked) {
                                                    field.onChange([
                                                      ...field.value,
                                                      permission.id,
                                                    ]);
                                                  } else {
                                                    field.onChange(
                                                      field.value.filter(
                                                        (id) =>
                                                          id !== permission.id,
                                                      ),
                                                    );
                                                  }
                                                }}
                                              />
                                            </FormControl>
                                            <span className="text-sm">
                                              {permission.name}
                                            </span>
                                          </label>
                                        ))}
                                      </div>
                                    </div>
                                  ))}
                              </div>
                            </div>
                          ))}
                      </div>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              )}
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
                "Create Role"
              )}
            </Button>
            <Button
              type="button"
              variant="outline"
              onClick={() => navigate("/system/roles")}
            >
              Cancel
            </Button>
          </div>
        </form>
      </Form>
    </div>
  );
}
