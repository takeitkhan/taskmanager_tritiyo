<?php

use Tritiyo\Task\Controllers\TaskController;
use Tritiyo\Task\Controllers\TaskSiteController;
use Tritiyo\Task\Controllers\TaskVehicleController;
use Tritiyo\Task\Controllers\TaskMaterialController;
use Tritiyo\Task\Controllers\TaskProofController;
use Tritiyo\Task\Controllers\TaskStatusController;
use Tritiyo\Task\Controllers\TaskRequisitionBillController;
use Illuminate\Http\Request;
use Tritiyo\Task\Excel\ManagerTaskReport;

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
     Route::post('requsition-approve/amount-send', [TaskRequisitionBillController::class, 'requisitionApproveAmountSend'])->name('requsition.approve.amount.send');


    /**
     * User History
     */
    Route::any('history/{user_id}/users', function (Request $request, $user_id) {
//        dd($user_id);
        if (!empty($request->daterange)) {
            $task_for_date = $request->daterange;
        } else {
            $task_for_date = '';
        }
        return view('task::user_history', compact('user_id', 'task_for_date'));

    })->name('hidtory.user');

    /**
     * KPI of user
     */

    Route::any('kpi/{user_id}/users', function (Request $request, $user_id){
        if (!empty($request->daterange)) {
            $task_for_date = $request->daterange;
        } else {
            $task_for_date = '';
        }
        return view('task::user_kpi', compact('user_id', 'task_for_date'));
    })->name('kpi.user');

    Route::any('get_user_history', function (Request $request) {
        if($request->get('user_id') == null){  
            $user_id = $request->get('current_user_id');
        }else{
            $user_id = $request->get('user_id');  
        }
        if (!empty($request->daterange)) {
            $task_for_date = $request->daterange;
        } else {
            $task_for_date = '';
        }
        return view('task::user_history', compact('user_id', 'task_for_date'));

    })->name('get_user_history');


    Route::get('manager/overridden-data/task_id={task_id}', function ($task_id) {
        if (!empty($task_id)) {
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

    Route::get('archive/resource-usage/', function () {
        return view('task::archive_resource_usage');
    })->name('archive.resource.usage');

    Route::get('archive/resource-usage/excel', function(){
        $date = request()->get('daterange');
        return Excel::download(new Tritiyo\Task\Excel\ArchiveResourceExport($date), 'Archive Usage Report Export' . $date . '.xlsx');
    })->name('archive.resource.usage.excel');

    Route::get('archive/resource-transaction/', function () {
        return view('task::archive_resource_transaction');
    })->name('archive.resource.transaction');

    Route::get('archive/resource-transaction/excel', function(){
        $date = request()->get('daterange');
        return Excel::download(new Tritiyo\Task\Excel\ArchiveResourceTransactionExport($date), 'Archive Transaction Report Export' . $date . '.xlsx');
    })->name('archive.resource.transaction.excel');
  
      Route::get('excel/manager-task-report', function () {
        return view('task::task_report_of_manager');
    })->name('excel.manager.task.report');
  
    Route::post('excel/manager-task-report/download', function (Request $request) {
        $manager = \App\Models\User::where('id', $request->manager_id)->first();
      	return Excel::download(new ManagerTaskReport($request->manager_id),   'Task Report Of '.$manager->name.'.xlsx');
    })->name('excel.manager.task.report.download');
  
});

// For date range form view
Route::group(['middleware' => ['web', 'role:5']], function () {
    Route::get('excel/requisition-report/accountant', function () {
        return view('task::excel.requisition_by_accountant');
    })->name('excel.requisition.report.accountant');
  
    Route::get('excel/bill-report/accountant', function () {
        return view('task::excel.bill_by_accountant');
    })->name('excel.bill.report.accountant');
});



//Ajax
Route::group(['middleware' => ['web']], function () {
    Route::get('/get-remain-budget/{project_id}', [TaskController::class, 'remainingBalanceOfProjectBudget'])->name('project.remain.balance');
    Route::get('/check-limit-site/{site_id}', [TaskController::class, 'checkSiteLimit'])->name('project.check.limit.site');
    Route::get('/check-resource-pending-bills/{resource_id}', [TaskController::class, 'resourcePendingBills'])->name('project.check.resource.pending.bills');
    Route::get('/total-requisition-bill-index/{task_id}', [TaskController::class, 'ajaxTotalRequisitionBillIndex'])->name('total.requisition.bill.index');
});
