import { Link } from "react-router";
import { Store } from "lucide-react";
import { CategoryNav } from "./CategoryNav";
import { CartIndicator } from "./CartIndicator";
import { AccountMenu } from "./AccountMenu";

export function Header() {
  return (
    <header className="border-b bg-white sticky top-0 z-50">
      <div className="container mx-auto px-4">
        <div className="flex h-16 items-center justify-between">
          <Link to="/" className="flex items-center gap-2 text-xl font-bold text-primary">
            <Store className="h-6 w-6" />
            Quicktane
          </Link>

          <nav className="hidden md:flex items-center gap-6">
            <Link to="/products" className="text-sm font-medium hover:text-primary transition-colors">
              All Products
            </Link>
            <CategoryNav />
          </nav>

          <div className="flex items-center gap-4">
            <CartIndicator />
            <AccountMenu />
          </div>
        </div>
      </div>
    </header>
  );
}
