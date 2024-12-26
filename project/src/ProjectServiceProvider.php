<?php

namespace Tritiyo\Project;

use Illuminate\Support\ServiceProvider;

use Tritiyo\Project\Repositories\Project\ProjectEloquent;
use Tritiyo\Project\Repositories\Project\ProjectInterface;
use Tritiyo\Project\Repositories\Project\ProjectRangeEloquent;
use Tritiyo\Project\Repositories\Project\ProjectRangeInterface;
use Tritiyo\Project\Repositories\Project\ProjectBudgetEloquent;
use Tritiyo\Project\Repositories\Project\ProjectBudgetInterface;

class ProjectServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/modules/projects.php');
        $this->loadViewsFrom(__DIR__ . '/views', 'project');
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        $this->publishes([
            __DIR__ . '/Database/Migrations/' => database_path('migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/Database/Seeders/' => database_path('seeders')
        ], 'seeders');


    }

    public function register()
    {
        $this->app->singleton(ProjectInterface::class, ProjectEloquent::class);
        $this->app->singleton(ProjectRangeInterface::class, ProjectRangeEloquent::class);
        $this->app->singleton(ProjectBudgetInterface::class, ProjectBudgetEloquent::class);
    }
}
