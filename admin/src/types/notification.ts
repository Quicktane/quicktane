export interface NotificationLog {
  id: number;
  uuid: string;
  channel: string;
  template_code: string;
  recipient: string;
  subject: string | null;
  status: string;
  error_message: string | null;
  store_view_id: number;
  sent_at: string | null;
  created_at: string;
  updated_at: string;
}

export interface NotificationTemplate {
  code: string;
  name: string;
  description: string;
}
