<?php

use Tritiyo\Vehicle\Controllers\VehicleController;

Route::group(['middleware' => ['web','role:1,3,4,8']], function () {
    Route::any('vehicles/search', [VehicleController::class, 'search'])->name('vehicles.search');

    Route::resources([
        'vehicles' => VehicleController::class,
    ]);
});
