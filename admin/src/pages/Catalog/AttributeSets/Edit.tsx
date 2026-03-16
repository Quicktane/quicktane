import { useCallback, useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { Attribute, AttributeSet, AttributeSetAttribute } from "@/types/catalog";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
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
import { Separator } from "@/components/ui/separator";
import { Skeleton } from "@/components/ui/skeleton";

const attributeSetSchema = z.object({
  name: z.string().min(1, "Name is required"),
  sort_order: z.coerce.number().int().min(0),
});

type AttributeSetFormValues = z.infer<typeof attributeSetSchema>;

interface AssignedAttribute {
  uuid: string;
  code: string;
  name: string;
  type: string;
  group_name: string;
  sort_order: number;
}

export function AttributeSetsEdit() {
  const { uuid } = useParams<{ uuid: string }>();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isSyncing, setIsSyncing] = useState(false);
  const [allAttributes, setAllAttributes] = useState<Attribute[]>([]);
  const [assignedAttributes, setAssignedAttributes] = useState<
    AssignedAttribute[]
  >([]);
  const [selectedAttributeUuid, setSelectedAttributeUuid] = useState("");
  const [newGroupName, setNewGroupName] = useState("General");
  const [newSortOrder, setNewSortOrder] = useState(0);

  const form = useForm<AttributeSetFormValues>({
    resolver: zodResolver(attributeSetSchema),
    defaultValues: {
      name: "",
      sort_order: 0,
    },
  });

  const loadData = useCallback(async () => {
    try {
      const [attributeSetResponse, attributesResponse] = await Promise.all([
        api.get<{ data: AttributeSet }>(`/admin/catalog/attribute-sets/${uuid}`),
        api.get<{ data: Attribute[] }>("/admin/catalog/attributes"),
      ]);

      const attributeSet = attributeSetResponse.data.data;
      setAllAttributes(attributesResponse.data.data);

      form.reset({
        name: attributeSet.name,
        sort_order: attributeSet.sort_order,
      });

      if (attributeSet.attributes) {
        setAssignedAttributes(
          attributeSet.attributes.map((attribute: AttributeSetAttribute) => ({
            uuid: attribute.uuid,
            code: attribute.code,
            name: attribute.name,
            type: attribute.type,
            group_name: attribute.pivot.group_name,
            sort_order: attribute.pivot.sort_order,
          })),
        );
      }
    } catch {
      toast.error("Failed to load attribute set");
      navigate("/catalog/attribute-sets");
    } finally {
      setIsLoading(false);
    }
  }, [uuid, form, navigate]);

  useEffect(() => {
    loadData();
  }, [loadData]);

  const onSubmit = async (values: AttributeSetFormValues) => {
    setIsSubmitting(true);

    try {
      await api.put(`/admin/catalog/attribute-sets/${uuid}`, values);
      toast.success("Attribute set updated successfully");
    } catch (error: unknown) {
      const axiosError = error as { response?: { data?: { message?: string } } };
      toast.error(
        axiosError.response?.data?.message ?? "Failed to update attribute set",
      );
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleAddAttribute = () => {
    if (!selectedAttributeUuid) return;

    const attribute = allAttributes.find(
      (a) => a.uuid === selectedAttributeUuid,
    );
    if (!attribute) return;

    if (assignedAttributes.some((a) => a.uuid === attribute.uuid)) {
      toast.error("Attribute is already assigned");
      return;
    }

    setAssignedAttributes((prev) => [
      ...prev,
      {
        uuid: attribute.uuid,
        code: attribute.code,
        name: attribute.name,
        type: attribute.type,
        group_name: newGroupName,
        sort_order: newSortOrder,
      },
    ]);

    setSelectedAttributeUuid("");
    setNewSortOrder((prev) => prev + 1);
  };

  const handleRemoveAttribute = (attributeUuid: string) => {
    setAssignedAttributes((prev) =>
      prev.filter((a) => a.uuid !== attributeUuid),
    );
  };

  const handleUpdateGroupName = (attributeUuid: string, groupName: string) => {
    setAssignedAttributes((prev) =>
      prev.map((a) =>
        a.uuid === attributeUuid ? { ...a, group_name: groupName } : a,
      ),
    );
  };

  const handleUpdateSortOrder = (
    attributeUuid: string,
    sortOrder: number,
  ) => {
    setAssignedAttributes((prev) =>
      prev.map((a) =>
        a.uuid === attributeUuid ? { ...a, sort_order: sortOrder } : a,
      ),
    );
  };

  const handleSyncAttributes = async () => {
    setIsSyncing(true);

    try {
      await api.put(`/admin/catalog/attribute-sets/${uuid}/sync-attributes`, {
        attributes: assignedAttributes.map((a) => ({
          uuid: a.uuid,
          group_name: a.group_name,
          sort_order: a.sort_order,
        })),
      });

      toast.success("Attributes synced successfully");
    } catch (error: unknown) {
      const axiosError = error as { response?: { data?: { message?: string } } };
      toast.error(
        axiosError.response?.data?.message ?? "Failed to sync attributes",
      );
    } finally {
      setIsSyncing(false);
    }
  };

  const availableAttributes = allAttributes.filter(
    (a) => !assignedAttributes.some((assigned) => assigned.uuid === a.uuid),
  );

  const groupedAttributes = assignedAttributes.reduce<
    Record<string, AssignedAttribute[]>
  >((groups, attribute) => {
    const group = attribute.group_name || "General";
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
        <Skeleton className="h-64 w-full" />
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <PageHeader title="Edit Attribute Set" />

      <Form {...form}>
        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle>General</CardTitle>
              <CardDescription>
                Basic attribute set configuration
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
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
              <Button type="submit" disabled={isSubmitting}>
                {isSubmitting ? "Saving..." : "Save"}
              </Button>
            </CardContent>
          </Card>
        </form>
      </Form>

      <Card>
        <CardHeader>
          <CardTitle>Attributes</CardTitle>
          <CardDescription>
            Manage which attributes belong to this set
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-6">
          <div className="flex items-end gap-3">
            <div className="flex-1">
              <label className="mb-2 block text-sm font-medium">
                Add Attribute
              </label>
              <Select
                value={selectedAttributeUuid}
                onValueChange={setSelectedAttributeUuid}
              >
                <SelectTrigger>
                  <SelectValue placeholder="Select an attribute" />
                </SelectTrigger>
                <SelectContent>
                  {availableAttributes.map((attribute) => (
                    <SelectItem key={attribute.uuid} value={attribute.uuid}>
                      {attribute.name} ({attribute.code})
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            <div className="w-40">
              <label className="mb-2 block text-sm font-medium">Group</label>
              <Input
                value={newGroupName}
                onChange={(e) => setNewGroupName(e.target.value)}
                placeholder="Group name"
              />
            </div>
            <div className="w-24">
              <label className="mb-2 block text-sm font-medium">Order</label>
              <Input
                type="number"
                min={0}
                value={newSortOrder}
                onChange={(e) => setNewSortOrder(Number(e.target.value))}
              />
            </div>
            <Button
              type="button"
              variant="outline"
              onClick={handleAddAttribute}
              disabled={!selectedAttributeUuid}
            >
              <Plus className="mr-2 h-4 w-4" />
              Add
            </Button>
          </div>

          <Separator />

          {Object.keys(groupedAttributes).length === 0 ? (
            <p className="py-4 text-center text-sm text-muted-foreground">
              No attributes assigned yet.
            </p>
          ) : (
            Object.entries(groupedAttributes)
              .sort(([a], [b]) => a.localeCompare(b))
              .map(([groupName, attributes]) => (
                <div key={groupName} className="space-y-2">
                  <h4 className="text-sm font-semibold text-muted-foreground">
                    {groupName}
                  </h4>
                  <div className="space-y-1">
                    {attributes
                      .sort((a, b) => a.sort_order - b.sort_order)
                      .map((attribute) => (
                        <div
                          key={attribute.uuid}
                          className="flex items-center gap-3 rounded-md border p-2"
                        >
                          <div className="flex-1">
                            <span className="text-sm font-medium">
                              {attribute.name}
                            </span>
                            <span className="ml-2 text-xs text-muted-foreground">
                              ({attribute.code} - {attribute.type})
                            </span>
                          </div>
                          <Input
                            className="w-32"
                            value={attribute.group_name}
                            onChange={(e) =>
                              handleUpdateGroupName(
                                attribute.uuid,
                                e.target.value,
                              )
                            }
                            placeholder="Group"
                          />
                          <Input
                            className="w-20"
                            type="number"
                            min={0}
                            value={attribute.sort_order}
                            onChange={(e) =>
                              handleUpdateSortOrder(
                                attribute.uuid,
                                Number(e.target.value),
                              )
                            }
                          />
                          <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            onClick={() =>
                              handleRemoveAttribute(attribute.uuid)
                            }
                          >
                            <Trash2 className="h-4 w-4 text-destructive" />
                          </Button>
                        </div>
                      ))}
                  </div>
                </div>
              ))
          )}

          <div className="flex gap-2">
            <Button
              type="button"
              onClick={handleSyncAttributes}
              disabled={isSyncing}
            >
              {isSyncing ? "Syncing..." : "Sync Attributes"}
            </Button>
            <Button
              type="button"
              variant="outline"
              onClick={() => navigate("/catalog/attribute-sets")}
            >
              Back to List
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
