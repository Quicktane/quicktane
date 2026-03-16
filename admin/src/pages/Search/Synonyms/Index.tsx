import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef } from "@tanstack/react-table";
import { Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { SearchSynonym } from "@/types/search";
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

function DeleteSynonymButton({
  uuid,
  term,
  onDeleted,
}: {
  uuid: string;
  term: string;
  onDeleted: () => void;
}) {
  const [isDeleting, setIsDeleting] = useState(false);

  const handleDelete = async () => {
    setIsDeleting(true);

    try {
      await api.delete(`/admin/search/synonyms/${uuid}`);
      toast.success(`Synonym "${term}" deleted`);
      onDeleted();
    } catch {
      toast.error("Failed to delete synonym");
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
          <AlertDialogTitle>Delete Synonym</AlertDialogTitle>
          <AlertDialogDescription>
            Are you sure you want to delete the synonym for "{term}"? This
            action cannot be undone.
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

export function SearchSynonymsIndex() {
  const [synonyms, setSynonyms] = useState<SearchSynonym[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  const loadSynonyms = useCallback(async () => {
    try {
      const response = await api.get<{ data: SearchSynonym[] }>(
        "/admin/search/synonyms",
      );
      setSynonyms(response.data.data);
    } catch {
      toast.error("Failed to load search synonyms");
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadSynonyms();
  }, [loadSynonyms]);

  const columns: ColumnDef<SearchSynonym>[] = [
    {
      accessorKey: "term",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Term" />
      ),
    },
    {
      accessorKey: "synonyms",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Synonyms" />
      ),
      cell: ({ row }) => {
        const synonyms = row.getValue<string[]>("synonyms");
        return synonyms.join(", ");
      },
    },
    {
      accessorKey: "is_active",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Status" />
      ),
      cell: ({ row }) => (
        <Badge variant={row.getValue("is_active") ? "default" : "outline"}>
          {row.getValue("is_active") ? "Active" : "Inactive"}
        </Badge>
      ),
    },
    {
      id: "actions",
      header: "Actions",
      cell: function ActionsCell({ row }) {
        const synonym = row.original;

        return (
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon" asChild>
              <Link to={`/search/synonyms/${synonym.uuid}`}>
                <Pencil className="h-4 w-4" />
              </Link>
            </Button>
            <DeleteSynonymButton
              uuid={synonym.uuid}
              term={synonym.term}
              onDeleted={loadSynonyms}
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
        title="Search Synonyms"
        description="Manage search term synonyms for improved search results"
        actions={
          <Button asChild>
            <Link to="/search/synonyms/create">
              <Plus className="mr-2 h-4 w-4" />
              New Synonym
            </Link>
          </Button>
        }
      />
      <DataTable columns={columns} data={synonyms} />
    </div>
  );
}
