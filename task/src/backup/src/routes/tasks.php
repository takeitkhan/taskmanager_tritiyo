<?php

use Tritiyo\Task\Controllers\TaskController;
use Tritiyo\Task\Controllers\TaskSiteController;
use Tritiyo\Task\Controllers\TaskVehicleController;
use Tritiyo\Task\Controllers\TaskMaterialController;
use Tritiyo\Task\Controllers\TaskProofController;
use Tritiyo\Task\Controllers\TaskStatusController;
use Tritiyo\Task\Controllers\TaskRequisitionBillController;
use Illuminate\Http\Request;

Route::group(['middleware' => ['web', 'role:1,2,3,4,5,8']], function () {
    //TaskStatus
    Route::resources([
        'taskstatus' => TaskStatusController::class,
    ]);
});

Route::group(['middleware' => ['web', 'role:1,2,3,4,5,8']], function () {
    Route::any('tasks/search', [TaskController::class, 'search'])->name('tasks.search');
    Route::resources([
        'tasks' => TaskController::class,
    ]);
    Route::get('tasks/anonymousproof/{id}', [TaskController::class, 'anonymousProof'])->name('tasks.anonymousproof.edit');
    Route::get('tasks/add_bill/{id}', [TaskRequisitionBillController::class, 'add_bill'])->name('tasks.add_bill');
    Route::put('tasks/update_bill/{id}', [TaskRequisitionBillController::class, 'billUpdate'])->name('tasks.update_bill');
});

Route::group(['middleware' => ['web', 'role:1,2,3,4,5,8']], function () {
    Route::resources([
        'tasksites' => TaskSiteController::class,
    ]);

    Route::get('tasks/sites/{id}', [TaskSiteController::class, 'taskSitebyTaskId'])->name('tasks.site.edit');

    //Vehicle
    Route::resources([
        'taskvehicle' => TaskVehicleController::class,
    ]);

    //Material
    Route::resources([
        'taskmaterial' => TaskMaterialController::class,
    ]);

    //Requisition
    Route::resources([
        'taskrequisitionbill' => TaskRequisitionBillController::class,
    ]);

    Route::any('history/{user_id}/users', function(Request $request, $user_id){
        if(!empty($request->daterange)){
            $task_for_date = $request->daterange;
        } else {
            $task_for_date = '';
        }
        return view('task::user_history', compact('user_id', 'task_for_date'));

    })->name('hidtory.user');


    Route::get('manager/overridden-data/task_id={task_id}', function($task_id){
        if(!empty($task_id)){
            return view('task::manager_overridden_data', compact('task_id'));
        } else {
            return redirect()->back();
        }

    })->name('tasks.manager.overridden.data');


});

Route::group(['middleware' => ['web', 'role:2']], function () {
    //TaskProof
    Route::resources([
        'taskproof' => TaskProofController::class,
    ]);
});


Route::group(['middleware' => ['web']], function () {
    Route::get('live/resource-usage/', function () {
        return view('task::live_resource_usage');
    })->name('live.resource.usage');

});

Route::group(['middleware' => ['web', 'role:5']], function () {

Route::get('excel/requisition-report/accountant', function () {
    return view('task::excel.requisition_by_accountant');
})->name('excel.requisition.report.accountant');

});
