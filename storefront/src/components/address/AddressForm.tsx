import { useEffect, useState } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Loader2 } from "lucide-react";
import { api } from "@/lib/api";
import type { Country, Region } from "@/types/directory";
import type { CustomerAddress } from "@/types/customer";

interface AddressFormProps {
  address?: CustomerAddress | null;
  onSubmit: (data: AddressFormData) => Promise<void>;
  onCancel: () => void;
}

export interface AddressFormData {
  first_name: string;
  last_name: string;
  company: string;
  street_line_1: string;
  street_line_2: string;
  city: string;
  region_id: number | null;
  postcode: string;
  country_id: string;
  phone: string;
  is_default_billing: boolean;
  is_default_shipping: boolean;
}

export function AddressForm({ address, onSubmit, onCancel }: AddressFormProps) {
  const [countries, setCountries] = useState<Country[]>([]);
  const [regions, setRegions] = useState<Region[]>([]);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const [formData, setFormData] = useState<AddressFormData>({
    first_name: address?.first_name ?? "",
    last_name: address?.last_name ?? "",
    company: address?.company ?? "",
    street_line_1: address?.street_line_1 ?? "",
    street_line_2: address?.street_line_2 ?? "",
    city: address?.city ?? "",
    region_id: address?.region_id ?? null,
    postcode: address?.postcode ?? "",
    country_id: address?.country_id ?? "",
    phone: address?.phone ?? "",
    is_default_billing: address?.is_default_billing ?? false,
    is_default_shipping: address?.is_default_shipping ?? false,
  });

  useEffect(() => {
    api
      .get<{ data: Country[] }>("/directory/countries")
      .then((response) => setCountries(response.data.data))
      .catch(() => {});
  }, []);

  useEffect(() => {
    if (!formData.country_id) {
      setRegions([]);
      return;
    }

    const selectedCountry = countries.find(
      (country) => country.iso2 === formData.country_id,
    );

    if (selectedCountry?.regions && selectedCountry.regions.length > 0) {
      setRegions(selectedCountry.regions);
    } else {
      api
        .get<{ data: Region[] }>(
          `/directory/countries/${formData.country_id}/regions`,
        )
        .then((response) => setRegions(response.data.data))
        .catch(() => setRegions([]));
    }
  }, [formData.country_id, countries]);

  const handleChange = (field: keyof AddressFormData, value: string | number | boolean | null) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  const handleSubmit = async (event: React.FormEvent) => {
    event.preventDefault();
    setIsSubmitting(true);
    try {
      await onSubmit(formData);
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-4">
      <div className="grid grid-cols-2 gap-4">
        <div className="space-y-2">
          <Label htmlFor="first_name">First Name</Label>
          <Input
            id="first_name"
            value={formData.first_name}
            onChange={(event) => handleChange("first_name", event.target.value)}
            required
          />
        </div>
        <div className="space-y-2">
          <Label htmlFor="last_name">Last Name</Label>
          <Input
            id="last_name"
            value={formData.last_name}
            onChange={(event) => handleChange("last_name", event.target.value)}
            required
          />
        </div>
      </div>

      <div className="space-y-2">
        <Label htmlFor="company">Company (optional)</Label>
        <Input
          id="company"
          value={formData.company}
          onChange={(event) => handleChange("company", event.target.value)}
        />
      </div>

      <div className="space-y-2">
        <Label htmlFor="street_line_1">Street Address</Label>
        <Input
          id="street_line_1"
          value={formData.street_line_1}
          onChange={(event) => handleChange("street_line_1", event.target.value)}
          required
        />
      </div>

      <div className="space-y-2">
        <Label htmlFor="street_line_2">Street Address 2 (optional)</Label>
        <Input
          id="street_line_2"
          value={formData.street_line_2}
          onChange={(event) => handleChange("street_line_2", event.target.value)}
        />
      </div>

      <div className="grid grid-cols-2 gap-4">
        <div className="space-y-2">
          <Label htmlFor="city">City</Label>
          <Input
            id="city"
            value={formData.city}
            onChange={(event) => handleChange("city", event.target.value)}
            required
          />
        </div>
        <div className="space-y-2">
          <Label htmlFor="postcode">Postcode</Label>
          <Input
            id="postcode"
            value={formData.postcode}
            onChange={(event) => handleChange("postcode", event.target.value)}
            required
          />
        </div>
      </div>

      <div className="grid grid-cols-2 gap-4">
        <div className="space-y-2">
          <Label>Country</Label>
          <Select
            value={formData.country_id}
            onValueChange={(value) => {
              handleChange("country_id", value);
              handleChange("region_id", null);
            }}
          >
            <SelectTrigger>
              <SelectValue placeholder="Select country" />
            </SelectTrigger>
            <SelectContent>
              {countries.map((country) => (
                <SelectItem key={country.iso2} value={country.iso2}>
                  {country.name}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>

        {regions.length > 0 && (
          <div className="space-y-2">
            <Label>State / Region</Label>
            <Select
              value={formData.region_id?.toString() ?? ""}
              onValueChange={(value) => handleChange("region_id", parseInt(value, 10))}
            >
              <SelectTrigger>
                <SelectValue placeholder="Select region" />
              </SelectTrigger>
              <SelectContent>
                {regions.map((region) => (
                  <SelectItem key={region.id} value={region.id.toString()}>
                    {region.name}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        )}
      </div>

      <div className="space-y-2">
        <Label htmlFor="phone">Phone (optional)</Label>
        <Input
          id="phone"
          value={formData.phone}
          onChange={(event) => handleChange("phone", event.target.value)}
        />
      </div>

      <div className="flex items-center gap-6">
        <label className="flex items-center gap-2 text-sm">
          <input
            type="checkbox"
            checked={formData.is_default_billing}
            onChange={(event) => handleChange("is_default_billing", event.target.checked)}
            className="rounded"
          />
          Default billing address
        </label>
        <label className="flex items-center gap-2 text-sm">
          <input
            type="checkbox"
            checked={formData.is_default_shipping}
            onChange={(event) => handleChange("is_default_shipping", event.target.checked)}
            className="rounded"
          />
          Default shipping address
        </label>
      </div>

      <div className="flex justify-end gap-3 pt-2">
        <Button type="button" variant="outline" onClick={onCancel}>
          Cancel
        </Button>
        <Button type="submit" disabled={isSubmitting}>
          {isSubmitting && <Loader2 className="h-4 w-4 animate-spin mr-2" />}
          {address ? "Update Address" : "Add Address"}
        </Button>
      </div>
    </form>
  );
}
