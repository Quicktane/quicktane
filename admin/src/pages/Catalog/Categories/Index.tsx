import { useCallback, useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import {
  ChevronDown,
  ChevronRight,
  FolderTree,
  Pencil,
  Plus,
  Trash2,
} from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import { cn } from "@/lib/utils";
import type { Category } from "@/types/catalog";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
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
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
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
import { Skeleton } from "@/components/ui/skeleton";

const categorySchema = z.object({
  name: z.string().min(1, "Name is required"),
  slug: z.string().min(1, "Slug is required"),
  description: z.string().optional(),
  parent_uuid: z.string().optional(),
  position: z.coerce.number().int().min(0),
  is_active: z.boolean(),
  include_in_menu: z.boolean(),
  meta_title: z.string().optional(),
  meta_description: z.string().optional(),
});

type CategoryFormValues = z.infer<typeof categorySchema>;

function generateSlug(name: string): string {
  return name
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-|-$/g, "");
}

function flattenCategories(
  categories: Category[],
  excludeUuid?: string,
): { uuid: string; name: string; level: number }[] {
  const result: { uuid: string; name: string; level: number }[] = [];

  function walk(items: Category[]) {
    for (const category of items) {
      if (category.uuid === excludeUuid) continue;
      result.push({
        uuid: category.uuid,
        name: "\u00A0\u00A0".repeat(category.level) + category.name,
        level: category.level,
      });
      if (category.children) {
        walk(category.children);
      }
    }
  }

  walk(categories);
  return result;
}

interface TreeNodeProps {
  category: Category;
  selectedUuid: string | null;
  onSelect: (category: Category) => void;
  level: number;
}

function TreeNode({
  category,
  selectedUuid,
  onSelect,
  level,
}: TreeNodeProps) {
  const [expanded, setExpanded] = useState(level < 2);
  const hasChildren = category.children && category.children.length > 0;

  return (
    <div>
      <div
        className={cn(
          "flex cursor-pointer items-center gap-1 rounded-md px-2 py-1.5 text-sm hover:bg-muted",
          selectedUuid === category.uuid && "bg-muted font-medium",
        )}
        style={{ paddingLeft: `${level * 16 + 8}px` }}
      >
        <button
          type="button"
          className="flex h-4 w-4 shrink-0 items-center justify-center"
          onClick={() => hasChildren && setExpanded(!expanded)}
        >
          {hasChildren &&
            (expanded ? (
              <ChevronDown className="h-3 w-3" />
            ) : (
              <ChevronRight className="h-3 w-3" />
            ))}
        </button>
        <button
          type="button"
          className="flex-1 text-left"
          onClick={() => onSelect(category)}
        >
          {category.name}
          {!category.is_active && (
            <span className="ml-2 text-xs text-muted-foreground">
              (inactive)
            </span>
          )}
        </button>
      </div>
      {expanded &&
        hasChildren &&
        category.children?.map((child) => (
          <TreeNode
            key={child.uuid}
            category={child}
            selectedUuid={selectedUuid}
            onSelect={onSelect}
            level={level + 1}
          />
        ))}
    </div>
  );
}

export function CategoriesIndex() {
  const [categories, setCategories] = useState<Category[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [selectedCategory, setSelectedCategory] = useState<Category | null>(
    null,
  );
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isDeleting, setIsDeleting] = useState(false);
  const [createDialogOpen, setCreateDialogOpen] = useState(false);
  const [isCreating, setIsCreating] = useState(false);
  const [moveParentUuid, setMoveParentUuid] = useState<string>("");
  const [isMoving, setIsMoving] = useState(false);

  const editForm = useForm<CategoryFormValues>({
    resolver: zodResolver(categorySchema),
    defaultValues: {
      name: "",
      slug: "",
      description: "",
      position: 0,
      is_active: true,
      include_in_menu: true,
      meta_title: "",
      meta_description: "",
    },
  });

  const createForm = useForm<CategoryFormValues>({
    resolver: zodResolver(categorySchema),
    defaultValues: {
      name: "",
      slug: "",
      description: "",
      parent_uuid: "",
      position: 0,
      is_active: true,
      include_in_menu: true,
      meta_title: "",
      meta_description: "",
    },
  });

  const watchedCreateName = createForm.watch("name");

  useEffect(() => {
    if (watchedCreateName) {
      createForm.setValue("slug", generateSlug(watchedCreateName));
    }
  }, [watchedCreateName, createForm]);

  const loadCategories = useCallback(async () => {
    try {
      const response = await api.get<{ data: Category[] }>(
        "/admin/catalog/categories",
      );
      setCategories(response.data.data);
    } catch {
      toast.error("Failed to load categories");
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadCategories();
  }, [loadCategories]);

  const handleSelectCategory = (category: Category) => {
    setSelectedCategory(category);

    editForm.reset({
      name: category.name,
      slug: category.slug,
      description: category.description ?? "",
      position: category.position,
      is_active: category.is_active,
      include_in_menu: category.include_in_menu,
      meta_title: category.meta_title ?? "",
      meta_description: category.meta_description ?? "",
    });

    setMoveParentUuid("");
  };

  const handleUpdateCategory = async (values: CategoryFormValues) => {
    if (!selectedCategory) return;
    setIsSubmitting(true);

    try {
      await api.put(
        `/admin/catalog/categories/${selectedCategory.uuid}`,
        values,
      );

      toast.success("Category updated successfully");
      await loadCategories();
    } catch (error: unknown) {
      const axiosError = error as { response?: { data?: { message?: string } } };
      toast.error(
        axiosError.response?.data?.message ?? "Failed to update category",
      );
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleDeleteCategory = async () => {
    if (!selectedCategory) return;
    setIsDeleting(true);

    try {
      await api.delete(
        `/admin/catalog/categories/${selectedCategory.uuid}`,
      );

      toast.success("Category deleted successfully");
      setSelectedCategory(null);
      await loadCategories();
    } catch {
      toast.error("Failed to delete category");
    } finally {
      setIsDeleting(false);
    }
  };

  const handleCreateCategory = async (values: CategoryFormValues) => {
    setIsCreating(true);

    try {
      const payload = {
        ...values,
        parent_uuid: values.parent_uuid || null,
      };

      await api.post("/admin/catalog/categories", payload);
      toast.success("Category created successfully");
      setCreateDialogOpen(false);
      createForm.reset();
      await loadCategories();
    } catch (error: unknown) {
      const axiosError = error as { response?: { data?: { message?: string } } };
      toast.error(
        axiosError.response?.data?.message ?? "Failed to create category",
      );
    } finally {
      setIsCreating(false);
    }
  };

  const handleMoveCategory = async () => {
    if (!selectedCategory) return;
    setIsMoving(true);

    try {
      await api.put(
        `/admin/catalog/categories/${selectedCategory.uuid}/move`,
        { parent_uuid: moveParentUuid || null },
      );

      toast.success("Category moved successfully");
      await loadCategories();
    } catch (error: unknown) {
      const axiosError = error as { response?: { data?: { message?: string } } };
      toast.error(
        axiosError.response?.data?.message ?? "Failed to move category",
      );
    } finally {
      setIsMoving(false);
    }
  };

  const flatCategoriesForCreate = flattenCategories(categories);
  const flatCategoriesForMove = selectedCategory
    ? flattenCategories(categories, selectedCategory.uuid)
    : [];

  if (isLoading) {
    return (
      <div className="space-y-4">
        <Skeleton className="h-8 w-48" />
        <div className="grid grid-cols-3 gap-4">
          <Skeleton className="h-96" />
          <Skeleton className="col-span-2 h-96" />
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <PageHeader
        title="Categories"
        description="Manage product categories"
        actions={
          <Dialog open={createDialogOpen} onOpenChange={setCreateDialogOpen}>
            <DialogTrigger asChild>
              <Button>
                <Plus className="mr-2 h-4 w-4" />
                New Category
              </Button>
            </DialogTrigger>
            <DialogContent className="max-w-md">
              <DialogHeader>
                <DialogTitle>Create Category</DialogTitle>
              </DialogHeader>
              <Form {...createForm}>
                <form
                  onSubmit={createForm.handleSubmit(handleCreateCategory)}
                  className="space-y-4"
                >
                  <FormField
                    control={createForm.control}
                    name="parent_uuid"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Parent Category</FormLabel>
                        <Select
                          onValueChange={field.onChange}
                          value={field.value}
                        >
                          <FormControl>
                            <SelectTrigger>
                              <SelectValue placeholder="Root (no parent)" />
                            </SelectTrigger>
                          </FormControl>
                          <SelectContent>
                            <SelectItem value="none">
                              Root (no parent)
                            </SelectItem>
                            {flatCategoriesForCreate.map((category) => (
                              <SelectItem
                                key={category.uuid}
                                value={category.uuid}
                              >
                                {category.name}
                              </SelectItem>
                            ))}
                          </SelectContent>
                        </Select>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  <FormField
                    control={createForm.control}
                    name="name"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Name</FormLabel>
                        <FormControl>
                          <Input placeholder="Category name" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  <FormField
                    control={createForm.control}
                    name="slug"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Slug</FormLabel>
                        <FormControl>
                          <Input placeholder="category-slug" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  <FormField
                    control={createForm.control}
                    name="position"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Position</FormLabel>
                        <FormControl>
                          <Input
                            type="number"
                            min={0}
                            {...field}
                            onChange={(e) => field.onChange(Number(e.target.value))}
                          />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  <div className="flex gap-4">
                    <FormField
                      control={createForm.control}
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
                    <FormField
                      control={createForm.control}
                      name="include_in_menu"
                      render={({ field }) => (
                        <FormItem className="flex items-center gap-2 space-y-0">
                          <FormControl>
                            <Switch
                              checked={field.value}
                              onCheckedChange={field.onChange}
                            />
                          </FormControl>
                          <FormLabel>In Menu</FormLabel>
                        </FormItem>
                      )}
                    />
                  </div>
                  <div className="flex justify-end gap-2">
                    <Button
                      type="button"
                      variant="outline"
                      onClick={() => setCreateDialogOpen(false)}
                    >
                      Cancel
                    </Button>
                    <Button type="submit" disabled={isCreating}>
                      {isCreating ? "Creating..." : "Create"}
                    </Button>
                  </div>
                </form>
              </Form>
            </DialogContent>
          </Dialog>
        }
      />

      <div className="grid grid-cols-3 gap-4">
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2 text-base">
              <FolderTree className="h-4 w-4" />
              Category Tree
            </CardTitle>
          </CardHeader>
          <CardContent>
            {categories.length === 0 ? (
              <p className="py-4 text-center text-sm text-muted-foreground">
                No categories yet.
              </p>
            ) : (
              <div className="max-h-[600px] overflow-y-auto">
                {categories.map((category) => (
                  <TreeNode
                    key={category.uuid}
                    category={category}
                    selectedUuid={selectedCategory?.uuid ?? null}
                    onSelect={handleSelectCategory}
                    level={0}
                  />
                ))}
              </div>
            )}
          </CardContent>
        </Card>

        <div className="col-span-2 space-y-4">
          {selectedCategory ? (
            <>
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2 text-base">
                    <Pencil className="h-4 w-4" />
                    Edit: {selectedCategory.name}
                  </CardTitle>
                  <CardDescription>
                    Update category details
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <Form {...editForm}>
                    <form
                      onSubmit={editForm.handleSubmit(handleUpdateCategory)}
                      className="space-y-4"
                    >
                      <div className="grid grid-cols-2 gap-4">
                        <FormField
                          control={editForm.control}
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
                        <FormField
                          control={editForm.control}
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
                      </div>
                      <FormField
                        control={editForm.control}
                        name="description"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Description</FormLabel>
                            <FormControl>
                              <Textarea rows={3} {...field} />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                      <FormField
                        control={editForm.control}
                        name="position"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Position</FormLabel>
                            <FormControl>
                              <Input
                                type="number"
                                min={0}
                                className="w-32"
                                {...field}
                                onChange={(e) => field.onChange(Number(e.target.value))}
                              />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                      <div className="flex gap-8">
                        <FormField
                          control={editForm.control}
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
                        <FormField
                          control={editForm.control}
                          name="include_in_menu"
                          render={({ field }) => (
                            <FormItem className="flex items-center gap-2 space-y-0">
                              <FormControl>
                                <Switch
                                  checked={field.value}
                                  onCheckedChange={field.onChange}
                                />
                              </FormControl>
                              <FormLabel>Include in Menu</FormLabel>
                            </FormItem>
                          )}
                        />
                      </div>
                      <div className="grid grid-cols-2 gap-4">
                        <FormField
                          control={editForm.control}
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
                          control={editForm.control}
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
                      <div className="flex gap-2">
                        <Button type="submit" disabled={isSubmitting}>
                          {isSubmitting ? "Saving..." : "Save Changes"}
                        </Button>
                        <AlertDialog>
                          <AlertDialogTrigger asChild>
                            <Button type="button" variant="destructive">
                              <Trash2 className="mr-2 h-4 w-4" />
                              Delete
                            </Button>
                          </AlertDialogTrigger>
                          <AlertDialogContent>
                            <AlertDialogHeader>
                              <AlertDialogTitle>
                                Delete Category
                              </AlertDialogTitle>
                              <AlertDialogDescription>
                                Are you sure you want to delete "
                                {selectedCategory.name}"? This will also remove
                                all child categories.
                              </AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter>
                              <AlertDialogCancel>Cancel</AlertDialogCancel>
                              <AlertDialogAction
                                onClick={handleDeleteCategory}
                                disabled={isDeleting}
                              >
                                {isDeleting ? "Deleting..." : "Delete"}
                              </AlertDialogAction>
                            </AlertDialogFooter>
                          </AlertDialogContent>
                        </AlertDialog>
                      </div>
                    </form>
                  </Form>
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle className="text-base">Move Category</CardTitle>
                  <CardDescription>
                    Change the parent of this category
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="flex items-end gap-3">
                    <div className="flex-1">
                      <label className="mb-2 block text-sm font-medium">
                        New Parent
                      </label>
                      <Select
                        value={moveParentUuid}
                        onValueChange={setMoveParentUuid}
                      >
                        <SelectTrigger>
                          <SelectValue placeholder="Root (no parent)" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem value="none">
                            Root (no parent)
                          </SelectItem>
                          {flatCategoriesForMove.map((category) => (
                            <SelectItem
                              key={category.uuid}
                              value={category.uuid}
                            >
                              {category.name}
                            </SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                    </div>
                    <Button
                      type="button"
                      onClick={handleMoveCategory}
                      disabled={isMoving}
                    >
                      {isMoving ? "Moving..." : "Move"}
                    </Button>
                  </div>
                </CardContent>
              </Card>
            </>
          ) : (
            <Card>
              <CardContent className="flex h-96 items-center justify-center">
                <p className="text-sm text-muted-foreground">
                  Select a category from the tree to edit it.
                </p>
              </CardContent>
            </Card>
          )}
        </div>
      </div>
    </div>
  );
}
