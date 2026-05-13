<?php

namespace Wzije\IndoArea;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class IndoAreaServiceProvider extends ServiceProvider
{


    public function register(): void
    {
        $databasePath = __DIR__ . '/../database/records.sqlite';
        Config::set([
            'database.connections.sqlite_indo_area' => [
                'driver' => 'sqlite',
                'database' => realpath($databasePath) ?: $databasePath,
                'prefix' => '',
                'foreign_key_constraints' => true
            ]
        ]);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }
}
