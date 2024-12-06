<?php

namespace Digitools\MultiTenancy\Services;

use Illuminate\Support\Facades\DB;

class DatabaseSwitcher
{
    public function switchToTenant($database)
    {
        config(['database.connections.tenant.database' => $database]);
        DB::purge('tenant');
        DB::reconnect('tenant');
        DB::setDefaultConnection('tenant');
    }

    public function switchToDefault()
    {
        DB::purge('tenant');
        DB::setDefaultConnection(config('database.default'));
    }
}
