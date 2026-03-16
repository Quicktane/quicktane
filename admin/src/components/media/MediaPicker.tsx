import { useCallback, useEffect, useState } from "react";
import { ImagePlus } from "lucide-react";
import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { MediaUploader } from "./MediaUploader";
import { MediaGallery } from "./MediaGallery";
import { api } from "@/lib/api";
import type { MediaFile } from "@/types/media";

interface MediaPickerProps {
  onSelect: (files: MediaFile[]) => void;
  multiple?: boolean;
  trigger?: React.ReactNode;
}

export function MediaPicker({
  onSelect,
  multiple = false,
  trigger,
}: MediaPickerProps) {
  const [open, setOpen] = useState(false);
  const [files, setFiles] = useState<MediaFile[]>([]);
  const [selected, setSelected] = useState<MediaFile[]>([]);
  const [isLoading, setIsLoading] = useState(false);

  const loadFiles = useCallback(async () => {
    setIsLoading(true);

    try {
      const response = await api.get<{ data: MediaFile[] }>(
        "/admin/media/files",
      );
      setFiles(response.data.data);
    } catch {
      // silently fail
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    if (open) {
      loadFiles();
      setSelected([]);
    }
  }, [open, loadFiles]);

  const handleUpload = (file: MediaFile) => {
    setFiles((prev) => [file, ...prev]);
  };

  const handleSelect = (file: MediaFile) => {
    if (multiple) {
      setSelected((prev) => {
        const exists = prev.some((f) => f.uuid === file.uuid);
        return exists
          ? prev.filter((f) => f.uuid !== file.uuid)
          : [...prev, file];
      });
    } else {
      setSelected([file]);
    }
  };

  const handleConfirm = () => {
    onSelect(selected);
    setOpen(false);
  };

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        {trigger ?? (
          <Button variant="outline" size="sm" type="button">
            <ImagePlus className="mr-2 h-4 w-4" />
            Select Media
          </Button>
        )}
      </DialogTrigger>
      <DialogContent className="max-w-3xl">
        <DialogHeader>
          <DialogTitle>Media Library</DialogTitle>
        </DialogHeader>
        <div className="space-y-4">
          <MediaUploader onUpload={handleUpload} />
          {isLoading ? (
            <div className="py-8 text-center text-sm text-muted-foreground">
              Loading...
            </div>
          ) : (
            <MediaGallery
              files={files}
              selectedUuid={
                selected.length === 1 ? selected[0]?.uuid : undefined
              }
              onSelect={handleSelect}
            />
          )}
          <div className="flex justify-end gap-2">
            <Button variant="outline" onClick={() => setOpen(false)}>
              Cancel
            </Button>
            <Button onClick={handleConfirm} disabled={selected.length === 0}>
              Select ({selected.length})
            </Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
}
