<?php

use Tritiyo\Site\Controllers\SiteController;
use Tritiyo\Site\Controllers\SiteInvoiceController;
use Illuminate\Http\Request;
use Tritiyo\Site\Excel\SiteExport;
use Tritiyo\Site\Excel\ManagerSiteReport;

Route::group(['middleware' => ['web', 'role:1,3,4,5']], function () {
		Route::get('invoices-list', [SiteInvoiceController::class, 'invoicesList'])->name('multiple.site.invoices.list');
});

Route::group(['middleware' => ['web', 'role:1,3,4,5,8']], function () {
    Route::any('sites/search', [SiteController::class, 'search'])->name('sites.search');

    Route::resources([
        'sites' => SiteController::class,
    ]);

    Route::resources([
        'site_invoices' => SiteInvoiceController::class,
    ]);

  	

    /**
     * Multiple Site Invoice
     */
    Route::get('multiple-site-invoice', [SiteInvoiceController::class, 'multipleSiteInvoice'])->name('multiple.site.invoice');
    Route::post('multiple-site-invoice/store', [SiteInvoiceController::class, 'multipleSiteInvoiceStore'])->name('multiple.site.invoice.store');
    Route::get('multiple-site-invoice/{id}', [SiteInvoiceController::class, 'multipleSiteInvoiceEdit'])->name('multiple.site.invoice.edit');
    Route::post('multiple-site-invoice/update', [SiteInvoiceController::class, 'multipleSiteInvoiceUpdate'])->name('multiple.site.invoice.update');
    Route::get('multiple-site-invoice/verify/{id}', [SiteInvoiceController::class, 'multipleSiteInvoiceVerify'])->name('multiple.site.invoice.verify');
  
  
  	/**
     * Multiple Site Invoice Together
     */
  	Route::get('multiple-site-invoice-together', [SiteInvoiceController::class, 'multipleSiteInvoiceTogether'])->name('multiple.site.invoice.together');
    Route::post('multiple-site-invoice-together/store', [SiteInvoiceController::class, 'multipleSiteInvoiceTogetherStore'])->name('multiple.site.invoice.together.store');
  	Route::get('multiple-site-invoice-together-edit-view/{id}', [SiteInvoiceController::class, 'multipleSiteInvoiceTogetherEditView'])->name('multiple.site.invoice.together.edit.view');
  	Route::post('invoice_edit_done', [SiteInvoiceController::class, 'multipleSiteInvoiceTogetherEditDone'])->name('invoice.edit.done');
  	Route::get('multiple-site-invoice-together-single-edit/{id}', [SiteInvoiceController::class, 'multipleSiteInvoiceTogetherSingleEdit'])->name('multiple.site.invoice.together.single.edit');
  
    Route::get('multiple-site-invoice-together/{id}', [SiteInvoiceController::class, 'multipleSiteInvoiceTogetherEdit'])->name('multiple.site.invoice.together.edit');
    Route::post('multiple-site-invoice-together/update', [SiteInvoiceController::class, 'multipleSiteInvoiceTogetherUpdate'])->name('multiple.site.invoice.together.update');
    Route::get('multiple-site-invoice-together/verify/{id}', [SiteInvoiceController::class, 'multipleSiteInvoiceTogetherVerify'])->name('multiple.site.invoice.together.verify');

  	Route::get('project-based-range/{id}', [SiteInvoiceController::class, 'projectBasedRange'])->name('project.based.range');
  
    //Task Status Complete
    Route::any('site/updated-status', function (Request $request) {
        //When request From Site_status_update.blade
        if (isset($request->batch_status_update)) {
            //dd($request->status_completed);
            if ($request->status_running == 'Submit as Running') {
                $status = 'Running';
            }  
          	if ($request->status_completed == 'Submit as completed') {
                $status = 'Completed';
            }
            //dd($status);

            $html = '<table border="1" width="100%" style="border-collapse:collapse">';
            $html .= '<tr align="center">';
            $html .= '<td><strong>Updated By</strong></td>';
            $html .= '<td><strong>Site code</strong></td>';
            $html .= '<td><strong>Completions Status</strong></td>';
            $html .= '</tr>';

            $totalArray = array_sum(array_count_values(array_column($request->batch_status_update, 'site_id')));
            $i = 0;

            foreach ($request->batch_status_update as $key => $v) {
                if (array_key_exists('site_id', $v)) {
                    $data = new Tritiyo\Site\Models\TaskSiteComplete();
                    $data->user_id = $v['user_id'];
                    $data->task_id = $v['task_id'];
                    $data->task_for = $v['task_for'];
                    $data->site_id = $v['site_id'];
                    $data->status = $status;
                    $data->save();
                    Tritiyo\Site\Models\Site::where('id', $v['site_id'])->update(['completion_status' => $status]);


                    $html .= '<tr align="center">';
                    $html .= '<td>' . App\Models\User::where('id', $v['user_id'])->first()->name . '</td>';
                    $html .= '<td>' . \Tritiyo\Site\Models\Site::where('id', $v['site_id'])->first()->site_code . '</td>';
                    $html .= '<td>' . $status . '</td>';
                    $html .= '</tr>';

                    $i++;
                }
            }


            //dd($html);
            //dump($i);
            //dd();
            $emailAddress = auth()->user()->email;
            if ($status == 'Completed') {
                if ($i == $totalArray) {
                    //Send Mail
                    //Tritiyo\Task\Helpers\MailHelper::send($html, 'Site Completion Status', $emailAddress);
                }
            }
            return redirect()->route('dashboard')->with(['status' => 1, 'message' => 'Successfully updated']);
        }
        //When Request from Show.balde.php
        if (!empty($request->show_page_single_site_id)) {
            Tritiyo\Site\Models\Site::where('id',  $request->show_page_single_site_id)->update([
                'completion_status' => $request->show_page_completion_status,
                'completed_date'  =>  date('Y-m-d'),
                'pending_note' => $request->show_page_pending_note,
                ]);
            return redirect()->back()->with(['status' => 1, 'message' => 'Successfully updated']);
        }
        return view('site::site_status_update');
    })->name('site.status.update');
});

Route::group(['middleware' => ['web', 'role:1,3,4,5,8']], function () {
    Route::get('excel/site-report/accountant', function () {
        return view('site::excel.site_by_accountant');
    })->name('excel.site.report.accountant');
  
      Route::get('excel/manager-site-report', function () {
        return view('site::excel.manager_site_report');
    })->name('excel.manager.site.report');
  
    Route::post('excel/manager-site-report/download', function (Request $request) {
        $manager = \App\Models\User::where('id', $request->manager_id)->first();
      	return Excel::download(new ManagerSiteReport($request->manager_id),   'Site Report Of '.$manager->name.'.xlsx');
    })->name('excel.manager.site.report.download');

     //Site import as excel
     Route::get('site/import', [SiteController::class, 'siteImport'])->name('sites.import');
     Route::post('site/import-excel', [SiteController::class, 'siteImportExcel'])->name('sites.import.excel');

     Route::get('site/excel-excel', function(){
         return Excel::download(new SiteExport(),   'Sites.xlsx');
     })->name('sites.export.excel');

     Route::post('/matchedsite/update', [SiteController::class, 'updateMatchedSite'])->name('sites.matched.update');

     Route::post('/unmatchedsite/store', [SiteController::class, 'storeUnmatchedSite'])->name('sites.unmatched.store');
});
