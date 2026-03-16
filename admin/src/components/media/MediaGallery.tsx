import { X } from "lucide-react";
import { cn } from "@/lib/utils";
import type { MediaFile } from "@/types/media";

interface MediaGalleryProps {
  files: MediaFile[];
  selectedUuid?: string | null;
  onSelect?: (file: MediaFile) => void;
  onRemove?: (file: MediaFile) => void;
  className?: string;
}

export function MediaGallery({
  files,
  selectedUuid,
  onSelect,
  onRemove,
  className,
}: MediaGalleryProps) {
  const getThumbnailUrl = (file: MediaFile): string => {
    const thumbnail = file.variants?.find(
      (v) => v.variant_name === "thumbnail",
    );
    return thumbnail?.url ?? file.url;
  };

  const isImage = (mimeType: string): boolean => {
    return mimeType.startsWith("image/");
  };

  return (
    <div className={cn("grid grid-cols-4 gap-3 sm:grid-cols-6", className)}>
      {files.map((file) => (
        <div
          key={file.uuid}
          className={cn(
            "group relative aspect-square cursor-pointer overflow-hidden rounded-lg border-2 transition-colors",
            selectedUuid === file.uuid
              ? "border-primary"
              : "border-transparent hover:border-muted-foreground/25",
          )}
          onClick={() => onSelect?.(file)}
        >
          {isImage(file.mime_type) ? (
            <img
              src={getThumbnailUrl(file)}
              alt={file.alt_text ?? file.filename}
              className="h-full w-full object-cover"
            />
          ) : (
            <div className="flex h-full w-full items-center justify-center bg-muted">
              <span className="text-xs text-muted-foreground">
                {file.mime_type.split("/")[1]?.toUpperCase()}
              </span>
            </div>
          )}
          {onRemove && (
            <button
              type="button"
              onClick={(e) => {
                e.stopPropagation();
                onRemove(file);
              }}
              className="absolute right-1 top-1 hidden rounded-full bg-destructive p-0.5 text-destructive-foreground group-hover:block"
            >
              <X className="h-3 w-3" />
            </button>
          )}
        </div>
      ))}
    </div>
  );
}
