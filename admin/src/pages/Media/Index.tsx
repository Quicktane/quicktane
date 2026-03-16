import { useCallback, useEffect, useState } from "react";
import { toast } from "sonner";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { FileText, Loader2, Trash2 } from "lucide-react";
import { api } from "@/lib/api";
import type { MediaFile } from "@/types/media";
import { PageHeader } from "@/components/PageHeader";
import { MediaUploader } from "@/components/media/MediaUploader";
import { MediaGallery } from "@/components/media/MediaGallery";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Separator } from "@/components/ui/separator";
import { Skeleton } from "@/components/ui/skeleton";
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
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
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";

const updateFileSchema = z.object({
  alt_text: z.string().max(255).optional().or(z.literal("")),
  title: z.string().max(255).optional().or(z.literal("")),
});

type UpdateFileFormValues = z.infer<typeof updateFileSchema>;

function formatFileSize(bytes: number): string {
  if (bytes < 1024) {
    return `${bytes} B`;
  }

  if (bytes < 1024 * 1024) {
    return `${(bytes / 1024).toFixed(1)} KB`;
  }

  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

function isImage(mimeType: string): boolean {
  return mimeType.startsWith("image/");
}

function FileDetailDialog({
  file,
  open,
  onOpenChange,
  onUpdate,
  onDelete,
}: {
  file: MediaFile;
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onUpdate: (file: MediaFile) => void;
  onDelete: (uuid: string) => void;
}) {
  const [isUpdating, setIsUpdating] = useState(false);
  const [isDeleting, setIsDeleting] = useState(false);

  const form = useForm<UpdateFileFormValues>({
    resolver: zodResolver(updateFileSchema),
    defaultValues: {
      alt_text: file.alt_text ?? "",
      title: file.title ?? "",
    },
  });

  useEffect(() => {
    form.reset({
      alt_text: file.alt_text ?? "",
      title: file.title ?? "",
    });
  }, [file, form]);

  const handleUpdate = async (values: UpdateFileFormValues) => {
    setIsUpdating(true);

    try {
      const response = await api.put<{ data: MediaFile }>(
        `/admin/media/files/${file.uuid}`,
        values,
      );
      onUpdate(response.data.data);
      toast.success("File updated successfully");
    } catch {
      toast.error("Failed to update file");
    } finally {
      setIsUpdating(false);
    }
  };

  const handleDelete = async () => {
    setIsDeleting(true);

    try {
      await api.delete(`/admin/media/files/${file.uuid}`);
      onDelete(file.uuid);
      onOpenChange(false);
      toast.success("File deleted successfully");
    } catch {
      toast.error("Failed to delete file");
    } finally {
      setIsDeleting(false);
    }
  };

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-2xl">
        <DialogHeader>
          <DialogTitle>File Details</DialogTitle>
        </DialogHeader>

        <div className="grid gap-4 sm:grid-cols-2">
          <div className="overflow-hidden rounded-lg border">
            {isImage(file.mime_type) ? (
              <img
                src={file.url}
                alt={file.alt_text ?? file.filename}
                className="h-full w-full object-contain"
              />
            ) : (
              <div className="flex h-full min-h-48 items-center justify-center bg-muted">
                <FileText className="h-16 w-16 text-muted-foreground" />
              </div>
            )}
          </div>

          <div className="space-y-4">
            <div className="space-y-2 text-sm">
              <div className="flex justify-between">
                <span className="text-muted-foreground">Filename</span>
                <span className="truncate pl-2 font-medium">
                  {file.filename}
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-muted-foreground">Type</span>
                <span className="font-medium">{file.mime_type}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-muted-foreground">Size</span>
                <span className="font-medium">{formatFileSize(file.size)}</span>
              </div>
              {file.width && file.height && (
                <div className="flex justify-between">
                  <span className="text-muted-foreground">Dimensions</span>
                  <span className="font-medium">
                    {file.width} x {file.height}
                  </span>
                </div>
              )}
            </div>

            <Separator />

            <Form {...form}>
              <form
                onSubmit={form.handleSubmit(handleUpdate)}
                className="space-y-3"
              >
                <FormField
                  control={form.control}
                  name="alt_text"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Alt Text</FormLabel>
                      <FormControl>
                        <Input placeholder="Describe the image..." {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="title"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Title</FormLabel>
                      <FormControl>
                        <Input placeholder="File title..." {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <Button type="submit" size="sm" disabled={isUpdating}>
                  {isUpdating ? (
                    <>
                      <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                      Saving...
                    </>
                  ) : (
                    "Save Changes"
                  )}
                </Button>
              </form>
            </Form>

            {file.variants && file.variants.length > 0 && (
              <>
                <Separator />
                <div>
                  <h4 className="mb-2 text-sm font-medium">Variants</h4>
                  <div className="space-y-1">
                    {file.variants.map((variant) => (
                      <div
                        key={variant.id}
                        className="flex items-center justify-between text-xs"
                      >
                        <span className="font-medium">
                          {variant.variant_name}
                        </span>
                        <span className="text-muted-foreground">
                          {variant.width}x{variant.height} -{" "}
                          {formatFileSize(variant.size)}
                        </span>
                      </div>
                    ))}
                  </div>
                </div>
              </>
            )}
          </div>
        </div>

        <DialogFooter className="sm:justify-between">
          <AlertDialog>
            <AlertDialogTrigger asChild>
              <Button variant="destructive" size="sm">
                <Trash2 className="mr-2 h-4 w-4" />
                Delete File
              </Button>
            </AlertDialogTrigger>
            <AlertDialogContent>
              <AlertDialogHeader>
                <AlertDialogTitle>Delete File</AlertDialogTitle>
                <AlertDialogDescription>
                  Are you sure you want to delete "{file.filename}"? This action
                  cannot be undone.
                </AlertDialogDescription>
              </AlertDialogHeader>
              <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <AlertDialogAction
                  onClick={handleDelete}
                  disabled={isDeleting}
                >
                  {isDeleting ? "Deleting..." : "Delete"}
                </AlertDialogAction>
              </AlertDialogFooter>
            </AlertDialogContent>
          </AlertDialog>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}

export function MediaIndex() {
  const [files, setFiles] = useState<MediaFile[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [selectedFile, setSelectedFile] = useState<MediaFile | null>(null);

  const loadFiles = useCallback(async () => {
    try {
      const response = await api.get<{ data: MediaFile[] }>(
        "/admin/media/files",
      );
      setFiles(response.data.data);
    } catch {
      toast.error("Failed to load media files");
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadFiles();
  }, [loadFiles]);

  const handleUpload = (file: MediaFile) => {
    setFiles((previous) => [file, ...previous]);
    toast.success(`"${file.filename}" uploaded successfully`);
  };

  const handleUpdate = (updatedFile: MediaFile) => {
    setFiles((previous) =>
      previous.map((file) =>
        file.uuid === updatedFile.uuid ? updatedFile : file,
      ),
    );
    setSelectedFile(updatedFile);
  };

  const handleDelete = (uuid: string) => {
    setFiles((previous) => previous.filter((file) => file.uuid !== uuid));
    setSelectedFile(null);
  };

  if (isLoading) {
    return (
      <div className="space-y-4">
        <Skeleton className="h-8 w-48" />
        <Skeleton className="h-32 w-full" />
        <Skeleton className="h-64 w-full" />
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Media Library"
        description="Upload and manage media files"
      />

      <MediaUploader onUpload={handleUpload} />

      {files.length === 0 ? (
        <div className="flex h-48 items-center justify-center rounded-lg border-2 border-dashed">
          <p className="text-sm text-muted-foreground">
            No files uploaded yet. Drag and drop files above to get started.
          </p>
        </div>
      ) : (
        <MediaGallery
          files={files}
          selectedUuid={selectedFile?.uuid}
          onSelect={setSelectedFile}
        />
      )}

      {selectedFile && (
        <FileDetailDialog
          file={selectedFile}
          open={!!selectedFile}
          onOpenChange={(open) => {
            if (!open) {
              setSelectedFile(null);
            }
          }}
          onUpdate={handleUpdate}
          onDelete={handleDelete}
        />
      )}
    </div>
  );
}
