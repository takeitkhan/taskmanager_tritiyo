<?php

use Tritiyo\Material\Controllers\MaterialController;

Route::group(['middleware' => ['web','role:1,3,4,8']], function () {
    Route::any('materials/search', [MaterialController::class, 'search'])->name('materials.search');

    Route::resources([
        'materials' => MaterialController::class,
    ]);
});
