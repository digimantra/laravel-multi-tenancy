<?php

if (!function_exists('current_tenant')) {
    function current_tenant()
    {
        return app('current_tenant');
    }
}

if (!function_exists('is_tenant_environment')) {
    function is_tenant_environment()
    {
        return !is_null(current_tenant());
    }
}
