import { useEffect, useState } from "react";
import { Plus } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { Skeleton } from "@/components/ui/skeleton";
import { AddressCard } from "@/components/address/AddressCard";
import { AddressForm, type AddressFormData } from "@/components/address/AddressForm";
import { api } from "@/lib/api";
import { toast } from "sonner";
import type { CustomerAddress } from "@/types/customer";

export function Addresses() {
  const [addresses, setAddresses] = useState<CustomerAddress[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [editingAddress, setEditingAddress] = useState<CustomerAddress | null>(
    null,
  );

  const fetchAddresses = async () => {
    try {
      const response = await api.get<{ data: CustomerAddress[] }>(
        "/customer/me/addresses",
      );
      setAddresses(response.data.data);
    } catch {
      toast.error("Failed to load addresses");
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchAddresses();
  }, []);

  const handleCreate = async (data: AddressFormData) => {
    await api.post("/customer/me/addresses", data);
    toast.success("Address added");
    setIsDialogOpen(false);
    fetchAddresses();
  };

  const handleUpdate = async (data: AddressFormData) => {
    if (!editingAddress) return;
    await api.put(`/customer/me/addresses/${editingAddress.uuid}`, data);
    toast.success("Address updated");
    setEditingAddress(null);
    setIsDialogOpen(false);
    fetchAddresses();
  };

  const handleDelete = async (uuid: string) => {
    try {
      await api.delete(`/customer/me/addresses/${uuid}`);
      toast.success("Address deleted");
      setAddresses((prev) => prev.filter((address) => address.uuid !== uuid));
    } catch {
      toast.error("Failed to delete address");
    }
  };

  const openCreate = () => {
    setEditingAddress(null);
    setIsDialogOpen(true);
  };

  const openEdit = (address: CustomerAddress) => {
    setEditingAddress(address);
    setIsDialogOpen(true);
  };

  if (isLoading) {
    return (
      <div className="space-y-4">
        {Array.from({ length: 2 }).map((_, index) => (
          <Skeleton key={index} className="h-40 w-full" />
        ))}
      </div>
    );
  }

  return (
    <>
      <Card>
        <CardHeader className="flex flex-row items-center justify-between">
          <CardTitle>My Addresses</CardTitle>
          <Button size="sm" onClick={openCreate}>
            <Plus className="h-4 w-4 mr-2" />
            Add Address
          </Button>
        </CardHeader>
        <CardContent>
          {addresses.length === 0 ? (
            <p className="text-center text-muted-foreground py-8">
              No addresses saved yet.
            </p>
          ) : (
            <div className="grid sm:grid-cols-2 gap-4">
              {addresses.map((address) => (
                <AddressCard
                  key={address.uuid}
                  address={address}
                  onEdit={openEdit}
                  onDelete={handleDelete}
                />
              ))}
            </div>
          )}
        </CardContent>
      </Card>

      <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
        <DialogContent className="max-w-lg max-h-[90vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>
              {editingAddress ? "Edit Address" : "Add New Address"}
            </DialogTitle>
          </DialogHeader>
          <AddressForm
            address={editingAddress}
            onSubmit={editingAddress ? handleUpdate : handleCreate}
            onCancel={() => setIsDialogOpen(false)}
          />
        </DialogContent>
      </Dialog>
    </>
  );
}
