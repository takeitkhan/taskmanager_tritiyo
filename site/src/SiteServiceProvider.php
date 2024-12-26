<?php

namespace Tritiyo\Site;

use Illuminate\Support\ServiceProvider;
use Tritiyo\Site\Repositories\SiteEloquent;
use Tritiyo\Site\Repositories\SiteInvoiceEloquent;
use Tritiyo\Site\Repositories\SiteInterface;
use Tritiyo\Site\Repositories\SiteInvoiceInterface;

class SiteServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/sites.php');
        $this->loadViewsFrom(__DIR__ . '/views', 'site');
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        $this->publishes([
            __DIR__ . '/Migrations/' => database_path('migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/Seeders/' => database_path('seeders')
        ], 'seeders');
    }

    public function register()
    {
        $this->app->singleton(SiteInterface::class, SiteEloquent::class);
        $this->app->singleton(SiteInvoiceInterface::class, SiteInvoiceEloquent::class);
    }
}
