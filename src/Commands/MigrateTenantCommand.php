<?php
namespace Digitools\MultiTenancy\Commands;

use Illuminate\Console\Command;
use Digitools\MultiTenancy\Models\Tenant;

class MigrateTenantCommand extends Command
{
    protected $signature = 'tenant:migrate {--tenant_id=} {--all}';
    protected $description = 'Run migrations for a specific tenant or all tenants';

    public function handle()
    {
        $tenantId = $this->option('tenant_id');
        $runForAll = $this->option('all');

        if ($runForAll) {
            $tenants = Tenant::all();
        } elseif ($tenantId) {
            $tenants = Tenant::where('id', $tenantId)->get();
        } else {
            $this->error('Please provide either --tenant_id or --all option.');
            return;
        }

        foreach ($tenants as $tenant) {
            $this->info("Migrating for tenant: {$tenant->name}");
            $tenant->switchDatabase();

            $this->call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);
        }
    }
}
