import { Outlet } from "react-router";
import { AppSidebar } from "@/components/sidebar/Sidebar";
import { Header } from "@/components/header/Header";
import { ErrorBoundary } from "@/components/ErrorBoundary";
import { SidebarInset, SidebarProvider } from "@/components/ui/sidebar";

export function AdminLayout() {
  return (
    <SidebarProvider>
      <AppSidebar />
      <SidebarInset>
        <Header />
        <div className="flex-1 overflow-auto p-6">
          <ErrorBoundary>
            <Outlet />
          </ErrorBoundary>
        </div>
      </SidebarInset>
    </SidebarProvider>
  );
}
