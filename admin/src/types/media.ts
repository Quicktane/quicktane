export interface MediaVariant {
  id: number;
  variant_name: string;
  mime_type: string;
  size: number;
  width: number;
  height: number;
  url: string;
}

export interface MediaFile {
  uuid: string;
  disk: string;
  filename: string;
  mime_type: string;
  size: number;
  width: number | null;
  height: number | null;
  alt_text: string | null;
  title: string | null;
  url: string;
  created_at: string;
  updated_at: string;
  variants?: MediaVariant[];
}
