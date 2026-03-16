import { Link, useLocation } from "react-router";
import type { LucideIcon } from "lucide-react";
import {
  LayoutDashboard,
  Globe,
  Store,
  Languages,
  MapPin,
  Coins,
  Users,
  Shield,
  Package,
  FolderTree,
  Tags,
  Layers,
  Warehouse,
  PackageCheck,
  Image,
  UserCircle,
  UsersRound,
  ShoppingCart,
  ClipboardList,
  Receipt,
  Calculator,
  Truck,
  Percent,
  Tag,
  FileText,
  SquareCode,
  Link2,
  Search,
  Bell,
} from "lucide-react";
import {
  Sidebar,
  SidebarContent,
  SidebarGroup,
  SidebarGroupContent,
  SidebarGroupLabel,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarRail,
} from "@/components/ui/sidebar";
import { usePermission } from "@/hooks/usePermission";

interface NavItem {
  to: string;
  label: string;
  icon: LucideIcon;
}

interface NavSection {
  title: string;
  permission: string;
  items: NavItem[];
}

const navSections: NavSection[] = [
  {
    title: "Catalog",
    permission: "catalog.products.manage",
    items: [
      { to: "/catalog/products", label: "Products", icon: Package },
      { to: "/catalog/categories", label: "Categories", icon: FolderTree },
      { to: "/catalog/attributes", label: "Attributes", icon: Tags },
      { to: "/catalog/attribute-sets", label: "Attribute Sets", icon: Layers },
    ],
  },
  {
    title: "Customers",
    permission: "customer.customers.manage",
    items: [
      { to: "/customers", label: "Customers", icon: UserCircle },
      { to: "/customers/groups", label: "Customer Groups", icon: UsersRound },
    ],
  },
  {
    title: "Sales",
    permission: "cart.carts.view",
    items: [
      { to: "/orders", label: "Orders", icon: ClipboardList },
      { to: "/carts", label: "Carts", icon: ShoppingCart },
      { to: "/invoices", label: "Invoices", icon: Receipt },
    ],
  },
  {
    title: "Tax",
    permission: "tax.manage",
    items: [
      { to: "/tax/classes", label: "Tax Classes", icon: Calculator },
      { to: "/tax/zones", label: "Tax Zones", icon: Calculator },
      { to: "/tax/rates", label: "Tax Rates", icon: Calculator },
      { to: "/tax/rules", label: "Tax Rules", icon: Calculator },
    ],
  },
  {
    title: "Shipping",
    permission: "shipping.manage",
    items: [
      { to: "/shipping/methods", label: "Methods", icon: Truck },
      { to: "/shipping/zones", label: "Zones", icon: Truck },
      { to: "/shipping/rates", label: "Rates", icon: Truck },
    ],
  },
  {
    title: "Marketing",
    permission: "promotion.manage",
    items: [
      { to: "/marketing/rules", label: "Cart Price Rules", icon: Percent },
      { to: "/marketing/coupons", label: "Coupons", icon: Tag },
    ],
  },
  {
    title: "Inventory",
    permission: "inventory.sources.manage",
    items: [
      { to: "/inventory/sources", label: "Sources", icon: Warehouse },
      { to: "/inventory/stock", label: "Stock", icon: PackageCheck },
    ],
  },
  {
    title: "Content",
    permission: "cms.pages.manage",
    items: [
      { to: "/cms/pages", label: "Pages", icon: FileText },
      { to: "/cms/blocks", label: "Blocks", icon: SquareCode },
      { to: "/cms/url-rewrites", label: "URL Rewrites", icon: Link2 },
    ],
  },
  {
    title: "Search",
    permission: "search.manage",
    items: [{ to: "/search/synonyms", label: "Synonyms", icon: Search }],
  },
  {
    title: "Notifications",
    permission: "notification.manage",
    items: [
      { to: "/notifications/logs", label: "Notification Log", icon: Bell },
    ],
  },
  {
    title: "Media",
    permission: "media.files.manage",
    items: [{ to: "/media", label: "Media Library", icon: Image }],
  },
  {
    title: "Stores",
    permission: "store.websites.manage",
    items: [
      { to: "/stores/websites", label: "Websites", icon: Globe },
      { to: "/stores/store-views", label: "Store Views", icon: Languages },
      { to: "/stores/config", label: "Configuration", icon: Store },
    ],
  },
  {
    title: "Directory",
    permission: "directory.countries.manage",
    items: [
      { to: "/directory/countries", label: "Countries", icon: MapPin },
      { to: "/directory/currencies", label: "Currencies", icon: Coins },
    ],
  },
  {
    title: "System",
    permission: "user.users.manage",
    items: [
      { to: "/system/users", label: "Users", icon: Users },
      { to: "/system/roles", label: "Roles", icon: Shield },
    ],
  },
];

export function AppSidebar() {
  const { hasPermission } = usePermission();
  const location = useLocation();

  return (
    <Sidebar collapsible="icon">
      <SidebarHeader className="px-4 py-3">
        <Link to="/" className="flex items-center gap-2">
          <Package className="size-5 shrink-0" />
          <span className="text-lg font-bold tracking-tight group-data-[collapsible=icon]:hidden">
            Quicktane
          </span>
        </Link>
      </SidebarHeader>

      <SidebarContent>
        {/* Dashboard - always visible */}
        <SidebarGroup>
          <SidebarGroupContent>
            <SidebarMenu>
              <SidebarMenuItem>
                <SidebarMenuButton
                  asChild
                  isActive={location.pathname === "/"}
                  tooltip="Dashboard"
                >
                  <Link to="/">
                    <LayoutDashboard />
                    <span>Dashboard</span>
                  </Link>
                </SidebarMenuButton>
              </SidebarMenuItem>
            </SidebarMenu>
          </SidebarGroupContent>
        </SidebarGroup>

        {/* Dynamic nav sections */}
        {navSections.map(
          (section) =>
            hasPermission(section.permission) && (
              <SidebarGroup key={section.title}>
                <SidebarGroupLabel>{section.title}</SidebarGroupLabel>
                <SidebarGroupContent>
                  <SidebarMenu>
                    {section.items.map((item) => (
                      <SidebarMenuItem key={item.to}>
                        <SidebarMenuButton
                          asChild
                          isActive={location.pathname === item.to}
                          tooltip={item.label}
                        >
                          <Link to={item.to}>
                            <item.icon />
                            <span>{item.label}</span>
                          </Link>
                        </SidebarMenuButton>
                      </SidebarMenuItem>
                    ))}
                  </SidebarMenu>
                </SidebarGroupContent>
              </SidebarGroup>
            ),
        )}
      </SidebarContent>

      <SidebarRail />
    </Sidebar>
  );
}
