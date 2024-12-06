<?php

namespace Digitools\MultiTenancy\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = ['name', 'domain', 'database'];

    public function switchDatabase()
    {
        config(['database.connections.tenant.database' => $this->database]);
        \DB::purge('tenant');
        \DB::reconnect('tenant');
    }
}
