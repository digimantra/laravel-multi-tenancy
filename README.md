

# Laravel Multi-Tenancy 

A simple and lightweight multi-tenancy solution for Laravel applications, supporting tenant database switching, tenant migrations, and helper functions.

## Installation

### Step 1: Install the Package
Install the package via Composer:
```bash
composer require digitools/multi-tenancy
```

### Step 2: Publish Configuration and Migrations
Publish the configuration file and migrations:
```bash
php artisan vendor:publish --provider="Digitools\MultiTenancy\Providers\MultiTenancyServiceProvider" --tag=multi-tenancy
```

This will create:
- A configuration file: `config/multi-tenancy.php`.
- A migration file: `database/migrations/{timestamp}_create_tenants_table.php`.

### Step 3: Run the Migration
Run the migration to create the `tenants` table:
```bash
php artisan migrate
```

### Step 4: Add Database Connections
Update your `config/database.php` file to include a `tenant` database connection:
```php
'connections' => [
    'tenant' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => null, // dynamically set by the package
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
    ],
    // other connections...
],
```

---

## Usage

### Create Tenants
Add entries to the `tenants` table. Each tenant should have:
- A `name`.
- A unique `domain`.
- A unique `database`.

Example:
```php
use Digitools\MultiTenancy\Models\Tenant;

// Create a tenant
Tenant::create([
    'name' => 'Tenant 1',
    'domain' => 'tenant1.example.com',
    'database' => 'tenant1_db',
]);

Tenant::create([
    'name' => 'Tenant 2',
    'domain' => 'tenant2.example.com',
    'database' => 'tenant2_db',
]);
```

### Middleware for Tenant Resolution
Create a middleware to resolve the current tenant based on the domain:

```php
namespace App\Http\Middleware;

use Closure;
use Digitools\MultiTenancy\Models\Tenant;
use Digitools\MultiTenancy\Services\DatabaseSwitcher;

class ResolveTenant
{
    public function handle($request, Closure $next)
    {
        $host = $request->getHost();
        $tenant = Tenant::where('domain', $host)->first();

        if ($tenant) {
            app(DatabaseSwitcher::class)->switchToTenant($tenant->database);
            app()->instance('current_tenant', $tenant);
        }

        return $next($request);
    }
}
```

Register the middleware in `app/Http/Kernel.php`:
```php
protected $middlewareGroups = [
    'web' => [
        // other middleware...
        \App\Http\Middleware\ResolveTenant::class,
    ],
];
```

### Run Tenant-Specific Migrations
To migrate tenant-specific tables, use:
```bash
php artisan tenant:migrate --tenant_id=1
```

Or migrate all tenants:
```bash
php artisan tenant:migrate --all
```

Ensure tenant-specific migrations are placed under `database/migrations/tenant`.

---

## Example Testing

### Seed Data
Seed tenant databases with some sample data:
```php
$tenant = Tenant::first();
app(Digitools\MultiTenancy\Services\DatabaseSwitcher::class)->switchToTenant($tenant->database);

DB::table('users')->insert([
    'name' => 'Tenant User',
    'email' => 'user@tenant.com',
    'password' => bcrypt('password'),
]);
```

### Test Tenant Switching
Test API routes or controllers with tenant-specific data:
```php
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    $tenant = current_tenant();
    return response()->json([
        'tenant' => $tenant->name,
        'database' => DB::connection()->getDatabaseName(),
    ]);
});
```

Access this route for each tenant domain to verify the correct tenant and database are being resolved.

---

## Configuration

Customize the `multi-tenancy.php` configuration file as needed. Example options include default database, tenant identification strategy, and more.

---

## Contributing
Feel free to open issues or submit pull requests for enhancements or bug fixes.

---

## License
This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

