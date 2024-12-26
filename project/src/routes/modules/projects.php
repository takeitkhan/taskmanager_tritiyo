<?php

use Tritiyo\Project\Http\Controllers\ProjectController;
use Tritiyo\Project\Http\Controllers\ProjectRangeController;
use Tritiyo\Project\Http\Controllers\ProjectBudgetController;
use Maatwebsite\Excel\Facades\Excel;
use Tritiyo\Project\Excel\MobileBillExport;
use Tritiyo\Project\Excel\SiteExport;
use Tritiyo\Project\Excel\SiteInformationExport;
use Tritiyo\Project\Http\Controllers\TargetProjectKpiController;

Route::group(['middleware' => ['web', 'role:1,3,4,5,7,8']], function () {
    Route::any('projects/search', [ProjectController::class, 'search'])->name('projects.search');
    Route::any('projects/site/{id}', [ProjectController::class, 'site'])->name('projects.site');
    Route::any('projects/range/{project_id}', [ProjectController::class, 'range'])->name('projects.range');
    Route::any('projects/current-range/{project_id}', [ProjectController::class, 'currentRange'])->name('projects.current.range');
    Route::get('projects/add_mobile_bill', [ProjectController::class, 'add_mobile_bill'])->name('projects.add.mobile.bill');
    Route::post('projects/add_mobile_bill/store', [ProjectController::class, 'add_mobile_bill_store'])->name('projects.add.mobile.bill.store');

    Route::post('projects/site-recurring', [ProjectController::class, 'updateSiteRecurring'])->name('projects.site.recurring');

    /**
     * KPI
     */
    Route::get('target/projects/kpi/create', [TargetProjectKpiController::class, 'create'])->name('target.projects.kpi.create');
    Route::post('target/projects/kpi/store', [TargetProjectKpiController::class, 'store'])->name('target.projects.kpi.store');
    Route::get('target/projects/kpi/edit', [TargetProjectKpiController::class, 'edit'])->name('target.projects.kpi.edit');
    Route::post('target/projects/kpi/update', [TargetProjectKpiController::class, 'update'])->name('target.projects.kpi.update');
    Route::get('target/projects/kpi/delete', [TargetProjectKpiController::class, 'destroy'])->name('target.projects.kpi.delete');

    Route::get('view/target-all-project-costing/kpi/', [TargetProjectKpiController::class, 'viewTargetAllProjectCosting'])->name('view.all.target.projects.costing.kpi');

    //End Kpi

    Route::resources([
        'projects' => ProjectController::class,
    ]);

    Route::resources([
        'project_ranges' => ProjectRangeController::class,
    ]);

    Route::resources([
        'project_budgets' => ProjectBudgetController::class,
    ]);




    /**
     * project site information
     */
    Route::get('projects/sitesInfo/{project_id}', [ProjectController::class, 'sitesInfo'])->name('projects.sites.info');
    Route::get('projects/sitesInfo/{project_id}/export', function($project_id){
        $range_id = request()->get('range_id');
        $range_date = request()->get('range_date');
        $projectName = \Tritiyo\Project\Models\Project::where('id', $project_id)->first()->name;
        return Excel::download(new SiteInformationExport($project_id, $range_id),   'Site Information of '.$projectName.'('.$range_date.')'.'.xlsx');
    })->name('projects.sites.info.export');

	/** Project Site Export as Export Excel*/
	Route::get('projects/sites/{project_id}/export', function($project_id){
      $project = \Tritiyo\Project\Models\Project::where('id', $project_id)->first();
      $projectName = preg_replace('/[^a-zA-Z0-9\']/', ' ',  $project->name);
      $key = request()->get('key') ?? null;
      return Excel::download(new SiteExport($project_id, ['key' => $key]), 'Site of '.$projectName.'.xlsx');
      
    })->name('project.site.export');
  
  
    //Excel
    Route::get('download/excel/mobile-bill/', function (Request $request) {
        $date = request()->get('daterange');
        $manager_id = request()->get('manager');
        return Excel::download(new MobileBillExport($date, $manager_id), date('Ymd') . 'Mobile Bill.xlsx');
    })->name('download_excel_mobile_bill');
});


//Ajax Api
Route::get('project-based-site/{projectID}/{arrsiteList}', [ProjectController::class, 'projectBasedSite'])->name('project.based.site');

