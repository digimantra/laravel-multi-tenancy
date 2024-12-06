<?php

namespace Digitools\MultiTenancy\Commands;

use Illuminate\Console\Command;
use Digitools\MultiTenancy\Services\TenantManager;

class CreateTenantCommand extends Command
{
    protected $signature = 'tenant:create {name} {domain} {database}';
    protected $description = 'Create a new tenant';

    public function handle()
    {
        $name = $this->argument('name');
        $domain = $this->argument('domain');
        $database = $this->argument('database');

        $tenantManager = new TenantManager();
        $tenantManager->createTenant($name, $domain, $database);

        $this->info("Tenant '{$name}' created successfully.");
    }
}
