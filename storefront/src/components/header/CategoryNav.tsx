import { useEffect, useState } from "react";
import { Link } from "react-router";
import { api } from "@/lib/api";
import type { Category } from "@/types/catalog";

export function CategoryNav() {
  const [categories, setCategories] = useState<Category[]>([]);

  useEffect(() => {
    api
      .get<{ data: Category[] }>("/catalog/categories")
      .then((response) => {
        setCategories(
          response.data.data.filter((category) => category.include_in_menu),
        );
      })
      .catch(() => {
        // Silently fail — nav is non-critical
      });
  }, []);

  if (categories.length === 0) {
    return null;
  }

  return (
    <>
      {categories.map((category) => (
        <Link
          key={category.uuid}
          to={`/categories/${category.slug}`}
          className="text-sm font-medium hover:text-primary transition-colors"
        >
          {category.name}
        </Link>
      ))}
    </>
  );
}
