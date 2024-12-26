<?php
namespace Tritiyo\Vehicle;
use Illuminate\Support\ServiceProvider;
use Tritiyo\Vehicle\Repositories\VehicleEloquent;
use Tritiyo\Vehicle\Repositories\VehicleInterface;
class VehicleServiceProvider extends ServiceProvider {

    public function boot(){
        $this->loadRoutesFrom(__DIR__. '/routes/vehicles.php');
        $this->loadViewsFrom(__DIR__. '/views', 'vehicle');
        $this->loadMigrationsFrom(__DIR__. '/Migrations');

        $this->publishes([
            __DIR__. '/Migrations/' => database_path('migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__. '/Seeders/' => database_path('seeders')
        ], 'seeders');
    }

    public function register(){
        $this->app->singleton(VehicleInterface::class, VehicleEloquent::class);
    }
}