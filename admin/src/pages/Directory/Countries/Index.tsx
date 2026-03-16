import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef } from "@tanstack/react-table";
import { Pencil } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { Country } from "@/types/directory";
import { PageHeader } from "@/components/PageHeader";
import { DataTable } from "@/components/data-table/DataTable";
import { DataTableColumnHeader } from "@/components/data-table/DataTableColumnHeader";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";

const columns: ColumnDef<Country>[] = [
  {
    accessorKey: "name",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Name" />
    ),
  },
  {
    accessorKey: "iso2",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="ISO2" />
    ),
  },
  {
    accessorKey: "iso3",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="ISO3" />
    ),
  },
  {
    accessorKey: "phone_code",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Phone Code" />
    ),
    cell: ({ row }) => row.getValue("phone_code") ?? "-",
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
      const country = row.original;

      return (
        <div className="flex items-center gap-2">
          <Button variant="ghost" size="icon" asChild>
            <Link to={`/directory/countries/${country.iso2}`}>
              <Pencil className="h-4 w-4" />
            </Link>
          </Button>
        </div>
      );
    },
  },
];

export function CountriesIndex() {
  const [countries, setCountries] = useState<Country[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  const loadCountries = useCallback(async () => {
    try {
      const response = await api.get<{ data: Country[] }>(
        "/admin/directory/countries",
      );
      setCountries(response.data.data);
    } catch {
      toast.error("Failed to load countries");
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadCountries();
  }, [loadCountries]);

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
        title="Countries"
        description="Manage countries and their settings"
      />
      <DataTable columns={columns} data={countries} />
    </div>
  );
}
