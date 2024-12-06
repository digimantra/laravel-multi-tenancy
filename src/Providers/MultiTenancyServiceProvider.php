<?php
namespace Digitools\MultiTenancy\Providers;

use Illuminate\Support\ServiceProvider;
use Digitools\MultiTenancy\Commands\CreateTenantCommand;
use Digitools\MultiTenancy\Commands\MigrateTenantCommand;

class MultiTenancyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/multi-tenancy.php',
            'multi-tenancy'
        );
    }

    public function boot()
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/../../config/multi-tenancy.php' => config_path('multi-tenancy.php'),
        ], 'multi-tenancy');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../../migrations/create_tenants_table.php' => database_path('migrations/' . date('Y_m_d_His') . '_create_tenants_table.php'),
        ], 'multi-tenancy');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateTenantCommand::class,
                MigrateTenantCommand::class,
            ]);
        }
    }
}
