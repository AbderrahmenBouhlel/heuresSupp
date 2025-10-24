<?php

namespace App\Providers;

use App\Modules\Admin\V1\infrastructure\ExcelServicePort;
use App\Modules\Admin\V1\infrastructure\adapters\AssignmentAdapterV1 ;
use Illuminate\Support\ServiceProvider;

class ExcelAdapterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            ExcelServicePort::class,
            AssignmentAdapterV1::class
        );
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
