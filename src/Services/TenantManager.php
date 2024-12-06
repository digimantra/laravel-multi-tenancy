<?php
namespace Digitools\MultiTenancy\Services;

use Digitools\MultiTenancy\Models\Tenant;

class TenantManager
{
    public function createTenant($name, $domain, $database)
    {
        return Tenant::create([
            'name' => $name,
            'domain' => $domain,
            'database' => $database,
        ]);
    }
}
