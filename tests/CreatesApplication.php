<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        /*
         * Safety guard: Laravel's RefreshDatabase trait can rebuild the
         * configured database. Never allow the automated test suite to run
         * against MiniGrove's normal development database.
         */
        $connection = (string) config('database.default');
        $database = (string) config(
            "database.connections.{$connection}.database"
        );

        if (
            !$app->environment('testing')
            || $database !== 'minigrove_testing'
        ) {
            throw new \RuntimeException(
                'Test database safety check failed. '
                .'Expected minigrove_testing, got '
                .($database !== '' ? $database : '[empty]')
                .'.'
            );
        }

        return $app;
    }
}
