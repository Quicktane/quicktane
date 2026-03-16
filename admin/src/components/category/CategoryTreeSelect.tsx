import { useCallback, useEffect, useState } from "react";
import { ChevronRight, ChevronDown, Check } from "lucide-react";
import { cn } from "@/lib/utils";
import { api } from "@/lib/api";
import type { Category } from "@/types/catalog";

interface CategoryTreeSelectProps {
  selectedUuids: string[];
  onChange: (uuids: string[]) => void;
  excludeUuid?: string;
}

interface TreeNodeProps {
  category: Category;
  selectedUuids: string[];
  onToggle: (uuid: string) => void;
  excludeUuid?: string;
  level: number;
}

function TreeNode({
  category,
  selectedUuids,
  onToggle,
  excludeUuid,
  level,
}: TreeNodeProps) {
  const [expanded, setExpanded] = useState(level < 2);
  const hasChildren = category.children && category.children.length > 0;

  if (category.uuid === excludeUuid) return null;

  const isSelected = selectedUuids.includes(category.uuid);

  return (
    <div>
      <div
        className={cn(
          "flex cursor-pointer items-center gap-1 rounded-md px-2 py-1 text-sm hover:bg-muted",
        )}
        style={{ paddingLeft: `${level * 16 + 8}px` }}
      >
        <button
          type="button"
          className="flex h-4 w-4 items-center justify-center"
          onClick={() => hasChildren && setExpanded(!expanded)}
        >
          {hasChildren &&
            (expanded ? (
              <ChevronDown className="h-3 w-3" />
            ) : (
              <ChevronRight className="h-3 w-3" />
            ))}
        </button>
        <button
          type="button"
          className={cn(
            "flex h-4 w-4 items-center justify-center rounded border",
            isSelected
              ? "border-primary bg-primary text-primary-foreground"
              : "border-muted-foreground/30",
          )}
          onClick={() => onToggle(category.uuid)}
        >
          {isSelected && <Check className="h-3 w-3" />}
        </button>
        <span
          className="ml-1"
          onClick={() => onToggle(category.uuid)}
          role="button"
          tabIndex={0}
          onKeyDown={(e) => {
            if (e.key === "Enter" || e.key === " ") onToggle(category.uuid);
          }}
        >
          {category.name}
        </span>
      </div>
      {expanded &&
        hasChildren &&
        category.children?.map((child) => (
          <TreeNode
            key={child.uuid}
            category={child}
            selectedUuids={selectedUuids}
            onToggle={onToggle}
            excludeUuid={excludeUuid}
            level={level + 1}
          />
        ))}
    </div>
  );
}

export function CategoryTreeSelect({
  selectedUuids,
  onChange,
  excludeUuid,
}: CategoryTreeSelectProps) {
  const [categories, setCategories] = useState<Category[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const loadCategories = async () => {
      try {
        const response = await api.get<{ data: Category[] }>(
          "/admin/catalog/categories",
        );
        setCategories(response.data.data);
      } catch {
        // silently fail
      } finally {
        setIsLoading(false);
      }
    };

    loadCategories();
  }, []);

  const handleToggle = useCallback(
    (uuid: string) => {
      onChange(
        selectedUuids.includes(uuid)
          ? selectedUuids.filter((id) => id !== uuid)
          : [...selectedUuids, uuid],
      );
    },
    [selectedUuids, onChange],
  );

  if (isLoading) {
    return (
      <div className="py-4 text-center text-sm text-muted-foreground">
        Loading categories...
      </div>
    );
  }

  return (
    <div className="max-h-64 overflow-y-auto rounded-md border p-2">
      {categories.map((category) => (
        <TreeNode
          key={category.uuid}
          category={category}
          selectedUuids={selectedUuids}
          onToggle={handleToggle}
          excludeUuid={excludeUuid}
          level={0}
        />
      ))}
    </div>
  );
}
