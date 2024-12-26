<?php

namespace Tritiyo\Task;

use Illuminate\Support\ServiceProvider;

use Tritiyo\Task\Repositories\TaskEloquent;
use Tritiyo\Task\Repositories\TaskInterface;

use Tritiyo\Task\Repositories\TaskSiteEloquent;
use Tritiyo\Task\Repositories\TaskSiteInterface;

use Tritiyo\Task\Repositories\TaskVehicle\TaskVehicleEloquent;
use Tritiyo\Task\Repositories\TaskVehicle\TaskVehicleInterface;

use Tritiyo\Task\Repositories\TaskMaterial\TaskMaterialEloquent;
use Tritiyo\Task\Repositories\TaskMaterial\TaskMaterialInterface;

use Tritiyo\Task\Repositories\TaskProof\TaskProofEloquent;
use Tritiyo\Task\Repositories\TaskProof\TaskProofInterface;

use Tritiyo\Task\Repositories\TaskStatus\TaskStatusEloquent;
use Tritiyo\Task\Repositories\TaskStatus\TaskStatusInterface;

use Tritiyo\Task\Repositories\TaskRequisitionBill\TaskRequisitionBillEloquent;
use Tritiyo\Task\Repositories\TaskRequisitionBill\TaskRequisitionBillInterface;

class TaskServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/tasks.php');
        $this->loadViewsFrom(__DIR__ . '/views', 'task');
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
        $this->app->singleton(TaskInterface::class, TaskEloquent::class);

        $this->app->singleton(TaskSiteInterface::class, TaskSiteEloquent::class);

        $this->app->singleton(TaskVehicleInterface::class, TaskVehicleEloquent::class);

        $this->app->singleton(TaskMaterialInterface::class, TaskMaterialEloquent::class);

        $this->app->singleton(TaskProofInterface::class, TaskProofEloquent::class);

        $this->app->singleton(TaskStatusInterface::class, TaskStatusEloquent::class);

        $this->app->singleton(TaskRequisitionBillInterface::class, TaskRequisitionBillEloquent::class);
    }
}
