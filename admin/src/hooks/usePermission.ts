import { useCallback } from "react";
import { useAuth } from "@/contexts/AuthContext";

export function usePermission() {
  const { user } = useAuth();

  const hasPermission = useCallback(
    (permissionSlug: string): boolean => {
      if (!user?.role) {
        return false;
      }

      if (user.role.is_system) {
        return true;
      }

      return (
        user.role.permissions?.some(
          (permission) => permission.slug === permissionSlug,
        ) ?? false
      );
    },
    [user],
  );

  return { hasPermission };
}
