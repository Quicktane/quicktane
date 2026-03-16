import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { type ColumnDef } from "@tanstack/react-table";
import { Eye } from "lucide-react";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { NotificationLog } from "@/types/notification";
import { PageHeader } from "@/components/PageHeader";
import { DataTable } from "@/components/data-table/DataTable";
import { DataTableColumnHeader } from "@/components/data-table/DataTableColumnHeader";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Skeleton } from "@/components/ui/skeleton";

const statusVariant: Record<
  string,
  "default" | "secondary" | "outline" | "destructive"
> = {
  sent: "default",
  failed: "destructive",
  pending: "secondary",
};

export function NotificationLogsIndex() {
  const [logs, setLogs] = useState<NotificationLog[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [statusFilter, setStatusFilter] = useState<string>("all");

  const loadLogs = useCallback(async () => {
    try {
      const response = await api.get<{ data: NotificationLog[] }>(
        "/admin/notification/logs",
      );
      setLogs(response.data.data);
    } catch {
      toast.error("Failed to load notification logs");
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadLogs();
  }, [loadLogs]);

  const filteredLogs =
    statusFilter === "all"
      ? logs
      : logs.filter((log) => log.status === statusFilter);

  const columns: ColumnDef<NotificationLog>[] = [
    {
      accessorKey: "recipient",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Recipient" />
      ),
    },
    {
      accessorKey: "template_code",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Template" />
      ),
    },
    {
      accessorKey: "subject",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Subject" />
      ),
      cell: ({ row }) => row.getValue("subject") ?? "-",
    },
    {
      accessorKey: "status",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Status" />
      ),
      cell: ({ row }) => {
        const status = row.getValue<string>("status");
        return (
          <Badge variant={statusVariant[status] ?? "outline"}>
            {status.charAt(0).toUpperCase() + status.slice(1)}
          </Badge>
        );
      },
    },
    {
      accessorKey: "channel",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Channel" />
      ),
    },
    {
      accessorKey: "sent_at",
      header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Sent At" />
      ),
      cell: ({ row }) => {
        const sentAt = row.getValue<string | null>("sent_at");
        return sentAt
          ? new Date(sentAt).toLocaleDateString("en-US", {
              year: "numeric",
              month: "short",
              day: "numeric",
              hour: "2-digit",
              minute: "2-digit",
            })
          : "-";
      },
    },
    {
      id: "actions",
      header: "Actions",
      cell: function ActionsCell({ row }) {
        const log = row.original;

        return (
          <Button variant="ghost" size="icon" asChild>
            <Link to={`/notifications/logs/${log.uuid}`}>
              <Eye className="h-4 w-4" />
            </Link>
          </Button>
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
        title="Notification Log"
        description="View notification delivery history"
        actions={
          <Select value={statusFilter} onValueChange={setStatusFilter}>
            <SelectTrigger className="w-[150px]">
              <SelectValue placeholder="Filter status" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All</SelectItem>
              <SelectItem value="pending">Pending</SelectItem>
              <SelectItem value="sent">Sent</SelectItem>
              <SelectItem value="failed">Failed</SelectItem>
            </SelectContent>
          </Select>
        }
      />
      <DataTable columns={columns} data={filteredLogs} />
    </div>
  );
}
