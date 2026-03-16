import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef } from "@tanstack/react-table";
import { Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { UrlRewrite } from "@/types/cms";
import { PageHeader } from "@/components/PageHeader";
import { DataTable } from "@/components/data-table/DataTable";
import { DataTableColumnHeader } from "@/components/data-table/DataTableColumnHeader";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
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

function DeleteUrlRewriteButton({
  uuid,
  requestPath,
  onDeleted,
}: {
  uuid: string;
  requestPath: string;
  onDeleted: () => void;
}) {
  const [isDeleting, setIsDeleting] = useState(false);

  const handleDelete = async () => {
    setIsDeleting(true);

    try {
      await api.delete(`/admin/cms/url-rewrites/${uuid}`);
      toast.success(`URL rewrite "${requestPath}" deleted`);
      onDeleted();
    } catch {
      toast.error("Failed to delete URL rewrite");
    } finally {
      setIsDeleting(false);
    }
  };

  return (
    <AlertDialog>
      <AlertDialogTrigger asChild>
        <Button variant="ghost" size="icon">
          <Trash2 className="h-4 w-4 text-destructive" />
        </Button>
      </AlertDialogTrigger>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Delete URL Rewrite</AlertDialogTitle>
          <AlertDialogDescription>
            Are you sure you want to delete the URL rewrite "{requestPath}"?
            This action cannot be undone.
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel>Cancel</AlertDialogCancel>
          <AlertDialogAction onClick={handleDelete} disabled={isDeleting}>
            {isDeleting ? "Deleting..." : "Delete"}
          </AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  );
}

function getRedirectTypeLabel(redirectType: number | null): string {
  switch (redirectType) {
    case 301:
      return "301 Permanent";
    case 302:
      return "302 Temporary";
    default:
      return "Internal";
  }
}

export function CmsUrlRewritesIndex() {
  const [urlRewrites, setUrlRewrites] = useState<UrlRewrite[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  const loadUrlRewrites = useCallback(async () => {
    try {
      const response = await api.get<{ data: UrlRewrite[] }>(
        "/admin/cms/url-rewrites",
      );
      setUrlRewrites(response.data.data);
    } catch {
      toast.error("Failed to load URL rewrites");
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadUrlRewrites();
  }, [loadUrlRewrites]);

  const columns: ColumnDef<UrlRewrite>[] = [
    {
      accessorKey: "request_path",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Request Path" />
      ),
    },
    {
      accessorKey: "target_path",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Target Path" />
      ),
    },
    {
      accessorKey: "entity_type",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Entity Type" />
      ),
      cell: ({ row }) => (
        <Badge variant="secondary">
          {row.getValue<string>("entity_type")}
        </Badge>
      ),
    },
    {
      accessorKey: "redirect_type",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Redirect Type" />
      ),
      cell: ({ row }) => {
        const redirectType = row.getValue<number | null>("redirect_type");
        return (
          <Badge variant={redirectType ? "default" : "outline"}>
            {getRedirectTypeLabel(redirectType)}
          </Badge>
        );
      },
    },
    {
      accessorKey: "store_view_id",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Store View" />
      ),
    },
    {
      id: "actions",
      header: "Actions",
      cell: function ActionsCell({ row }) {
        const urlRewrite = row.original;

        return (
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon" asChild>
              <Link to={`/cms/url-rewrites/${urlRewrite.uuid}`}>
                <Pencil className="h-4 w-4" />
              </Link>
            </Button>
            <DeleteUrlRewriteButton
              uuid={urlRewrite.uuid}
              requestPath={urlRewrite.request_path}
              onDeleted={loadUrlRewrites}
            />
          </div>
        );
      },
    },
  ];

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
        title="URL Rewrites"
        description="Manage URL rewrites and redirects"
        actions={
          <Button asChild>
            <Link to="/cms/url-rewrites/create">
              <Plus className="mr-2 h-4 w-4" />
              New URL Rewrite
            </Link>
          </Button>
        }
      />
      <DataTable columns={columns} data={urlRewrites} />
    </div>
  );
}
