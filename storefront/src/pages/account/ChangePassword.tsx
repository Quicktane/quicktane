import { useState } from "react";
import { Loader2 } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { api } from "@/lib/api";
import { toast } from "sonner";
import type { AxiosError } from "axios";

interface ValidationErrors {
  message: string;
  errors: Record<string, string[]>;
}

export function ChangePassword() {
  const [formData, setFormData] = useState({
    current_password: "",
    password: "",
    password_confirmation: "",
  });
  const [errors, setErrors] = useState<Record<string, string[]>>({});
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleChange = (field: string, value: string) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
    setErrors((prev) => {
      const next = { ...prev };
      delete next[field];
      return next;
    });
  };

  const handleSubmit = async (event: React.FormEvent) => {
    event.preventDefault();
    setIsSubmitting(true);
    setErrors({});

    try {
      await api.put("/customer/me/password", formData);
      toast.success("Password changed successfully");
      setFormData({
        current_password: "",
        password: "",
        password_confirmation: "",
      });
    } catch (error) {
      const axiosError = error as AxiosError<ValidationErrors>;
      if (axiosError.response?.data?.errors) {
        setErrors(axiosError.response.data.errors);
      } else {
        toast.error(
          axiosError.response?.data?.message ?? "Failed to change password",
        );
      }
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <Card>
      <CardHeader>
        <CardTitle>Change Password</CardTitle>
      </CardHeader>
      <CardContent>
        <form onSubmit={handleSubmit} className="space-y-4 max-w-md">
          <div className="space-y-2">
            <Label htmlFor="current_password">Current Password</Label>
            <Input
              id="current_password"
              type="password"
              value={formData.current_password}
              onChange={(event) =>
                handleChange("current_password", event.target.value)
              }
              required
              autoComplete="current-password"
            />
            {errors.current_password && (
              <p className="text-sm text-destructive">
                {errors.current_password[0]}
              </p>
            )}
          </div>

          <div className="space-y-2">
            <Label htmlFor="password">New Password</Label>
            <Input
              id="password"
              type="password"
              value={formData.password}
              onChange={(event) =>
                handleChange("password", event.target.value)
              }
              required
              autoComplete="new-password"
            />
            {errors.password && (
              <p className="text-sm text-destructive">{errors.password[0]}</p>
            )}
          </div>

          <div className="space-y-2">
            <Label htmlFor="password_confirmation">Confirm New Password</Label>
            <Input
              id="password_confirmation"
              type="password"
              value={formData.password_confirmation}
              onChange={(event) =>
                handleChange("password_confirmation", event.target.value)
              }
              required
              autoComplete="new-password"
            />
          </div>

          <Button type="submit" disabled={isSubmitting}>
            {isSubmitting && (
              <Loader2 className="h-4 w-4 animate-spin mr-2" />
            )}
            Change Password
          </Button>
        </form>
      </CardContent>
    </Card>
  );
}
