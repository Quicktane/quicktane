import { BrowserRouter, Routes, Route, Navigate } from "react-router";
import { Toaster } from "@/components/ui/sonner";
import { AuthProvider } from "@/contexts/AuthContext";
import { CartProvider } from "@/contexts/CartContext";
import { StoreLayout } from "@/layouts/StoreLayout";
import { Home } from "@/pages/Home";
import { ProductList } from "@/pages/ProductList";
import { ProductDetail } from "@/pages/ProductDetail";
import { CategoryPage } from "@/pages/CategoryPage";
import { CartPage } from "@/pages/Cart";
import { Checkout } from "@/pages/Checkout";
import { OrderConfirmation } from "@/pages/OrderConfirmation";
import { Login } from "@/pages/Login";
import { Register } from "@/pages/Register";
import { AccountLayout } from "@/pages/account/AccountLayout";
import { Profile } from "@/pages/account/Profile";
import { ChangePassword } from "@/pages/account/ChangePassword";
import { Addresses } from "@/pages/account/Addresses";
import { Orders } from "@/pages/account/Orders";
import { OrderDetail } from "@/pages/account/OrderDetail";

export function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <CartProvider>
          <Routes>
            <Route element={<StoreLayout />}>
              <Route path="/" element={<Home />} />
              <Route path="/products" element={<ProductList />} />
              <Route path="/products/:slug" element={<ProductDetail />} />
              <Route path="/categories/:slug" element={<CategoryPage />} />
              <Route path="/cart" element={<CartPage />} />
              <Route path="/checkout" element={<Checkout />} />
              <Route path="/order-confirmation/:uuid" element={<OrderConfirmation />} />
              <Route path="/login" element={<Login />} />
              <Route path="/register" element={<Register />} />
              <Route path="/account" element={<AccountLayout />}>
                <Route index element={<Navigate to="profile" replace />} />
                <Route path="profile" element={<Profile />} />
                <Route path="orders" element={<Orders />} />
                <Route path="orders/:uuid" element={<OrderDetail />} />
                <Route path="password" element={<ChangePassword />} />
                <Route path="addresses" element={<Addresses />} />
              </Route>
            </Route>
          </Routes>
          <Toaster position="top-right" />
        </CartProvider>
      </AuthProvider>
    </BrowserRouter>
  );
}
