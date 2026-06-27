<?php

namespace App\Models\Concerns;

trait UsesMainDatabaseConnection
{
    public function getConnectionName()
    {
        return config('database.default');
    }
}
