import { useCallback, useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router";
import { toast } from "sonner";
import { api } from "@/lib/api";
import type { NotificationLog } from "@/types/notification";
import { PageHeader } from "@/components/PageHeader";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";

const statusVariant: Record<
  string,
  "default" | "secondary" | "outline" | "destructive"
> = {
  sent: "default",
  failed: "destructive",
  pending: "secondary",
};

function DetailRow({ label, children }: { label: string; children: React.ReactNode }) {
  return (
    <div className="grid grid-cols-3 gap-4 py-3 border-b last:border-b-0">
      <dt className="text-sm font-medium text-muted-foreground">{label}</dt>
      <dd className="col-span-2 text-sm">{children}</dd>
    </div>
  );
}

export function NotificationLogsShow() {
  const { uuid } = useParams<{ uuid: string }>();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(true);
  const [log, setLog] = useState<NotificationLog | null>(null);

  const loadLog = useCallback(async () => {
    try {
      const response = await api.get<{ data: NotificationLog }>(
        `/admin/notification/logs/${uuid}`,
      );
      setLog(response.data.data);
    } catch {
      toast.error("Failed to load notification log");
      navigate("/notifications/logs");
    } finally {
      setIsLoading(false);
    }
  }, [uuid, navigate]);

  useEffect(() => {
    loadLog();
  }, [loadLog]);

  if (isLoading || !log) {
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
        title="Notification Detail"
        description={`Log ${log.uuid}`}
        actions={
          <Badge variant={statusVariant[log.status] ?? "outline"}>
            {log.status.charAt(0).toUpperCase() + log.status.slice(1)}
          </Badge>
        }
      />

      <Card>
        <CardHeader>
          <CardTitle>Log Details</CardTitle>
        </CardHeader>
        <CardContent>
          <dl>
            <DetailRow label="Recipient">{log.recipient}</DetailRow>
            <DetailRow label="Template">{log.template_code}</DetailRow>
            <DetailRow label="Subject">{log.subject ?? "-"}</DetailRow>
            <DetailRow label="Channel">{log.channel}</DetailRow>
            <DetailRow label="Status">
              <Badge variant={statusVariant[log.status] ?? "outline"}>
                {log.status.charAt(0).toUpperCase() + log.status.slice(1)}
              </Badge>
            </DetailRow>
            {log.status === "failed" && log.error_message && (
              <DetailRow label="Error Message">
                <span className="text-destructive">{log.error_message}</span>
              </DetailRow>
            )}
            <DetailRow label="Store View ID">{log.store_view_id}</DetailRow>
            <DetailRow label="Sent At">
              {log.sent_at
                ? new Date(log.sent_at).toLocaleDateString("en-US", {
                    year: "numeric",
                    month: "short",
                    day: "numeric",
                    hour: "2-digit",
                    minute: "2-digit",
                  })
                : "-"}
            </DetailRow>
            <DetailRow label="Created At">
              {new Date(log.created_at).toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit",
              })}
            </DetailRow>
          </dl>
        </CardContent>
      </Card>

      <div>
        <Button
          variant="outline"
          onClick={() => navigate("/notifications/logs")}
        >
          Back to Notification Log
        </Button>
      </div>
    </div>
  );
}
