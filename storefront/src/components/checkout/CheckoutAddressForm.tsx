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
import type { CheckoutAddress } from "@/types/checkout";
import type { Customer } from "@/types/customer";

interface CheckoutAddressFormProps {
  onSubmit: (data: CheckoutAddress) => Promise<void>;
  isLoading: boolean;
  customer?: Customer | null;
  initialAddress?: CheckoutAddress | null;
}

export function CheckoutAddressForm({
  onSubmit,
  isLoading,
  customer,
  initialAddress,
}: CheckoutAddressFormProps) {
  const [countries, setCountries] = useState<Country[]>([]);
  const [regions, setRegions] = useState<Region[]>([]);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const [formData, setFormData] = useState<CheckoutAddress>({
    first_name: initialAddress?.first_name ?? customer?.first_name ?? "",
    last_name: initialAddress?.last_name ?? customer?.last_name ?? "",
    company: initialAddress?.company ?? "",
    street_line_1: initialAddress?.street_line_1 ?? "",
    street_line_2: initialAddress?.street_line_2 ?? "",
    city: initialAddress?.city ?? "",
    region_id: initialAddress?.region_id ?? null,
    postcode: initialAddress?.postcode ?? "",
    country_id: initialAddress?.country_id ?? "",
    phone: initialAddress?.phone ?? customer?.phone ?? "",
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

  const handleChange = (
    field: keyof CheckoutAddress,
    value: string | number | null,
  ) => {
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
          <Label htmlFor="checkout_first_name">First Name</Label>
          <Input
            id="checkout_first_name"
            value={formData.first_name}
            onChange={(e) => handleChange("first_name", e.target.value)}
            required
          />
        </div>
        <div className="space-y-2">
          <Label htmlFor="checkout_last_name">Last Name</Label>
          <Input
            id="checkout_last_name"
            value={formData.last_name}
            onChange={(e) => handleChange("last_name", e.target.value)}
            required
          />
        </div>
      </div>

      <div className="space-y-2">
        <Label htmlFor="checkout_company">Company (optional)</Label>
        <Input
          id="checkout_company"
          value={formData.company ?? ""}
          onChange={(e) => handleChange("company", e.target.value)}
        />
      </div>

      <div className="space-y-2">
        <Label htmlFor="checkout_street">Street Address</Label>
        <Input
          id="checkout_street"
          value={formData.street_line_1}
          onChange={(e) => handleChange("street_line_1", e.target.value)}
          required
        />
      </div>

      <div className="space-y-2">
        <Label htmlFor="checkout_street2">Street Address 2 (optional)</Label>
        <Input
          id="checkout_street2"
          value={formData.street_line_2 ?? ""}
          onChange={(e) => handleChange("street_line_2", e.target.value)}
        />
      </div>

      <div className="grid grid-cols-2 gap-4">
        <div className="space-y-2">
          <Label htmlFor="checkout_city">City</Label>
          <Input
            id="checkout_city"
            value={formData.city}
            onChange={(e) => handleChange("city", e.target.value)}
            required
          />
        </div>
        <div className="space-y-2">
          <Label htmlFor="checkout_postcode">Postcode</Label>
          <Input
            id="checkout_postcode"
            value={formData.postcode}
            onChange={(e) => handleChange("postcode", e.target.value)}
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
              onValueChange={(value) =>
                handleChange("region_id", parseInt(value, 10))
              }
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
        <Label htmlFor="checkout_phone">Phone (optional)</Label>
        <Input
          id="checkout_phone"
          value={formData.phone ?? ""}
          onChange={(e) => handleChange("phone", e.target.value)}
        />
      </div>

      <Button
        type="submit"
        className="w-full"
        disabled={isSubmitting || isLoading}
      >
        {isSubmitting || isLoading ? (
          <>
            <Loader2 className="h-4 w-4 animate-spin mr-2" />
            Saving...
          </>
        ) : (
          "Continue to Shipping"
        )}
      </Button>
    </form>
  );
}
