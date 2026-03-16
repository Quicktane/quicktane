Create a new module scaffold for the Quicktane e-commerce platform.

Module name: $ARGUMENTS

## Determine module type

Parse the arguments to determine if this is a **local** or **platform** module:
- If arguments contain `--local` flag → create a **local module** in `app/Modules/`
- Otherwise → create a **platform module** in `packages/`

---

## Instructions for PLATFORM modules (default)

1. Read `.claude/architecture-rules.md` to ensure the scaffold follows all rules
2. Create the following directory structure under `packages/{module-name}/`:

```
packages/{module-name}/
├── composer.json                   # quicktane/module-{module-name}
├── src/
│   ├── {ModuleName}ServiceProvider.php
│   ├── Facades/
│   ├── Contracts/
│   ├── Models/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Services/
│   ├── Repositories/
│   ├── Events/
│   ├── Listeners/
│   ├── Steps/                     # Pipeline steps (if module participates in flows)
│   └── Policies/
├── database/
│   ├── migrations/
│   └── Seeders/
├── routes/
│   ├── api.php
│   └── admin.php
└── config/
```

3. Create `composer.json`:
   - Name: `quicktane/module-{module-name}`
   - PSR-4 autoload: `Quicktane\{ModuleName}\` → `src/`
   - If module has seeders, add: `Quicktane\{ModuleName}\Database\` → `database/`
   - Require: `quicktane/core` at `@dev`
   - Laravel auto-discovery for ServiceProvider
   - Add any cross-module dependencies to require

4. Create the ServiceProvider:
   - `declare(strict_types=1);`
   - Namespace: `Quicktane\{ModuleName}`
   - Extend `Quicktane\Core\Module\ModuleServiceProvider`
   - Register routes from `routes/api.php` and `routes/admin.php`
   - Register migrations from `database/migrations/`
   - Register config from `config/`

5. Create empty route files:
   - `routes/api.php` — with route group prefix `/api/v1/{module-name}` and `api` middleware
   - `routes/admin.php` — with route group prefix `/api/v1/admin/{module-name}` and `['api', 'auth']` middleware

6. Create an empty config file `config/{module-name}.php` returning an empty array

7. Register the new ServiceProvider in `config/modules.php` under the `modules` key

8. Add the package to root `composer.json` require: `"quicktane/module-{module-name}": "@dev"`

9. Run `composer update quicktane/module-{module-name}` to install the package

10. Output a summary of what was created

---

## Instructions for LOCAL modules (`--local` flag)

1. Read `.claude/architecture-rules.md` to ensure the scaffold follows all rules
2. Create the following directory structure under `app/{ModuleName}/`:

```
app/{ModuleName}/
├── {ModuleName}ServiceProvider.php
├── Facades/
├── Contracts/
├── Models/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
├── Services/
├── Repositories/
├── Events/
├── Listeners/
├── Steps/
├── Policies/
├── database/
│   ├── migrations/
│   └── Seeders/
├── routes/
│   ├── api.php
│   └── admin.php
└── config/
```

3. Create the ServiceProvider:
   - `declare(strict_types=1);`
   - Namespace: `App\{ModuleName}`
   - Extend `Quicktane\Core\Module\LocalModuleServiceProvider`
   - Register routes from `routes/api.php` and `routes/admin.php`
   - Register migrations from `database/migrations/`
   - Register config from `config/`

4. Create empty route files:
   - `routes/api.php` — with route group prefix `/api/v1/{module-name}` and `api` middleware
   - `routes/admin.php` — with route group prefix `/api/v1/admin/{module-name}` and `['api', 'auth']` middleware

5. Create an empty config file `config/{module-name}.php` returning an empty array

6. No need to edit `config/modules.php` — local modules are auto-discovered

7. No need to edit `composer.json` — autoloaded via `"App\\": "app/"`

8. Output a summary of what was created

---

## Rules
- All PHP files must have `declare(strict_types=1);`
- Follow PSR-12 and Laravel conventions
- Do NOT create any test files
- Do NOT create placeholder models or controllers — only the scaffold structure
- Use kebab-case for route prefixes (`order-item`, not `orderItem`)
- **Platform modules:** ServiceProvider extends `Quicktane\Core\Module\ModuleServiceProvider`
- **Local modules:** ServiceProvider extends `Quicktane\Core\Module\LocalModuleServiceProvider`
- Platform modules register in `config/modules.php`, NOT in `bootstrap/providers.php`
- Local modules are auto-discovered — do NOT register them anywhere
- Platform package name format: `quicktane/module-{module-name}`
- Platform namespace format: `Quicktane\{ModuleName}\`
- Local namespace format: `App\{ModuleName}\`
