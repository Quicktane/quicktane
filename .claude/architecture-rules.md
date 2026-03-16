# Quicktane Architecture Rules

Rules referenced by `/review` and other architect commands.
Only architectural patterns and structural decisions live here.
Coding conventions, naming, and style rules → `CLAUDE.md`.
Module-specific implementation details → `MODULE.md` in each module.

---

## 1. Module Structure

### Location
- Platform modules live in Composer packages under `packages/{module-name}/`
- Local (project-specific) modules live in `app/{ModuleName}/`
- Infrastructure/framework code lives in `packages/core/`
- No business logic in `app/Http/`, `app/Models/`, or other top-level Laravel dirs (use `app/Modules/` instead)
- Each package has its own `composer.json` and can be split into a separate repository

### Core infrastructure (`packages/core/`)

Core provides ONLY infrastructure for module interaction. Zero business logic.
It does not know what a Product, Order, or Payment is. Namespace: `Quicktane\Core\`

```
packages/core/
├── composer.json                        # quicktane/core
├── src/
│   ├── CoreServiceProvider.php          # Registers core bindings, reads config/modules.php
│   ├── Events/
│   │   ├── EventDispatcher.php          # Custom dispatcher with tracing + priority support
│   │   ├── OperationContext.php         # Mutable DTO passed to Before-events
│   │   └── OperationBlockedException.php
│   ├── Trace/
│   │   ├── OperationTracer.php          # Manages trace lifecycle (start → record → persist)
│   │   ├── TraceEntry.php               # Single trace entry (event, listener, action)
│   │   ├── TraceStorage.php             # Interface for trace persistence
│   │   ├── RedisTraceStorage.php        # In-flight storage (during operation)
│   │   └── DatabaseTraceStorage.php     # Final storage (after operation completes)
│   ├── Pipeline/
│   │   ├── Pipeline.php                 # Executes ordered steps, handles suspend/resume, nested detection
│   │   ├── PipelineStep.php             # Interface for a step (handle + compensate)
│   │   ├── PipelineRegistry.php         # Collects steps from modules, sorts, replaces
│   │   ├── PipelineContext.php          # Mutable data container passed between steps
│   │   ├── PipelineState.php            # Serializable snapshot for suspended pipelines
│   │   ├── PipelineSuspendException.php # Thrown by step to signal suspension
│   │   ├── PipelineStateStorage.php     # Interface for state persistence
│   │   ├── RedisPipelineStateStorage.php # Active state storage with TTL
│   │   ├── SuspendedPipeline.php        # Eloquent model for admin visibility (DB)
│   │   └── PipelineManager.php          # Force-complete, maintenance mode integration
│   └── Module/
│       ├── ModuleRegistry.php           # Tracks which modules are registered
│       ├── ModuleServiceProvider.php    # Abstract base provider for all modules
│       └── ModuleReplacer.php           # Mechanism to replace facades/modules via config
├── database/migrations/                 # Core infrastructure migrations (operation_traces table)
└── routes/
```

Core is NOT a module — it has no routes, no API, no facades. It provides infrastructure
that modules depend on. Auto-discovered via Laravel package discovery.

### Module registration (`config/modules.php`)

All modules are registered via config. This is where modules are activated, replaced, or disabled.

```php
// config/modules.php
return [
    // Active modules — order matters (dependencies first)
    'modules' => [
        'store'        => \Quicktane\Store\StoreServiceProvider::class,
        'directory'    => \Quicktane\Directory\DirectoryServiceProvider::class,
        'user'         => \Quicktane\User\UserServiceProvider::class,
        'catalog'      => \Quicktane\Catalog\CatalogServiceProvider::class,
        'inventory'    => \Quicktane\Inventory\InventoryServiceProvider::class,
        'customer'     => \Quicktane\Customer\CustomerServiceProvider::class,
        'cart'         => \Quicktane\Cart\CartServiceProvider::class,
        'promotion'    => \Quicktane\Promotion\PromotionServiceProvider::class,
        'tax'          => \Quicktane\Tax\TaxServiceProvider::class,
        'shipping'     => \Quicktane\Shipping\ShippingServiceProvider::class,
        'payment'      => \Quicktane\Payment\PaymentServiceProvider::class,
        'checkout'     => \Quicktane\Checkout\CheckoutServiceProvider::class,
        'order'        => \Quicktane\Order\OrderServiceProvider::class,
        // ... add or remove modules as needed
    ],

    // Directory for project-specific local modules (auto-discovered)
    'local_path' => app_path('Modules'),

    // Replace specific facade bindings (interface → new implementation)
    'replacements' => [
        // \Quicktane\Payment\Contracts\PaymentFacade::class
        //     => \Quicktane\StripePayment\Facades\PaymentFacade::class,
    ],

    // Replace pipeline steps
    'pipeline_replacements' => [
        // 'checkout' => [
        //     \Quicktane\Inventory\Steps\CheckStockStep::class
        //         => \Quicktane\Warehouse\Steps\CheckMultiSourceStockStep::class,
        // ],
    ],
];
```

### Local modules (`app/`)

For project-specific customizations that don't need to be Composer packages.
Auto-discovered from `config('modules.local_path')` — registered AFTER all platform modules.

```
app/{ModuleName}/
├── {ModuleName}ServiceProvider.php    # extends LocalModuleServiceProvider
├── Contracts/
├── Facades/
├── Models/
├── Http/Controllers/
├── Services/
├── Repositories/
├── Events/
├── Listeners/
├── database/migrations/
├── routes/
│   ├── api.php
│   └── admin.php
└── config/
```

Key differences from platform modules:
- Namespace: `App\{ModuleName}\` (not `Quicktane\`)
- Extends `LocalModuleServiceProvider` (not `ModuleServiceProvider`)
- No `composer.json` — autoloaded via root `"App\\": "app/"`
- No `src/` subdirectory — PHP files live at the module root
- Auto-discovered — no need to edit `config/modules.php`

### Module replacement

**Three levels of customization:**

1. **Replace a facade** — swap one facade implementation, keep the rest of the module
   ```php
   'replacements' => [
       Payment\Contracts\PaymentFacade::class => StripePayment\Facades\PaymentFacade::class,
   ]
   ```

2. **Replace a full module** — swap the entire ServiceProvider in `modules` list
   ```php
   'modules' => [
       'payment' => \Quicktane\StripePayment\StripePaymentServiceProvider::class,
   ]
   ```

3. **Extend via events/listeners** — add behavior without replacing anything

A replacement module MUST implement the same `Contracts/` interfaces as the module it replaces.

### Optional dependencies between modules

- **Required** — inject facade interface via constructor. If module not installed, app fails at boot (intentional).
- **Optional** — use events. Module dispatches event, if nobody listens, flow continues.
  Alternatively, check `ModuleRegistry::has('inventory')` for conditional logic.

### Required internal structure per module
```
packages/{module-name}/src/
├── Facades/
│   ├── {Domain}Facade.php               # Concrete implementation
│   └── ...                              # Multiple facades per module allowed
├── Contracts/
│   ├── {Domain}Facade.php               # Interface (clean name, same as concept)
│   └── ...                              # One interface per facade
├── Models/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
├── Services/
├── Repositories/
│   ├── {Model}Repository.php              # Interface (clean name)
│   ├── Mysql{Model}Repository.php         # MySQL/Eloquent implementation
│   └── Redis{Model}Repository.php         # Redis/Cache implementation (if needed)
├── Events/
├── Listeners/
├── Steps/                                 # Pipeline steps (if module participates in flows)
├── Policies/
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   ├── api.php        → prefix /api/v1/{module-kebab}
│   └── admin.php      → prefix /api/v1/admin/{module-kebab}
├── config/
└── {ModuleName}ServiceProvider.php
```

Empty dirs can be omitted until needed, but ServiceProvider, Facades/, Contracts/ and routes/ are mandatory.

### ServiceProvider must
- Extend `packages/core/src/Module/ModuleServiceProvider` (abstract base)
- Bind each facade interface to its implementation: `Contracts\{Domain}Facade` → `Facades\{Domain}Facade`
- Bind repository interfaces to implementations: `{Model}Repository` → `Mysql{Model}Repository` (or decorator)
- Register pipeline steps into `PipelineRegistry` (if module participates in flows)
- Register routes (api.php, admin.php)
- Register migrations
- Register config (if exists)
- Be listed in `config/modules.php` (NOT directly in `bootstrap/providers.php`)

---

## 2. Module Communication & Boundaries

### Facade pattern (module public API)

Facades are the ONLY entry point for other modules. A module can have **multiple facades**,
each covering a different domain area. Every facade MUST implement a corresponding interface.

**Interface** = clean name in `Contracts/` (e.g., `ProductFacade`)
**Implementation** = same name in `Facades/` (e.g., `ProductFacade`)
Namespace difference distinguishes them: `Contracts\ProductFacade` vs `Facades\ProductFacade`.

```
┌──────────────────┐         ┌───────────────────────────────┐
│   Order Module   │         │       Catalog Module          │
│                  │         │                               │
│  OrderService    │────────▶│  Contracts\ProductFacade      │ (interface)
│                  │         │         ▲                     │
│                  │         │         │ implements          │
│                  │         │  Facades\ProductFacade        │
│                  │         │     ├── ProductRepository     │
│                  │         │     └── PricingService        │
│                  │         │                               │
│  OrderService    │────────▶│  Contracts\CategoryFacade     │ (interface)
│                  │         │         ▲                     │
│                  │         │         │ implements          │
│                  │         │  Facades\CategoryFacade       │
│                  │         │     └── CategoryRepository    │
└──────────────────┘         └───────────────────────────────┘
```

**Facade rules:**
- A module can expose multiple facades (e.g., `ProductFacade`, `CategoryFacade`, `PricingFacade`)
- Interface lives in `Contracts/` with a clean name — NO `Interface` suffix
- Implementation lives in `Facades/` with the same name
- Can call any internal service, repository, or model within its own module
- Methods should be domain-oriented (`getProductWithPrice(int $id)`) not CRUD-oriented
- Injected into other modules via the **interface** (from `Contracts/`), never the concrete class

**Example:**
```php
// packages/catalog/src/Contracts/ProductFacade.php — INTERFACE
namespace Quicktane\Catalog\Contracts;

interface ProductFacade
{
    public function findProduct(int $id): ?ProductData;
    public function checkAvailability(array $productIds): array;
}

// packages/catalog/src/Facades/ProductFacade.php — IMPLEMENTATION
namespace Quicktane\Catalog\Facades;

use Quicktane\Catalog\Contracts\ProductFacade as ProductFacadeContract;

class ProductFacade implements ProductFacadeContract
{
    public function __construct(
        private ProductRepository $productRepository,
        private PricingService $pricingService,
    ) {}
    // ...
}

// packages/order/src/Services/OrderService.php — uses Catalog via interface
use Quicktane\Catalog\Contracts\ProductFacade;

class OrderService
{
    public function __construct(
        private ProductFacade $productFacade,  // ← interface from Contracts\
    ) {}
}
```

### Allowed cross-module communication
- **Facade interface injection** — the primary way. Module A injects Module B's facade interface via constructor DI
- **Events/Listeners** — Module A dispatches an Event, Module B listens. For async/decoupled flows

### Forbidden
- Direct import of another module's Model, Repository, or Service
- Using a concrete Facade class instead of its interface
- Importing from `Facades\` namespace of another module — only `Contracts\`
- Circular facade dependencies (A→B→A) — resolve via Events or extract shared logic to a new module
- Accessing another module's database tables directly (queries, joins)

---

## 3. Event System & Operation Tracing

### Event types per business operation

Every core business operation dispatches two events:

| Event | Type | Can block | When |
|-------|------|-----------|------|
| `Before{Operation}` | always sync | yes (exception) | before the operation, inside DB transaction |
| `After{Operation}` | depends on listener | no | after the operation, inside DB transaction |

Listeners on `After{Operation}` follow standard Laravel behavior:
- Regular listener → executes synchronously, inside the transaction
- Listener `implements ShouldQueue` → dispatched to queue after DB commit (`$afterCommit = true`)

No need for a separate async event — Laravel handles this natively.

### Before-events (blocking)

Before-events receive a **mutable OperationContext** — a DTO that listeners can modify or block.

```php
class BeforeOrderPlace
{
    public function __construct(
        public readonly OperationContext $context,
    ) {}
}

// Listener that blocks operation
class CheckFraudListener
{
    public function handle(BeforeOrderPlace $event): void
    {
        if ($this->fraudService->isSuspicious($event->context->get('customer_id'))) {
            throw new OperationBlockedException(
                operation: 'order.place',
                reason: 'Fraud detected',
                blocker: self::class,
            );
        }
    }
}
```

### After-events

```php
class AfterOrderPlace
{
    public function __construct(
        public readonly Order $order,
    ) {}
}

// Sync listener — runs inside transaction
class ReserveStockListener
{
    public function handle(AfterOrderPlace $event): void
    {
        $this->inventoryService->reserve($event->order->items);
    }
}

// Async listener — queued after commit
class SendOrderEmailListener implements ShouldQueue
{
    public bool $afterCommit = true;

    public function handle(AfterOrderPlace $event): void
    {
        // runs in queue worker, not in the request
    }
}
```

### Listener priority

Listeners can optionally declare execution priority when order matters.

```php
class CheckStockListener
{
    public int $priority = 100; // higher = runs first

    public function handle(BeforeOrderPlace $event): void { /* ... */ }
}
```

- `$priority` is optional — defaults to `0`
- Higher priority value = runs first (100 before 50 before 0)
- Custom `EventDispatcher` in `packages/core/src/Events/` sorts listeners by priority before dispatching
- Priority is only relevant for sync listeners (async/queued listeners have no guaranteed order)
- Use sparingly — most listeners don't need ordering

### Service operation flow

```php
class OrderService
{
    public function placeOrder(array $orderData): Order
    {
        return $this->operationTracer->execute('order.place', function () use ($orderData) {
            // operationTracer wraps everything in DB::transaction

            $context = new OperationContext($orderData);
            event(new BeforeOrderPlace($context));       // sync, can block

            $order = $this->createOrder($context);

            event(new AfterOrderPlace($order));           // sync or async per listener

            return $order;
        });
    }
}
```

### Which operations need events

**Required** — core business operations:
- Order: place, cancel, refund, status change
- Product: create, update, delete, price change
- Customer: register, update, delete
- Cart: add item, remove item, apply coupon
- Payment: authorize, capture, void
- Inventory: reserve, release, adjust
- Checkout: start, complete

**Not required** — administrative/infrastructure:
- CMS page CRUD, Settings changes, Cache operations, Admin user management

### Event naming
- `Before{Operation}` / `After{Operation}` — operation-oriented, not model-oriented
- Operations are verb-based: `OrderPlace`, `ProductCreate`, `PaymentCapture`
- Events live in the module that owns the operation: `packages/order/src/Events/BeforeOrderPlace.php`

### Operation Tracing

The custom `EventDispatcher` (in `packages/core/src/Events/`) wraps Laravel's dispatcher and records
every event dispatch + listener execution into the `OperationTracer`.

**Lifecycle:**
1. `$operationTracer->execute('order.place', ...)` starts a trace with a UUID
2. During execution, trace entries are stored in **Redis** (fast writes, no DB overhead)
3. When the operation completes (success or failure), the full trace is flushed from Redis to **DB** (`operation_traces` table)
4. Redis entry is deleted after flush

**Trace entry contains:**
- `trace_id` (UUID) — groups all entries for one operation
- `operation` — e.g., `order.place`
- `type` — `event_dispatched`, `listener_started`, `listener_completed`, `listener_failed`, `operation_blocked`
- `class` — event or listener FQCN
- `duration_ms` — execution time
- `metadata` — JSON with context (error message, modified fields, etc.)
- `status` — `ok`, `failed`, `blocked`
- `created_at` — timestamp

**Octane safety:** `OperationTracer` is a scoped binding — fresh instance per request.

---

## 4. Pipeline Pattern

Pipelines are ordered step-based flows (like middleware). Core provides the infrastructure,
modules register their steps. The pipeline assembles automatically from installed modules.

### Core infrastructure (in `packages/core/src/Pipeline/`)

```php
// packages/core/src/Pipeline/PipelineStep.php — interface for every step
interface PipelineStep
{
    public function handle(PipelineContext $context, Closure $next): mixed;
    public function compensate(PipelineContext $context): void;  // rollback on failure/expiry
    public static function priority(): int;    // execution order (100, 200, 300...)
    public static function pipeline(): string; // which pipeline ('checkout', 'order.refund', etc.)
}
```

### How modules register steps

```php
// Inventory/InventoryServiceProvider.php
public function boot(): void
{
    $this->app->make(PipelineRegistry::class)
        ->register('checkout', CheckStockStep::class)
        ->register('checkout', ReserveStockStep::class);
}
```

### Pipeline assembles from installed modules

```
Minimal store (Cart + Payment + Order only):
  checkout pipeline:
    100 → ValidateCartStep           (Cart)
    800 → AuthorizePaymentStep       (Payment)
    900 → CreateOrderStep            (Order)

Standard store (+ Inventory, Shipping, Tax, Promotion):
  checkout pipeline:
    100 → ValidateCartStep           (Cart)
    200 → CheckStockStep             (Inventory)
    300 → CalculateShippingStep      (Shipping)
    400 → ApplyPromotionsStep        (Promotion)
    500 → CalculateTaxStep           (Tax)
    800 → AuthorizePaymentStep       (Payment)
    850 → ReserveStockStep           (Inventory)
    900 → CreateOrderStep            (Order)

Enterprise (+ Warehouse replaces Inventory steps):
    200 → CheckMultiSourceStockStep  (Warehouse, replaced CheckStockStep)
    850 → AllocateWarehouseStep      (Warehouse, replaced ReserveStockStep)
```

No module hardcodes the flow. Install a module → its steps appear in the pipeline.

### Step replacement

```php
// Warehouse module replaces Inventory steps
$this->app->make(PipelineRegistry::class)
    ->replace('checkout', CheckStockStep::class, CheckMultiSourceStockStep::class)
    ->replace('checkout', ReserveStockStep::class, AllocateWarehouseStep::class);
```

Or via `config/modules.php` `pipeline_replacements` section.

### Nested pipeline detection

Pipelines must NOT be nested. If a pipeline step tries to start another pipeline,
a runtime error is thrown immediately.

```php
class Pipeline
{
    private static bool $isExecuting = false;

    public function run(string $name, PipelineContext $context): PipelineResult
    {
        if (self::$isExecuting) {
            throw new NestedPipelineException(
                "Cannot start pipeline '{$name}' — already inside a pipeline execution. "
                . "Use events or direct service calls instead of nesting pipelines."
            );
        }

        self::$isExecuting = true;

        try {
            // ... execute steps
        } finally {
            self::$isExecuting = false;
        }
    }
}
```

- Static flag detects nesting even across different Pipeline instances
- `finally` ensures reset on exceptions
- Steps that need complex flows should use events or facade calls, not pipelines
- Octane safe: pipeline execution completes within one request

### Pipeline execution with tracing

```php
class CheckoutService
{
    public function placeOrder(array $orderData): Order
    {
        return $this->operationTracer->execute('checkout.place', function () use ($orderData) {
            $context = new PipelineContext($orderData);
            $this->pipeline->run('checkout', $context);
            return $context->get('order');
        });
    }
}
```

### Pipeline vs Events

| | Pipeline | Events |
|--|----------|--------|
| Purpose | Ordered flow of required steps | Hooks at specific moments |
| Order | Explicit via `priority()` | Optional via `$priority` property |
| Can stop flow | Yes (`$next` not called) | Yes (Before-event exception) |
| Used for | checkout, refund, import | before/after specific operations |
| Modularity | Module adds steps to flow | Module listens to events |

A pipeline step CAN dispatch events inside itself:
```php
class CreateOrderStep implements PipelineStep
{
    public function handle(PipelineContext $context, Closure $next): mixed
    {
        event(new BeforeOrderCreate($context));
        $order = $this->orderFacade->create($context->toArray());
        $context->set('order', $order);
        event(new AfterOrderCreate($order));
        return $next($context);
    }
}
```

### Pipeline Suspend / Resume

Some steps require external interaction (3D Secure redirect, PayPal, external approval).
The pipeline suspends, lets the user interact externally, and resumes when a callback/webhook arrives.

**Step signals suspension by throwing `PipelineSuspendException`:**

```php
class AuthorizePaymentStep implements PipelineStep
{
    public function handle(PipelineContext $context, Closure $next): mixed
    {
        $result = $this->paymentFacade->authorize($context->get('payment_data'));

        if ($result->requiresRedirect()) {
            throw new PipelineSuspendException(
                redirectUrl: $result->getRedirectUrl(),
                reason: '3D Secure confirmation required',
                metadata: ['transaction_id' => $result->getTransactionId()],
            );
        }

        $context->set('payment_transaction', $result);
        return $next($context);
    }
}
```

**Pipeline catches the exception and persists state:**

```php
// Inside Pipeline::run() — handled automatically by core
try {
    $step->handle($context, $next);
} catch (PipelineSuspendException $exception) {
    $state = new PipelineState(
        token: Str::uuid(),
        pipelineName: $this->name,
        completedSteps: $this->completedSteps,
        currentStepIndex: $this->currentIndex,
        context: $context->serialize(),
        metadata: $exception->metadata,
        reason: $exception->reason,
        expiresAt: now()->addMinutes($this->ttl),
    );

    $this->stateStorage->save($state);         // Redis with TTL
    SuspendedPipeline::create($state);          // DB for admin visibility

    return new PipelineSuspendResult(
        token: $state->token,
        redirectUrl: $exception->redirectUrl,
    );
}
```

### Resume flow

**For redirect-based (user returns to site):**
```
User → Checkout → Pipeline suspends → token: abc-123
  → Redirect to Stripe 3DS with return_url containing token
  → User confirms → Stripe redirects back
  → Controller calls Pipeline::resume('abc-123')
  → Pipeline loads state from Redis, continues from suspended step
  → Remaining steps complete → order created
```

**For webhook-based (async callback):**
```
Pipeline suspends → token in payment metadata
  → Stripe sends webhook with token
  → Webhook controller calls Pipeline::resume('abc-123')
```

### Webhook idempotency

External webhooks may be delivered multiple times. Pipeline resume must be idempotent.

```php
// Inside Pipeline::resume()
public function resume(string $token, array $callbackData = []): PipelineResult
{
    // Atomic lock — SELECT FOR UPDATE prevents concurrent processing
    $suspended = DB::transaction(function () use ($token) {
        $record = SuspendedPipeline::where('token', $token)
            ->lockForUpdate()
            ->first();

        if (!$record) {
            throw new PipelineNotFoundException($token);
        }

        if ($record->status !== 'suspended') {
            // Already resumed/completed/expired — return cached result
            return $record;
        }

        $record->update(['status' => 'resuming']);
        return $record;
    });

    if ($suspended->status === 'completed') {
        return PipelineResult::fromCache($suspended->result);
    }

    if (!in_array($suspended->status, ['resuming'])) {
        throw new PipelineAlreadyProcessedException($token, $suspended->status);
    }

    // Continue pipeline execution from current step...
}
```

- `SELECT FOR UPDATE` on `suspended_pipelines` row to prevent concurrent resume
- If already completed — return cached result (stored in `result` JSON column)
- If expired/failed — throw exception, don't reprocess

### Guest identification

Guests don't have a user ID. The **pipeline token** is the identifier:
- Token is a UUID, generated on suspend, included in the return URL
- Works identically for guests and authenticated customers
- Token is single-use — consumed on resume
- Cart token is stored inside PipelineContext, so the resumed pipeline
  can re-identify the guest's cart without a session

### Failure handling (3DS rejected, payment declined)

**1. Redirect return with failure status:**
```
Stripe redirects back with ?status=failed
  → Pipeline::resume('abc-123') with failure flag
  → PaymentStep sees failure → throws PaymentDeclinedException
  → Pipeline fails → compensation on completed steps → PipelineFailed event
  → Response to user: "Payment declined, please try again"
```

**2. No return (user closes browser):**
```
Pipeline state in Redis with TTL (e.g., 30 minutes)
  → TTL expires → Redis key deleted
  → Scheduler PipelineCleanup command finds expired DB entries
  → Updates status to 'expired' → dispatches PipelineExpired event
  → Inventory module listens → releases reservations
```

### Compensation on failure/expiry

On failure/expiry, Pipeline calls `compensate()` in reverse order on completed steps.

**TTL-based auto-release** as defense in depth:
Inventory reservations have their own TTL. If compensation fails, reservations auto-release anyway.

### TTL configuration

```php
// config/pipelines.php
return [
    'ttl' => [
        'checkout'       => 30,   // minutes — payment redirect timeout
        'order.refund'   => 1440, // 24 hours — approval workflows
        'quote.approve'  => 10080, // 7 days — B2B quote approval
    ],
    'cleanup_interval' => 1, // minutes — how often scheduler checks for expired

    // Steps where force-complete is NOT allowed during module swap
    'non_completable_steps' => [
        // \Quicktane\Payment\Steps\AuthorizePaymentStep::class,
        // \Quicktane\Payment\Steps\CapturePaymentStep::class,
    ],
];
```

### DB table: `suspended_pipelines` (for admin visibility)

```
id | token (uuid) | pipeline_name | status | customer_id (nullable) |
   guest_token (nullable) | context (json) | completed_steps (json) |
   current_step | reason | metadata (json) | result (json, nullable) |
   expires_at | created_at | updated_at
```

**Statuses:** `suspended`, `resumed`, `completed`, `expired`, `failed`, `force_completed`

### Admin API endpoints

- `GET /api/v1/admin/pipelines/suspended` — list all suspended/expired pipelines with filters
- `GET /api/v1/admin/pipelines/suspended/{token}` — detail with full context and trace
- `POST /api/v1/admin/pipelines/suspended/{token}/expire` — force-expire a pipeline

### Standard pipelines

- `checkout` — purchase flow
- `order.refund` — refund processing
- `order.fulfill` — fulfillment/shipping
- `product.import` — bulk product import
- `customer.register` — registration flow

---

## 5. Repository Pattern

### Purpose
Repositories are the ONLY layer that talks to the database and cache. No Eloquent queries
or Cache calls anywhere else (not in Controllers, Services, Facades, or Listeners).

### Structure per model

```php
// Interface (clean name — injected everywhere)
interface ProductRepository
{
    public function find(int $id): ?Product;
    public function create(array $data): Product;
    public function update(int $id, array $data): Product;
    public function delete(int $id): bool;
}

// DB implementation (driver prefix)
class MysqlProductRepository implements ProductRepository
{
    public function __construct(
        private Product $model,
    ) {}
}

// Cache decorator (wraps ANY ProductRepository, adds caching)
class RedisProductRepository implements ProductRepository
{
    public function __construct(
        private ProductRepository $productRepository,  // ← interface, NOT concrete class
        private CacheManager $cacheManager,
    ) {}

    public function find(int $id): ?Product
    {
        return $this->cacheManager->remember(
            "catalog:product:{$id}",
            ttl: 3600,
            callback: fn () => $this->productRepository->find($id),
        );
    }
}

// ServiceProvider — manual wiring (decorator pattern):
$this->app->bind(ProductRepository::class, function (Application $application) {
    return new RedisProductRepository(
        productRepository: new MysqlProductRepository(
            model: $application->make(Product::class),
        ),
        cacheManager: $application->make(CacheManager::class),
    );
});
```

### Rules
- **NEVER inject concrete implementations** — always inject the interface
- Decorator wiring is done manually in the ServiceProvider
- **All DB queries** go through repositories — no `Model::where(...)` in services
- **All cache reads/writes** go through repositories — no `Cache::get(...)` in services
- Cache invalidation happens in the repository on write operations
- Repository methods return Models or DTOs, never raw query results

### Data flow
```
Controller → Service/Facade → Repository → Database/Cache
     ↑              ↑              ↑
  validates    business logic   data access + caching
```

---

## 6. Module Hot-Swap & Maintenance Mode

Replacing a module at runtime requires draining active pipelines. The system provides
maintenance mode and force-complete functionality.

### Maintenance mode for module swap

```
Admin triggers module swap:
  1. Enable maintenance mode → new requests get "maintenance" page
  2. Existing requests finish normally
  3. Wait for active pipelines to drain (or force-complete)
  4. Swap module config (config/modules.php)
  5. Restart Octane workers (picks up new module bindings)
  6. Disable maintenance mode → site is live with new module
```

### Force-complete suspended pipelines

When swapping modules, admin can force-complete all suspended pipelines.
However, some pipelines may be in a state where force-completion is dangerous
(e.g., mid-payment authorization).

```php
// config/pipelines.php → 'non_completable_steps'
// If a pipeline is currently on one of these steps, it cannot be force-completed
```

### Force-complete flow

```php
class PipelineManager
{
    public function forceCompleteAll(): ForceCompleteResult
    {
        $suspended = SuspendedPipeline::where('status', 'suspended')->get();
        $completed = [];
        $blocked = [];

        $nonCompletableSteps = config('pipelines.non_completable_steps', []);

        foreach ($suspended as $pipeline) {
            if (in_array($pipeline->current_step, $nonCompletableSteps)) {
                $blocked[] = $pipeline;
                continue;
            }

            // Run compensation on completed steps (reverse order)
            $this->compensate($pipeline);

            $pipeline->update([
                'status' => 'force_completed',
                'metadata->force_completed_at' => now(),
                'metadata->force_completed_reason' => 'module_swap',
            ]);

            event(new PipelineForceCompleted($pipeline));
            $completed[] = $pipeline;
        }

        return new ForceCompleteResult(
            completed: $completed,
            blocked: $blocked,
        );
    }
}
```

### Admin API endpoints for module swap

```
POST /api/v1/admin/maintenance/enable      — block new users, return active session count
GET  /api/v1/admin/maintenance/status       — current mode, active pipelines count
POST /api/v1/admin/pipelines/force-complete — force-complete all (returns blocked list)
POST /api/v1/admin/maintenance/disable      — re-open site
```

### Rules
- Maintenance mode blocks NEW requests only — existing requests complete normally
- Force-complete runs `compensate()` on all completed steps in reverse order (clean rollback)
- Pipelines on non-completable steps are skipped and reported to admin
- Admin must handle blocked pipelines manually (wait or expire after external confirmation)
- Octane restart is required after module swap to pick up new service container bindings

---

## Architectural Constraints

- No business logic in `core/` — core is infrastructure only
- No hardcoded pipeline flows — modules register steps, pipeline assembles automatically
- No module providers in `bootstrap/providers.php` — use `config/modules.php`
- No nested pipelines — runtime detection throws `NestedPipelineException`
- No force-complete on non-completable steps — respect `non_completable_steps` config
- No cross-module DB access — each module owns its tables
- No circular facade dependencies — resolve via events or extract shared module
- No concrete injection — always inject interfaces (facades, repositories)
