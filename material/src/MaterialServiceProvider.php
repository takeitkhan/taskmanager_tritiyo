<?php
namespace Tritiyo\Material;
use Illuminate\Support\ServiceProvider;
use Tritiyo\Material\Repositories\MaterialEloquent;
use Tritiyo\Material\Repositories\MaterialInterface;
class MaterialServiceProvider extends ServiceProvider {

    public function boot(){
        $this->loadRoutesFrom(__DIR__. '/routes/materials.php');
        $this->loadViewsFrom(__DIR__. '/views', 'material');
        $this->loadMigrationsFrom(__DIR__. '/Migrations');

        $this->publishes([
            __DIR__. '/Migrations/' => database_path('migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__. '/Seeders/' => database_path('seeders')
        ], 'seeders');
    }

    public function register(){
        $this->app->singleton(MaterialInterface::class, MaterialEloquent::class);
    }
}