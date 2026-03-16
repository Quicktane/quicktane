import { BrowserRouter, Routes, Route } from "react-router";
import { AuthProvider } from "@/contexts/AuthContext";
import { ProtectedRoute } from "@/components/ProtectedRoute";
import { AdminLayout } from "@/layouts/AdminLayout";
import { Dashboard } from "@/pages/Dashboard";
import { Login } from "@/pages/Login";
import { NotFound } from "@/pages/NotFound";
import { AttributesIndex } from "@/pages/Catalog/Attributes/Index";
import { AttributesCreate } from "@/pages/Catalog/Attributes/Create";
import { AttributesEdit } from "@/pages/Catalog/Attributes/Edit";
import { AttributeSetsIndex } from "@/pages/Catalog/AttributeSets/Index";
import { AttributeSetsCreate } from "@/pages/Catalog/AttributeSets/Create";
import { AttributeSetsEdit } from "@/pages/Catalog/AttributeSets/Edit";
import { CategoriesIndex } from "@/pages/Catalog/Categories/Index";
import { ProductsIndex } from "@/pages/Catalog/Products/Index";
import { ProductsCreate } from "@/pages/Catalog/Products/Create";
import { ProductsEdit } from "@/pages/Catalog/Products/Edit";
import { SourcesIndex } from "@/pages/Inventory/Sources/Index";
import { SourcesCreate } from "@/pages/Inventory/Sources/Create";
import { SourcesEdit } from "@/pages/Inventory/Sources/Edit";
import { StockIndex } from "@/pages/Inventory/Stock/Index";
import { MediaIndex } from "@/pages/Media/Index";
import { WebsitesIndex } from "@/pages/Stores/Websites/Index";
import { WebsitesCreate } from "@/pages/Stores/Websites/Create";
import { WebsitesEdit } from "@/pages/Stores/Websites/Edit";
import { StoreViewsIndex } from "@/pages/Stores/StoreViews/Index";
import { StoreViewsCreate } from "@/pages/Stores/StoreViews/Create";
import { StoreViewsEdit } from "@/pages/Stores/StoreViews/Edit";
import { ConfigurationIndex } from "@/pages/Stores/Configuration/Index";
import { CountriesIndex } from "@/pages/Directory/Countries/Index";
import { CountriesEdit } from "@/pages/Directory/Countries/Edit";
import { CurrenciesIndex } from "@/pages/Directory/Currencies/Index";
import { CurrenciesEdit } from "@/pages/Directory/Currencies/Edit";
import { UsersIndex } from "@/pages/System/Users/Index";
import { UsersCreate } from "@/pages/System/Users/Create";
import { UsersEdit } from "@/pages/System/Users/Edit";
import { RolesIndex } from "@/pages/System/Roles/Index";
import { RolesCreate } from "@/pages/System/Roles/Create";
import { RolesEdit } from "@/pages/System/Roles/Edit";
import { CustomersIndex } from "@/pages/Customers/Index";
import { CustomersCreate } from "@/pages/Customers/Create";
import { CustomersEdit } from "@/pages/Customers/Edit";
import { CustomerGroupsIndex } from "@/pages/Customers/Groups/Index";
import { CustomerGroupsCreate } from "@/pages/Customers/Groups/Create";
import { CustomerGroupsEdit } from "@/pages/Customers/Groups/Edit";
import { CartsIndex } from "@/pages/Carts/Index";
import { CartsShow } from "@/pages/Carts/Show";
import { OrdersIndex } from "@/pages/Orders/Index";
import { OrdersShow } from "@/pages/Orders/Show";
import { InvoicesIndex } from "@/pages/Invoices/Index";
import { TaxClassesIndex } from "@/pages/Tax/Classes/Index";
import { TaxClassesCreate } from "@/pages/Tax/Classes/Create";
import { TaxClassesEdit } from "@/pages/Tax/Classes/Edit";
import { TaxZonesIndex } from "@/pages/Tax/Zones/Index";
import { TaxZonesCreate } from "@/pages/Tax/Zones/Create";
import { TaxZonesEdit } from "@/pages/Tax/Zones/Edit";
import { TaxRatesIndex } from "@/pages/Tax/Rates/Index";
import { TaxRatesCreate } from "@/pages/Tax/Rates/Create";
import { TaxRatesEdit } from "@/pages/Tax/Rates/Edit";
import { TaxRulesIndex } from "@/pages/Tax/Rules/Index";
import { TaxRulesCreate } from "@/pages/Tax/Rules/Create";
import { TaxRulesEdit } from "@/pages/Tax/Rules/Edit";
import { ShippingMethodsIndex } from "@/pages/Shipping/Methods/Index";
import { ShippingMethodsCreate } from "@/pages/Shipping/Methods/Create";
import { ShippingMethodsEdit } from "@/pages/Shipping/Methods/Edit";
import { ShippingZonesIndex } from "@/pages/Shipping/Zones/Index";
import { ShippingZonesCreate } from "@/pages/Shipping/Zones/Create";
import { ShippingZonesEdit } from "@/pages/Shipping/Zones/Edit";
import { ShippingRatesIndex } from "@/pages/Shipping/Rates/Index";
import { ShippingRatesCreate } from "@/pages/Shipping/Rates/Create";
import { ShippingRatesEdit } from "@/pages/Shipping/Rates/Edit";
import { MarketingRulesIndex } from "@/pages/Marketing/Rules/Index";
import { MarketingRulesCreate } from "@/pages/Marketing/Rules/Create";
import { MarketingRulesEdit } from "@/pages/Marketing/Rules/Edit";
import { MarketingCouponsIndex } from "@/pages/Marketing/Coupons/Index";
import { MarketingCouponsCreate } from "@/pages/Marketing/Coupons/Create";
import { MarketingCouponsEdit } from "@/pages/Marketing/Coupons/Edit";
import { CmsPagesIndex } from "@/pages/Cms/Pages/Index";
import { CmsPagesCreate } from "@/pages/Cms/Pages/Create";
import { CmsPagesEdit } from "@/pages/Cms/Pages/Edit";
import { CmsBlocksIndex } from "@/pages/Cms/Blocks/Index";
import { CmsBlocksCreate } from "@/pages/Cms/Blocks/Create";
import { CmsBlocksEdit } from "@/pages/Cms/Blocks/Edit";
import { CmsUrlRewritesIndex } from "@/pages/Cms/UrlRewrites/Index";
import { CmsUrlRewritesCreate } from "@/pages/Cms/UrlRewrites/Create";
import { CmsUrlRewritesEdit } from "@/pages/Cms/UrlRewrites/Edit";
import { SearchSynonymsIndex } from "@/pages/Search/Synonyms/Index";
import { SearchSynonymsCreate } from "@/pages/Search/Synonyms/Create";
import { SearchSynonymsEdit } from "@/pages/Search/Synonyms/Edit";
import { NotificationLogsIndex } from "@/pages/Notifications/Logs/Index";
import { NotificationLogsShow } from "@/pages/Notifications/Logs/Show";

export function App() {
  return (
    <BrowserRouter basename="/admin">
      <AuthProvider>
        <Routes>
          <Route path="/login" element={<Login />} />
          <Route element={<ProtectedRoute />}>
            <Route element={<AdminLayout />}>
              <Route path="/" element={<Dashboard />} />

              <Route path="/catalog/products" element={<ProductsIndex />} />
              <Route
                path="/catalog/products/create"
                element={<ProductsCreate />}
              />
              <Route
                path="/catalog/products/:uuid"
                element={<ProductsEdit />}
              />
              <Route
                path="/catalog/categories"
                element={<CategoriesIndex />}
              />
              <Route
                path="/catalog/attributes"
                element={<AttributesIndex />}
              />
              <Route
                path="/catalog/attributes/create"
                element={<AttributesCreate />}
              />
              <Route
                path="/catalog/attributes/:uuid"
                element={<AttributesEdit />}
              />
              <Route
                path="/catalog/attribute-sets"
                element={<AttributeSetsIndex />}
              />
              <Route
                path="/catalog/attribute-sets/create"
                element={<AttributeSetsCreate />}
              />
              <Route
                path="/catalog/attribute-sets/:uuid"
                element={<AttributeSetsEdit />}
              />

              <Route path="/inventory/sources" element={<SourcesIndex />} />
              <Route
                path="/inventory/sources/create"
                element={<SourcesCreate />}
              />
              <Route
                path="/inventory/sources/:uuid"
                element={<SourcesEdit />}
              />
              <Route path="/inventory/stock" element={<StockIndex />} />

              <Route path="/stores/websites" element={<WebsitesIndex />} />
              <Route
                path="/stores/websites/create"
                element={<WebsitesCreate />}
              />
              <Route
                path="/stores/websites/:uuid"
                element={<WebsitesEdit />}
              />
              <Route
                path="/stores/store-views"
                element={<StoreViewsIndex />}
              />
              <Route
                path="/stores/store-views/create"
                element={<StoreViewsCreate />}
              />
              <Route
                path="/stores/store-views/:uuid"
                element={<StoreViewsEdit />}
              />
              <Route path="/stores/config" element={<ConfigurationIndex />} />

              <Route
                path="/directory/countries"
                element={<CountriesIndex />}
              />
              <Route
                path="/directory/countries/:iso2"
                element={<CountriesEdit />}
              />
              <Route
                path="/directory/currencies"
                element={<CurrenciesIndex />}
              />
              <Route
                path="/directory/currencies/:code"
                element={<CurrenciesEdit />}
              />

              <Route path="/system/users" element={<UsersIndex />} />
              <Route
                path="/system/users/create"
                element={<UsersCreate />}
              />
              <Route path="/system/users/:uuid" element={<UsersEdit />} />
              <Route path="/system/roles" element={<RolesIndex />} />
              <Route
                path="/system/roles/create"
                element={<RolesCreate />}
              />
              <Route path="/system/roles/:id" element={<RolesEdit />} />

              <Route path="/customers" element={<CustomersIndex />} />
              <Route
                path="/customers/create"
                element={<CustomersCreate />}
              />
              <Route
                path="/customers/groups"
                element={<CustomerGroupsIndex />}
              />
              <Route
                path="/customers/groups/create"
                element={<CustomerGroupsCreate />}
              />
              <Route
                path="/customers/groups/:uuid"
                element={<CustomerGroupsEdit />}
              />
              <Route
                path="/customers/:uuid"
                element={<CustomersEdit />}
              />

              <Route path="/carts" element={<CartsIndex />} />
              <Route path="/carts/:uuid" element={<CartsShow />} />

              <Route path="/orders" element={<OrdersIndex />} />
              <Route path="/orders/:uuid" element={<OrdersShow />} />

              <Route path="/invoices" element={<InvoicesIndex />} />

              <Route path="/tax/classes" element={<TaxClassesIndex />} />
              <Route
                path="/tax/classes/create"
                element={<TaxClassesCreate />}
              />
              <Route
                path="/tax/classes/:uuid"
                element={<TaxClassesEdit />}
              />
              <Route path="/tax/zones" element={<TaxZonesIndex />} />
              <Route
                path="/tax/zones/create"
                element={<TaxZonesCreate />}
              />
              <Route path="/tax/zones/:uuid" element={<TaxZonesEdit />} />
              <Route path="/tax/rates" element={<TaxRatesIndex />} />
              <Route
                path="/tax/rates/create"
                element={<TaxRatesCreate />}
              />
              <Route path="/tax/rates/:uuid" element={<TaxRatesEdit />} />
              <Route path="/tax/rules" element={<TaxRulesIndex />} />
              <Route
                path="/tax/rules/create"
                element={<TaxRulesCreate />}
              />
              <Route path="/tax/rules/:uuid" element={<TaxRulesEdit />} />

              <Route
                path="/shipping/methods"
                element={<ShippingMethodsIndex />}
              />
              <Route
                path="/shipping/methods/create"
                element={<ShippingMethodsCreate />}
              />
              <Route
                path="/shipping/methods/:uuid"
                element={<ShippingMethodsEdit />}
              />
              <Route
                path="/shipping/zones"
                element={<ShippingZonesIndex />}
              />
              <Route
                path="/shipping/zones/create"
                element={<ShippingZonesCreate />}
              />
              <Route
                path="/shipping/zones/:uuid"
                element={<ShippingZonesEdit />}
              />
              <Route
                path="/shipping/rates"
                element={<ShippingRatesIndex />}
              />
              <Route
                path="/shipping/rates/create"
                element={<ShippingRatesCreate />}
              />
              <Route
                path="/shipping/rates/:uuid"
                element={<ShippingRatesEdit />}
              />

              <Route
                path="/marketing/rules"
                element={<MarketingRulesIndex />}
              />
              <Route
                path="/marketing/rules/create"
                element={<MarketingRulesCreate />}
              />
              <Route
                path="/marketing/rules/:uuid"
                element={<MarketingRulesEdit />}
              />
              <Route
                path="/marketing/coupons"
                element={<MarketingCouponsIndex />}
              />
              <Route
                path="/marketing/coupons/create"
                element={<MarketingCouponsCreate />}
              />
              <Route
                path="/marketing/coupons/:uuid"
                element={<MarketingCouponsEdit />}
              />

              <Route path="/media" element={<MediaIndex />} />

              <Route path="/cms/pages" element={<CmsPagesIndex />} />
              <Route
                path="/cms/pages/create"
                element={<CmsPagesCreate />}
              />
              <Route path="/cms/pages/:uuid" element={<CmsPagesEdit />} />
              <Route path="/cms/blocks" element={<CmsBlocksIndex />} />
              <Route
                path="/cms/blocks/create"
                element={<CmsBlocksCreate />}
              />
              <Route
                path="/cms/blocks/:uuid"
                element={<CmsBlocksEdit />}
              />
              <Route
                path="/cms/url-rewrites"
                element={<CmsUrlRewritesIndex />}
              />
              <Route
                path="/cms/url-rewrites/create"
                element={<CmsUrlRewritesCreate />}
              />
              <Route
                path="/cms/url-rewrites/:uuid"
                element={<CmsUrlRewritesEdit />}
              />

              <Route
                path="/search/synonyms"
                element={<SearchSynonymsIndex />}
              />
              <Route
                path="/search/synonyms/create"
                element={<SearchSynonymsCreate />}
              />
              <Route
                path="/search/synonyms/:uuid"
                element={<SearchSynonymsEdit />}
              />

              <Route
                path="/notifications/logs"
                element={<NotificationLogsIndex />}
              />
              <Route
                path="/notifications/logs/:uuid"
                element={<NotificationLogsShow />}
              />

            <Route path="*" element={<NotFound />} />
          </Route>
        </Route>
      </Routes>
    </AuthProvider>
  </BrowserRouter>
  );
}
