import { useCallback, useState } from "react";
import { useDropzone } from "react-dropzone";
import { Upload, X, Loader2 } from "lucide-react";
import { cn } from "@/lib/utils";
import { api } from "@/lib/api";
import type { MediaFile } from "@/types/media";

interface MediaUploaderProps {
  onUpload: (file: MediaFile) => void;
  accept?: Record<string, string[]>;
  maxSize?: number;
  className?: string;
}

export function MediaUploader({
  onUpload,
  accept = {
    "image/jpeg": [".jpg", ".jpeg"],
    "image/png": [".png"],
    "image/gif": [".gif"],
    "image/webp": [".webp"],
    "image/svg+xml": [".svg"],
    "application/pdf": [".pdf"],
  },
  maxSize = 10 * 1024 * 1024,
  className,
}: MediaUploaderProps) {
  const [isUploading, setIsUploading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const onDrop = useCallback(
    async (acceptedFiles: File[]) => {
      setError(null);

      for (const file of acceptedFiles) {
        setIsUploading(true);

        try {
          const formData = new FormData();
          formData.append("file", file);

          const response = await api.post<{ data: MediaFile }>(
            "/admin/media/files",
            formData,
            {
              headers: { "Content-Type": "multipart/form-data" },
            },
          );

          onUpload(response.data.data);
        } catch {
          setError(`Failed to upload ${file.name}`);
        } finally {
          setIsUploading(false);
        }
      }
    },
    [onUpload],
  );

  const { getRootProps, getInputProps, isDragActive } = useDropzone({
    onDrop,
    accept,
    maxSize,
    multiple: true,
  });

  return (
    <div className={className}>
      <div
        {...getRootProps()}
        className={cn(
          "flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed p-8 transition-colors",
          isDragActive
            ? "border-primary bg-primary/5"
            : "border-muted-foreground/25 hover:border-primary/50",
          isUploading && "pointer-events-none opacity-50",
        )}
      >
        <input {...getInputProps()} />
        {isUploading ? (
          <Loader2 className="h-8 w-8 animate-spin text-muted-foreground" />
        ) : (
          <Upload className="h-8 w-8 text-muted-foreground" />
        )}
        <p className="mt-2 text-sm text-muted-foreground">
          {isDragActive
            ? "Drop files here..."
            : "Drag & drop files, or click to select"}
        </p>
        <p className="mt-1 text-xs text-muted-foreground">
          JPEG, PNG, GIF, WebP, SVG, PDF up to 10MB
        </p>
      </div>
      {error && (
        <div className="mt-2 flex items-center gap-1 text-sm text-destructive">
          <X className="h-4 w-4" />
          {error}
        </div>
      )}
    </div>
  );
}
