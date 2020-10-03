<?php

namespace HusseinFeras\Laraimage\Test;

use HusseinFeras\Laraimage\LaraimageServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        $this->artisan('migrate', ['--database' => 'testing']);
    }

    protected function getPackageProviders($app)
    {
        return [LaraimageServiceProvider::class];
    }
}
