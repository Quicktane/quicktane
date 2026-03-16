# Quicktane

E-commerce platform inspired by Magento, built on Laravel 12 + Octane for high performance.

## Project Vision

Modular, extensible e-commerce platform with:
- **Laravel Octane** (Swoole/RoadRunner) for persistent worker performance
- **Modular architecture** — features organized as independent modules (like Magento modules)
- **Multi-store support** — multiple storefronts from a single installation
- **Admin panel + Storefront** — separated admin API/UI and customer-facing storefront
- **EAV-like flexible attributes** for products (configurable product attributes without migrations)
- **Plugin/extension system** — third-party extensions via Composer packages

## Tech Stack

- **Backend:** PHP 8.2+, Laravel 12, Laravel Octane
- **Database:** MySQL/PostgreSQL (primary), Redis (cache/sessions/queues)
- **Search:** Meilisearch or Elasticsearch for product catalog
- **Queue:** Laravel Queue with Redis driver
- **Admin Frontend:** React + TypeScript + shadcn/ui + Tailwind CSS (standalone SPA, communicates via REST API)
- **Storefront:** API-first (REST + optional GraphQL), headless
- **Assets:** Vite

## Architecture — Monorepo + Composer Packages

**Monorepo** — all packages live in `packages/`, each is an independent Composer package
that can be split into its own repository via `splitsh/lite` or GitHub Actions.

Root `composer.json` uses `"repositories": [{"type": "path", "url": "packages/*"}]`
to symlink packages during development. In production, packages are installed from Packagist.

### Core (`quicktane/core`)

Infrastructure only, zero business logic. Namespace: `Quicktane\Core\`

```
packages/core/
├── composer.json
├── src/
│   ├── CoreServiceProvider.php
│   ├── Events/         # Custom EventDispatcher, OperationContext, OperationBlockedException
│   ├── Trace/          # OperationTracer (Redis in-flight → DB on completion)
│   ├── Pipeline/       # Pipeline (with suspend/resume), PipelineStep, Registry, State storage
│   ├── Module/         # ModuleRegistry, ModuleServiceProvider, ModuleReplacer
│   └── Http/
├── database/migrations/
└── routes/
```

### Modules

Each domain is a Composer package under `packages/`. Namespace: `Quicktane\{Module}\`

```
packages/
│ Tier 1 — Foundation
├── store/            # quicktane/module-store — Multi-store config, websites, store views
├── directory/        # quicktane/module-directory — Countries, regions, currencies, units
├── user/             # quicktane/module-user — Admin auth, roles, ACL
│
│ Tier 2 — Core Commerce
├── catalog/          # quicktane/module-catalog — Products, categories, attributes (EAV), pricing
├── inventory/        # quicktane/module-inventory — Stock, multi-source, reservations
├── customer/         # quicktane/module-customer — Accounts, addresses, groups, segments
│
│ Tier 3 — Purchase Flow
├── cart/             # quicktane/module-cart — Shopping cart, guest/auth carts
├── promotion/        # quicktane/module-promotion — Cart price rules, coupons, conditions engine
├── tax/              # quicktane/module-tax — Tax rules, rates, zones, classes
├── shipping/         # quicktane/module-shipping — Shipping abstraction, rates, tracking
├── payment/          # quicktane/module-payment — Payment abstraction, transactions
├── checkout/         # quicktane/module-checkout — Thin orchestrator for purchase flow
├── order/            # quicktane/module-order — Orders, state machine, invoices, credit memos
│
│ Tier 4 — Content & Search
├── cms/              # quicktane/module-cms — Pages, blocks, URL rewrites
├── media/            # quicktane/module-media — File upload, storage, image processing
├── search/           # quicktane/module-search — Meilisearch/ES indexing, facets, autocomplete
└── notification/     # quicktane/module-notification — Centralized emails, templates, notification log
```

Each module package follows this structure:
```
packages/catalog/
├── composer.json                          # quicktane/module-catalog, deps, PSR-4 autoload
├── src/                                   # PHP source (Quicktane\Catalog\ namespace)
│   ├── CatalogServiceProvider.php
│   ├── Facades/                           # Concrete implementations
│   ├── Contracts/                         # Interfaces (clean names, no "Interface" suffix)
│   ├── Models/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Services/
│   ├── Repositories/
│   │   ├── ProductRepository.php          # Interface (clean name)
│   │   ├── MysqlProductRepository.php     # DB implementation
│   │   └── RedisProductRepository.php     # Cache-first implementation
│   ├── Events/
│   ├── Listeners/
│   └── Policies/
├── database/
│   ├── migrations/
│   └── Seeders/
├── routes/
│   ├── api.php
│   └── admin.php
└── config/
```

### Module system
- Modules registered via `config/modules.php` — activate, deactivate, replace
- **Local modules** in `app/Modules/{Name}/` — auto-discovered, no Composer package needed
- Modules interact through **facade interfaces** from `Contracts/` (injected via DI)
- Ordered flows (checkout, refund) use **Pipeline** — modules register steps, flow assembles automatically
- Business operations dispatch `Before{Op}` / `After{Op}` events
- Any module can be **fully replaced** — new module implements same `Contracts/`, swap in config
- Required deps → facade injection. Optional deps → events or `ModuleRegistry::has()`
- Full architecture rules: `.claude/architecture-rules.md`

### Local modules (`app/`)
- Project-specific modules live directly in `app/{ModuleName}/`
- Namespace: `App\{ModuleName}\` (autoloaded via root `"App\\": "app/"`)
- Extend `Quicktane\Core\Module\LocalModuleServiceProvider` (not `ModuleServiceProvider`)
- No `src/` subdirectory — PHP files at module root
- Auto-discovered from `config('modules.local_path')`, registered after platform modules
- Same internal structure as platform modules (Contracts/, Facades/, Models/, etc.)

## Admin Frontend Architecture

Standalone React SPA communicating with Laravel backend via REST API.
Admin API routes are prefixed with `/api/v1/admin/`, authenticated via Sanctum tokens.

```
admin/
├── src/
│   ├── main.tsx                 # App entry point
│   ├── App.tsx                  # Router setup
│   ├── layouts/
│   │   └── AdminLayout.tsx      # Sidebar + header shell
│   ├── pages/                   # Route pages
│   │   ├── Dashboard.tsx
│   │   ├── Catalog/
│   │   │   ├── Products/
│   │   │   │   ├── Index.tsx
│   │   │   │   ├── Create.tsx
│   │   │   │   └── Edit.tsx
│   │   │   └── Categories/
│   │   ├── Orders/
│   │   ├── Customers/
│   │   └── Settings/
│   ├── components/              # Shared admin components
│   │   ├── ui/                  # shadcn/ui components (auto-generated)
│   │   ├── data-table/          # Reusable data tables
│   │   └── forms/               # Reusable form patterns
│   ├── hooks/                   # Custom React hooks
│   ├── lib/
│   │   ├── utils.ts             # cn() helper, shared utilities
│   │   └── api.ts               # Axios/fetch wrapper for REST API
│   └── types/                   # TypeScript types/interfaces
├── index.html
├── tailwind.config.ts
├── tsconfig.json
├── vite.config.ts
├── components.json              # shadcn/ui config
└── package.json
```

### Admin Frontend Conventions
- **shadcn/ui** components live in `admin/src/components/ui/`
- Use `cn()` utility from `lib/utils.ts` for conditional classes
- REST API calls via centralized `lib/api.ts` wrapper (axios with Sanctum auth)
- React Router for client-side routing
- Data tables use TanStack Table + shadcn DataTable pattern
- All components are TypeScript (`.tsx`), no `.jsx`
- Types/interfaces defined alongside page components or in `types/`
- Do NOT write tests — testing is disabled for now

## Conventions

### PHP Rules
- `declare(strict_types=1);` in every file
- PSR-12 + Laravel Pint defaults (`./vendor/bin/pint` before committing)
- Return types on all methods
- Type hints on all parameters
- No `mixed` unless absolutely unavoidable
- Variable names must be full, unabbreviated:
  - `$productRepository` — NOT `$productRepo`
  - `$pricingService` — NOT `$pricingSvc`
  - `$catalogFacade` — NOT `$catalog`
  - `$cacheManager` — NOT `$cache`
  - `$orderItem` — NOT `$oi`
  - This applies to constructor properties, method parameters, and local variables

### Naming

| Entity | Pattern | Example |
|--------|---------|---------|
| Model | Singular PascalCase | `Product`, `OrderItem` |
| Table | Plural snake_case | `products`, `order_items` |
| Controller | `{Model}Controller` | `ProductController` |
| Facade (interface) | `{Domain}Facade` in `Contracts/` | `ProductFacade`, `CategoryFacade` |
| Facade (implementation) | `{Domain}Facade` in `Facades/` | `ProductFacade`, `CategoryFacade` |
| Service | `{Domain}Service` | `CartService`, `PricingService` |
| Repository (interface) | `{Model}Repository` | `ProductRepository` |
| Repository (MySQL impl) | `Mysql{Model}Repository` | `MysqlProductRepository` |
| Repository (Redis impl) | `Redis{Model}Repository` | `RedisProductRepository` |
| Before-event | `Before{Operation}` | `BeforeOrderPlace`, `BeforeProductCreate` |
| After-event | `After{Operation}` | `AfterOrderPlace`, `AfterProductCreate` |
| Listener | Action verb | `CheckStockListener`, `SendOrderEmailListener` |
| Pipeline step | `{Action}Step` | `CheckStockStep`, `AuthorizePaymentStep` |
| Pipeline name | `{domain}.{action}` | `checkout`, `order.refund` |
| State Machine | `{Model}StateMachine` | `OrderStateMachine` |
| Status Enum | `{Model}Status` | `OrderStatus` |
| FormRequest | `{Action}{Model}Request` | `StoreProductRequest`, `UpdateOrderRequest` |
| API Resource | `{Model}Resource` | `ProductResource`, `OrderResource` |
| Route prefix (api) | `/api/v1/{module-kebab}` | `/api/v1/catalog` |
| Route prefix (admin) | `/api/v1/admin/{module-kebab}` | `/api/v1/admin/catalog` |
| Cache key | `{module}:{entity}:{id}` | `catalog:product:42` |

### Database
- All schema changes via migrations only
- Foreign keys and indexes explicitly defined
- UUIDs for public-facing identifiers (API responses, URLs)
- Auto-increment IDs for internal use (relations, queries)
- Soft deletes on: orders, customers, products
- Timestamps (`created_at`, `updated_at`) on all tables
- Migration files live inside the package: `packages/{name}/database/migrations/`

### Octane Compatibility
- No singletons that store request-scoped data
- No static mutable properties on classes
- No file-based sessions or cache — must use Redis
- No storing request/response objects in service properties
- Use scoped bindings (`$this->app->scoped(...)`) for request-scoped services
- Stateless services preferred
- Use `Octane::concurrently()` for parallel I/O (shipping rates, payment checks)

### API Design
- RESTful resource routes
- JSON responses via API Resources (no raw arrays/models)
- Consistent error format: `{ "message": "...", "errors": { "field": ["..."] } }`
- Pagination on all list endpoints (cursor or offset)
- Versioned: `/api/v1/...`
- Auth: Laravel Sanctum tokens for admin, optional for storefront

### Testing
- **Disabled for now** — do not write or generate tests

### Git
- Commit messages in English, imperative mood
- Branch naming: `feature/module-name-description`, `fix/short-description`

## What NOT to do

- Do not write or generate tests
- Do not put logic in Controllers — delegate to Services/Facades
- Do not use Eloquent queries outside Repositories — no `Model::where()` in Services, Controllers, Facades
- Do not use `Cache::get/put/remember` outside Repositories — all caching lives in Repositories
- Do not import another module's internals — use its facade interface from `Contracts/`
- Do not inject concrete Facade (from `Facades/`) — always inject the interface (from `Contracts/`)
- Do not inject concrete Repository (e.g., `MysqlProductRepository`) — always inject the interface
- Do not create helpers/utils for one-time operations
- Do not add comments for obvious code
- Do not add unused imports or dead code
- Do not create README or documentation files unless explicitly asked

## Agents

Always use subagents when the task matches their specialization. Do not do the work inline if an appropriate agent exists — delegate to it.

| Agent | When to use |
|-------|-------------|
| **frontend** | Any admin panel UI work: pages, components, hooks, API integration, routing, styling. Always delegate React/TypeScript/Tailwind work to this agent. |
| **tester** | After making changes that affect API endpoints or admin UI. Run this agent to verify everything works. |
| **Explore** | When you need to search the codebase, find files, understand how something works. Use for any non-trivial code exploration. |
| **Plan** | Before starting complex multi-step tasks. Use to design implementation strategy and identify affected files. |

### Rules
- **Prefer agents over doing work inline** — if a task matches an agent's description, delegate it
- **Always run agents in background** (`run_in_background: true`) — do not block the main conversation
- **Run agents in parallel** when tasks are independent (e.g., frontend agent for UI + backend work inline)
- **Always run tester** after completing a feature or fix that touches API or frontend
- **Use Explore** instead of manually searching when you need to understand unfamiliar parts of the codebase
- **Use Plan** before implementing features that span multiple modules or require architectural decisions

## Key Commands

```bash
composer setup          # Initial project setup
composer dev            # Start dev server (app + queue + logs + vite)
./vendor/bin/pint       # Fix code style
php artisan octane:start  # Start Octane server (production)
```

## Language

- Code, comments, commits, docs — English
- UI translations via Laravel localization (`lang/` directory)

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3.9
- laravel/framework (LARAVEL) - v12
- laravel/octane (OCTANE) - v2
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- laravel/boost (BOOST) - v2
- laravel/dusk (DUSK) - v8
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan Commands

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`, `php artisan tinker --execute "..."`).
- Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.

## URLs

- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Debugging

- Use the `database-query` tool when you only need to read from the database.
- Use the `database-schema` tool to inspect table structure before writing migrations or models.
- To execute PHP code for debugging, run `php artisan tinker --execute "your code here"` directly.
- To read configuration values, read the config files directly or run `php artisan config:show [key]`.
- To inspect routes, run `php artisan route:list` directly.
- To check environment variables, read the `.env` file directly.

## Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before trying other approaches when working with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries at once. For example: `['rate limiting', 'routing rate limiting', 'routing']`. The most relevant results will be returned first.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.

## Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
    - `public function __construct(public GitHub $github) { }`
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

## Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<!-- Explicit Return Types and Method Params -->
```php
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
```

## Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

## Comments

- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless the logic is exceptionally complex.

## PHPDoc Blocks

- Add useful array shape type definitions when appropriate.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

## Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

### APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

## Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

## Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app\Console\Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== octane/core rules ===

# Octane

- Octane boots the application once and reuses it across requests, so singletons persist between requests.
- The Laravel container's `scoped` method may be used as a safe alternative to `singleton`.
- Never inject the container, request, or config repository into a singleton's constructor; use a resolver closure or `bind()` instead:

```php
// Bad
$this->app->singleton(Service::class, fn (Application $app) => new Service($app['request']));

// Good
$this->app->singleton(Service::class, fn () => new Service(fn () => request()));
```

- Never append to static properties, as they accumulate in memory across requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

</laravel-boost-guidelines>
