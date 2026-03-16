import { Outlet } from "react-router";
import { Header } from "@/components/header/Header";
import { Footer } from "@/components/footer/Footer";

export function StoreLayout() {
  return (
    <div className="flex min-h-screen flex-col">
      <Header />
      <main className="flex-1">
        <Outlet />
      </main>
      <Footer />
    </div>
  );
}
