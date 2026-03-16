# Quicktane

A modular, headless e-commerce platform built on Laravel 12 and React 19.

Quicktane provides a complete API-first backend with two standalone React frontends: a full-featured **Admin Panel** for store management and a customer-facing **Storefront**. Every part of the platform is built around a pluggable module system, making it straightforward to extend, replace, or customize any component.

## Overview

```
                         Quicktane Architecture

    Storefront SPA              Admin SPA              3rd-party clients
    (React 19)                  (React 19)             (Mobile, POS, etc.)
         |                          |                        |
         +------------- REST API (v1) ----------------------+
                              |
                    Laravel 12 + Octane (FrankenPHP)
                              |
         +----------+---------+---------+-----------+
         |          |         |         |           |
      MariaDB    Redis   Meilisearch  Queue     Storage
```

The platform follows a **headless architecture**: the Laravel backend exposes versioned REST APIs consumed by any frontend or client. The Admin and Storefront SPAs are completely decoupled and communicate exclusively through the API.

## Features

### Catalog
- Products with multiple types (simple, configurable, bundle, virtual, downloadable)
- Hierarchical categories with nested sets
- EAV-style attribute system with attribute sets, options, and typed values
- Product media management with variant generation
- Special pricing with date ranges

### Cart and Checkout
- Guest and authenticated shopping carts
- Multi-step checkout pipeline with suspend/resume capability
- Support for asynchronous payment flows (3DS, PayPal redirects, etc.)
- Coupon application and discount calculation
- Automatic stock validation and reservation

### Orders
- Full order lifecycle: pending, processing, shipped, delivered, completed, canceled, refunded
- Invoice generation and credit memos
- Order history tracking

### Payments
- Pluggable payment gateway architecture
- Transaction logging
- Gateway callback handling for external payment providers

### Shipping
- Shipping methods, zones, and zone-country mapping
- Configurable shipping rates
- Automatic shipping calculation during checkout

### Tax
- Tax classes, zones, and rules
- Tax rate management
- Automatic tax calculation in the checkout pipeline

### Promotions
- Cart price rules with conditions
- Coupon management with usage tracking

### Inventory
- Multi-source inventory management
- Stock items and stock movement tracking
- Automatic stock reservation during checkout

### Customers
- Customer registration and authentication
- Customer groups and segments
- Address book management

### CMS
- Content pages and reusable blocks
- URL rewrites for SEO-friendly URLs

### Search
- Full-text search powered by Meilisearch
- Search synonym management

### Media
- File upload and management
- Image variant generation

### Administration
- Role-based access control with granular permissions
- User and role management
- Maintenance mode toggle
- Module management and configuration
- Dynamic admin menu system

### Multi-Store
- Website, store, and store view hierarchy
- Centralized configuration management
- Per-store country and currency assignments

## Tech Stack

### Backend

| Technology | Version | Purpose |
|---|---|---|
| PHP | 8.3 | Runtime |
| Laravel | 12 | Application framework |
| Laravel Octane | 2 | High-performance application server |
| FrankenPHP | Latest | PHP application server (Caddy-based) |
| Laravel Sanctum | 4 | API token authentication |
| Laravel Scout | 11 | Full-text search abstraction |
| Meilisearch | 1.12 | Search engine |
| MySQL / MariaDB | 8.4 / 11 | Database (MySQL for dev, MariaDB for production) |
| Redis | 7 | Cache, sessions, queues, pipeline state |

### Frontend (Admin and Storefront)

| Technology | Version | Purpose |
|---|---|---|
| React | 19 | UI framework |
| TypeScript | 5.7 | Type safety |
| Vite | 6 | Build tooling and dev server |
| React Router | 7 | Client-side routing |
| Tailwind CSS | 3 | Utility-first styling |
| shadcn/ui | Latest | Component library (Radix UI primitives) |
| React Hook Form | Latest | Form management |
| Zod | 4 | Schema validation |
| TanStack Table | Latest | Data tables (admin) |
| Axios | Latest | HTTP client |

### Infrastructure

| Technology | Purpose |
|---|---|
| Docker | Containerization |
| Kubernetes | Production orchestration |
| Kustomize | Kubernetes manifest management |
| GitHub Actions | CI/CD pipeline |
| GitHub Container Registry | Docker image hosting |
| Nginx | Static file serving for SPAs |
| Longhorn | Kubernetes persistent storage |

## Project Structure

```
quicktane/
├── app/                          # Domain modules
│   ├── Cart/                     # Shopping cart management
│   ├── Catalog/                  # Products, categories, attributes
│   ├── Checkout/                 # Checkout flow and pipeline steps
│   ├── Customer/                 # Customer accounts and addresses
│   ├── Directory/                # Countries, regions, currencies
│   ├── Order/                    # Orders, invoices, credit memos
│   ├── Payment/                  # Payment methods and transactions
│   ├── Store/                    # Store hierarchy and configuration
│   └── User/                     # Admin users, roles, permissions
│
├── packages/                     # Platform packages (Composer path repos)
│   ├── core/                     # Module system, pipelines, tracing
│   ├── cms/                      # Pages, blocks, URL rewrites
│   ├── inventory/                # Stock management, sources
│   ├── media/                    # File and image management
│   ├── notification/             # Email notifications, logging
│   ├── promotion/                # Cart price rules, coupons
│   ├── search/                   # Meilisearch integration, synonyms
│   ├── shipping/                 # Shipping methods, zones, rates
│   └── tax/                      # Tax classes, zones, rates, rules
│
├── admin/                        # Admin Panel (React SPA)
│   ├── src/
│   │   ├── components/           # Reusable UI components
│   │   ├── contexts/             # Auth context
│   │   ├── hooks/                # Custom hooks (permissions, etc.)
│   │   ├── lib/                  # API client, utilities
│   │   └── pages/                # Page components by domain
│   ├── package.json
│   └── vite.config.ts
│
├── storefront/                   # Customer Storefront (React SPA)
│   ├── src/
│   │   ├── components/           # Reusable UI components
│   │   ├── contexts/             # Auth and cart contexts
│   │   ├── lib/                  # API client, utilities
│   │   └── pages/                # Page components
│   ├── package.json
│   └── vite.config.ts
│
├── .docker/images/               # Production Dockerfiles
│   ├── php-base/                 # Base image (FrankenPHP + extensions)
│   ├── api/                      # Laravel API image
│   ├── admin/                    # Admin SPA (nginx)
│   └── storefront/               # Storefront SPA (nginx)
│
├── .kubernetes/production/       # Kubernetes manifests (Kustomize)
├── .github/workflows/            # CI/CD pipeline
├── config/                       # Laravel configuration
├── database/                     # Migrations, seeders, factories
├── routes/                       # API, web, and console routes
├── tests/                        # PHPUnit and Dusk tests
└── docker-compose.yml            # Local development services
```

### Module Structure

Each domain module follows a consistent structure:

```
app/{Module}/
├── {Module}ServiceProvider.php   # Module registration and boot
├── Contracts/                    # Interface definitions
├── Database/
│   ├── migrations/               # Database migrations
│   └── Seeders/                  # Data seeders
├── Enums/                        # PHP enums
├── Events/                       # Domain events
├── Facades/                      # Facade implementations
├── Http/
│   ├── Controllers/              # Admin API controllers
│   │   └── Storefront/           # Storefront API controllers
│   └── Requests/                 # Form Request validation
├── Models/                       # Eloquent models
├── Repositories/                 # Repository interfaces + implementations
├── routes/
│   ├── admin.php                 # Admin API routes
│   └── api.php                   # Storefront API routes
└── Services/                     # Business logic
```

## Getting Started

### Prerequisites

- PHP 8.3 with extensions: pdo_mysql, redis, pcntl, intl, gd, zip, opcache, bcmath
- Composer 2
- Node.js 20 and npm
- MySQL 8.4 or MariaDB 11
- Redis 7
- Meilisearch 1.12 (optional, for search)

### Quick Start with Docker

The fastest way to get Quicktane running locally:

```bash
# Clone the repository
git clone https://github.com/Quicktane/quicktane.git
cd quicktane

# Start all services (app, MySQL, Redis, Meilisearch, Mailpit)
docker-compose up -d

# Run the setup script inside the container
docker exec -it quicktane-app composer run setup

# Run migrations and seed the database
docker exec -it quicktane-app php artisan migrate --seed

# Install frontend dependencies
docker exec -it quicktane-app bash -c "cd admin && npm install"
docker exec -it quicktane-app bash -c "cd storefront && npm install"
```

The application will be available at:

| Service | URL |
|---|---|
| API | http://localhost:8000 |
| Admin Panel | http://localhost:5174 |
| Storefront | http://localhost:5175 |
| Mailpit (email UI) | http://localhost:8025 |

### Manual Setup

```bash
# Clone and install dependencies
git clone https://github.com/Quicktane/quicktane.git
cd quicktane
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Update .env with your database, Redis, and Meilisearch credentials

# Run migrations and seed
php artisan migrate --seed

# Install frontend dependencies
npm install
cd admin && npm install && cd ..
cd storefront && npm install && cd ..
```

### Running the Application

Start all services with a single command:

```bash
composer run dev
```

This starts concurrently:
- Laravel dev server on port 8000
- Queue worker for background jobs
- Real-time log viewer (Pail)
- Admin SPA dev server on port 5174
- Storefront SPA dev server on port 5175

### Default Credentials

After running the seeders, you can log in to the Admin Panel with:

| Field | Value |
|---|---|
| Email | `admin@quicktane.local` |
| Password | `password` |

## API Reference

All API endpoints are versioned under `/api/v1/`. Authentication is handled via Bearer tokens (Laravel Sanctum).

### Storefront API

Public and customer-authenticated endpoints:

| Endpoint Group | Path | Description |
|---|---|---|
| Catalog | `/api/v1/catalog/products` | Browse and search products |
| Catalog | `/api/v1/catalog/categories` | Browse categories |
| Cart | `/api/v1/cart` | Manage shopping cart |
| Checkout | `/api/v1/checkout` | Multi-step checkout process |
| Customer | `/api/v1/customer` | Registration, login, profile |
| Orders | `/api/v1/order` | View order history |
| Payment | `/api/v1/payment` | Available payment methods |
| Shipping | `/api/v1/shipping` | Available shipping methods |
| Search | `/api/v1/search` | Full-text product search |
| CMS | `/api/v1/cms` | Content pages and blocks |
| Store | `/api/v1/store` | Store configuration |

### Admin API

Requires authentication and role-based permissions:

| Endpoint Group | Path | Description |
|---|---|---|
| Auth | `/api/v1/admin/user` | Login, logout, user management |
| Catalog | `/api/v1/admin/catalog` | Product, category, attribute CRUD |
| Orders | `/api/v1/admin/order` | Order management |
| Customers | `/api/v1/admin/customer` | Customer management |
| Inventory | `/api/v1/admin/inventory` | Stock and source management |
| Shipping | `/api/v1/admin/shipping` | Shipping configuration |
| Tax | `/api/v1/admin/tax` | Tax configuration |
| Promotions | `/api/v1/admin/promotion` | Rules and coupon management |
| CMS | `/api/v1/admin/cms` | Content management |
| Store | `/api/v1/admin/store` | Store configuration |
| System | `/api/v1/admin/system` | Modules, maintenance mode |
| Media | `/api/v1/admin/media` | File uploads |

### Authentication

Quicktane uses **dual authentication** via Laravel Sanctum:

- **Admin users** authenticate via `POST /api/v1/admin/user/login` and receive a Bearer token. Access is controlled by role-based permissions.
- **Customers** authenticate via `POST /api/v1/customer/login` or register via `POST /api/v1/customer/register`. Customer routes are protected by the `customer` middleware.
- **Guest cart** tracking uses an `X-Cart-Token` header for anonymous shoppers.

### Health Check

```
GET /api/health
```

Returns application status, Octane availability, and server timestamp.

## Architecture

### Module System

Quicktane is built around a two-tier module system:

**Platform packages** (`packages/`) are Composer path repositories that provide core infrastructure and cross-cutting features (CMS, inventory, shipping, tax, promotions, search, media, notifications). These follow standard Composer package conventions.

**Domain modules** (`app/`) contain the primary e-commerce business logic (catalog, cart, checkout, orders, payments, customers, store, users). These live directly in the application and are auto-discovered by the core.

Every module extends `ModuleServiceProvider` and can define:
- Database migrations and seeders
- API routes (admin and storefront)
- Configuration schemas
- Admin menu items
- Scheduled tasks
- Versioned upgrade scripts

Modules register their dependencies and can be installed, uninstalled, and managed via Artisan commands:

```bash
php artisan module:status        # List all modules and their status
php artisan module:install       # Install a module
php artisan module:uninstall     # Uninstall a module
```

### Repository and Facade Patterns

Each module uses a **repository pattern** with interface-based bindings:

```php
// Interface
interface ProductRepository {
    public function findBySku(string $sku): ?Product;
}

// Implementation bound in the service provider
$this->app->bind(ProductRepository::class, MysqlProductRepository::class);
```

Modules also expose **facade classes** that aggregate common operations behind a clean interface. Any binding can be swapped via `config/modules.php` replacements, allowing full customization without modifying module code.

### Checkout Pipeline

The checkout process is implemented as a **multi-step pipeline** with built-in support for suspension and resumption. This is critical for payment gateways that require external redirects (3DS verification, PayPal, etc.).

The pipeline steps execute in priority order across modules:

1. **ValidateCart** -- verify cart contents
2. **CalculateSubtotal** -- sum line items
3. **ApplyDiscount** -- apply promotion rules and coupons
4. **CalculateShipping** -- determine shipping cost
5. **CalculateTax** -- calculate applicable taxes
6. **CalculateGrandTotal** -- compute final total
7. **ValidateStock** -- verify inventory availability
8. **ReserveInventory** -- reserve stock for the order
9. **AuthorizePayment** -- process payment (may suspend for gateway redirect)
10. **CreateOrder** -- generate the order record
11. **RecordPromotionUsage** -- track coupon and rule usage
12. **ConvertCart** -- clean up the cart after successful order

If a step fails, the pipeline runs **compensation** (rollback) on previously completed steps. If a step suspends (e.g., waiting for a payment gateway callback), the pipeline state is persisted to Redis and can be resumed later.

Modules can register additional steps or replace existing ones:

```php
PipelineRegistry::replace('checkout.place', CalculateTax::class, CustomTaxStep::class);
```

## Testing

Quicktane uses PHPUnit for testing with SQLite in-memory databases for speed.

```bash
# Run all tests
php artisan test --compact

# Run a specific test file
php artisan test --compact tests/Feature/ExampleTest.php

# Run tests matching a name
php artisan test --compact --filter=testProductCreation

# Run browser tests (requires Chrome)
php artisan dusk
```

## Deployment

### Docker Images

Quicktane ships with four production Docker images, all targeting `linux/amd64`:

| Image | Description |
|---|---|
| `ghcr.io/quicktane/quicktane/php-base` | Base image with FrankenPHP and PHP extensions |
| `ghcr.io/quicktane/quicktane/api` | Laravel API server (Octane) |
| `ghcr.io/quicktane/quicktane/storefront` | Storefront SPA served by nginx |
| `ghcr.io/quicktane/quicktane/admin` | Admin SPA served by nginx |

Build all images locally:

```bash
# Build base image
docker buildx build -f .docker/images/php-base/Dockerfile -t ghcr.io/quicktane/quicktane/php-base:latest .docker/images/php-base

# Build API image
docker buildx build -f .docker/images/api/Dockerfile --build-arg PHP_BASE_IMAGE=ghcr.io/quicktane/quicktane/php-base:latest -t ghcr.io/quicktane/quicktane/api:latest .

# Build Storefront image
docker buildx build -f .docker/images/storefront/Dockerfile --build-arg VITE_API_URL=https://api.your-domain.com -t ghcr.io/quicktane/quicktane/storefront:latest .

# Build Admin image
docker buildx build -f .docker/images/admin/Dockerfile --build-arg VITE_API_URL=https://api.your-domain.com -t ghcr.io/quicktane/quicktane/admin:latest .
```

### Kubernetes

Production Kubernetes manifests are in `.kubernetes/production/` and managed with Kustomize. The deployment includes:

| Component | Type | Description |
|---|---|---|
| API | Deployment (2 replicas) | Laravel Octane on FrankenPHP |
| Queue Worker | Deployment (1 replica) | Background job processing |
| Storefront | Deployment (1 replica) | Nginx serving the storefront SPA |
| Admin | Deployment (1 replica) | Nginx serving the admin SPA |
| MariaDB | StatefulSet (1 replica) | Database with Longhorn persistent storage |
| Redis | Deployment (1 replica) | Cache, sessions, and queues |
| Meilisearch | Deployment (1 replica) | Search engine |
| Ingress | Ingress (nginx) | Routes traffic to services by hostname and path |

#### Initial Setup

```bash
# Create the namespace
kubectl apply -f .kubernetes/production/namespace.yaml

# Create secrets (copy and fill in the example first)
cp .kubernetes/production/secrets.yaml.example .kubernetes/production/secrets.yaml
# Edit secrets.yaml with your actual values (APP_KEY, DB_PASSWORD, etc.)
kubectl apply -f .kubernetes/production/secrets.yaml

# Create the ghcr.io pull secret
kubectl create secret docker-registry ghcr-credentials \
  --namespace=quicktane \
  --docker-server=ghcr.io \
  --docker-username=YOUR_USERNAME \
  --docker-password=YOUR_GITHUB_PAT

# Deploy everything
kubectl apply -k .kubernetes/production

# Run migrations and seeders
kubectl -n quicktane exec deployment/api -- php artisan migrate --seed --force
```

#### Manual Deploy Script

A convenience script handles the full build, push, and deploy cycle:

```bash
.kubernetes/deploy.sh v1.0.0
```

### CI/CD with GitHub Actions

Automated deployment is triggered by **publishing a GitHub release** from the `main` branch.

The pipeline:
1. Builds and pushes all Docker images to `ghcr.io` (in parallel where possible)
2. Applies Kubernetes manifests
3. Waits for infrastructure pods (MariaDB, Redis, Meilisearch)
4. Runs database migrations and seeders
5. Performs a rolling update of all application deployments
6. Verifies the rollout

#### Required Setup

1. **GitHub Environment**: Create a `DEMO` environment in your repository settings
2. **GitHub Secret**: Add `KUBE_CONFIG` (base64-encoded kubeconfig) to the `DEMO` environment
3. **Package Permissions**: Grant the repository write access to each container package in GitHub Container Registry

#### Creating a Release

```bash
git tag v1.0.0
git push origin v1.0.0
```

Then go to GitHub and create a release from the tag. The pipeline starts automatically.

## Contributing

### Code Style

PHP code is formatted with [Laravel Pint](https://laravel.com/docs/pint). Run the formatter before submitting changes:

```bash
vendor/bin/pint --dirty
```

### Conventions

- Use explicit return type declarations and parameter type hints on all methods
- Use PHP 8 constructor property promotion
- Create Form Request classes for validation rather than inline validation in controllers
- Use the repository pattern for data access
- Write PHPUnit feature tests for new functionality
- Follow existing module structure when adding new domain modules
- Use `php artisan make:` commands to scaffold new files

### Running Tests Before Submitting

```bash
# Format code
vendor/bin/pint --dirty

# Run tests
php artisan test --compact
```

## License

Quicktane is proprietary software. See the [LICENSE](LICENSE) file for details.
