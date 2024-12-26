<?php


namespace Tritiyo\Site\Controllers;

use Tritiyo\Site\Models\SiteInvoice;
use Tritiyo\Site\Models\SiteInvoiceInfo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tritiyo\Site\Repositories\SiteInvoiceInterface;
use Validator;
use DB;


class SiteInvoiceController extends Controller
{
    /**
     * @var SiteInterface
     */
    private $site_invoice;

    /**
     * RoutelistController constructor.
     * @param SiteInterface $site_invoice
     */
    public function __construct(SiteInvoiceInterface $site_invoice)
    {
        $this->site_invoice = $site_invoice;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $site_invoices = $this->site->getAll();
        return view('site::index', ['sites' => $site_invoices]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('site::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required',
                'site_id' => 'required',
                'project_id' => 'required',
                'invoice_no' => 'required',
                'invoice_amount' => 'required',
                'invoice_date' => 'required',
                'invoice_type' => 'required'
            ]
        );
      
      	$bytes = random_bytes(16);
      	$status_key = bin2hex($bytes);     

      
      
      $matched = SiteInvoiceInfo::where('invoice_info_no', $request->invoice_no)->where('invoice_date',  $request->invoice_date)->first();
      
      //dump($request);
      //dd($matched);
      if(!empty($matched)) {
                            $invoice_total_amount = $matched->invoice_total_amount + $request->invoice_amount;                  

                            $infoAttributes = [
                              'invoice_total_amount' => $invoice_total_amount,                               
                              'completion_status' => 'Undone'
                            ];

                            SiteInvoiceInfo::where('id', $matched->id)->update($infoAttributes);
      } else {        			
                          $infoAttributes = [
                            'action_performed_by' => auth()->user()->id,
                            'project_id' => $request->project_id,
                            'range_id'  => \Tritiyo\Project\Helpers\ProjectHelper::current_range_id($request->project_id),
                            'range_status_key' => $request->range_status_key,
                            'invoice_info_no' => $request->invoice_no,
                            'invoice_total_amount' => $request->invoice_amount, 
                            'invoice_date' => $request->invoice_date,
                            'invoice_powo' => $request->invoice_powo,
                            'completion_status' => 'Done',
                            'status_key' => $status_key
                          ];
                          SiteInvoiceInfo::create($infoAttributes);
      }
      
      
        // store
        $attributes = [
                        'user_id' => auth()->user()->id,
                        'site_id' => $request->site_id,
                        'project_id' => $request->project_id,
                        'range_id' => \Tritiyo\Project\Helpers\ProjectHelper::current_range_id($request->project_id),
                        'range_status_key' => $request->range_status_key,
                        'invoice_no' => $request->invoice_no,
                        'invoice_amount' => $request->invoice_amount,
                        'invoice_date' => $request->invoice_date,
                        'invoice_type' => $request->invoice_type,
                        'status_key' => $request->status_key
        ];                        
      	//dump($attributes);
        //dd($infoAttributes);      
        try {
            $site_invoice = $this->site_invoice->create($attributes);          	
            return redirect()->back()->with(['status' => 1, 'message' => 'Successfully created']);
        } catch (\Exception $e) {
            return view('site::create')->with(['status' => 0, 'message' => 'Error']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \Tritiyo\Site\Models\SiteInvoice $site_invoice
     * @return \Illuminate\Http\Response
     */
    public function show(SiteInvoice $site_invoice)
    {
        return view('site::show', ['site' => $site_invoice]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Tritiyo\Site\Models\SiteInvoice $site_invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(SiteInvoice $site_invoice)
    {
        return view('site::edit', ['site' => $site_invoice]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Tritiyo\Site\Models\SiteInvoice $site_invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Site $site_invoice)
    {

        //dd($request);
        // store
        $attributes = [
            'project_id' => $request->project_id,
            'location' => $request->location,
            'site_code' => $request->site_code,
            'budget' => $request->budget,
            'completion_status' => $request->completion_status,
        ];

        try {
            $site_invoice = $this->site->update($site_invoice->id, $attributes);

            return back()
                ->with('message', 'Successfully saved')
                ->with('status', 1)
                ->with('site', $site_invoice);
        } catch (\Exception $e) {
            return view('site::edit', $site_invoice->id)->with(['status' => 0, 'message' => 'Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Tritiyo\Site\Models\SiteInvoice $site_invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->site->delete($id);
        return redirect()->back()->with(['status' => 1, 'message' => 'Successfully deleted']);
    }


    public function search(Request $request)
    {

        if (!empty($request->key)) {
            $default = [
                'search_key' => $request->key ?? '',
                'limit' => 10,
                'offset' => 0
            ];
            $site_invoices = $this->site->getDataByFilter($default);
        } else {
            $site_invoices = $this->site->getAll();
        }

        //dd($site_invoices);
        return view('site::index', ['sites' => $site_invoices]);
    }



    /**
     * Multiple Site Invoice
     */

    public function multipleSiteInvoice(){
        return view('site::multiple_site_invoice');
    }

    public function multipleSiteInvoiceStore(Request $request){        
        $bytes = random_bytes(16);
        $status_key = bin2hex($bytes);
        foreach($request->site_id as $key => $row){
            $attributes = [
                'project_id' => $request->project_id,
                'range_id'  => \Tritiyo\Project\Helpers\ProjectHelper::current_range_id($request->project_id),
                'status_key' => $status_key,
                'invoice_date' => $request->invoice_date,
                'invoice_no' => $request->invoice_no,
                'user_id' => auth()->user()->id,
                'site_id' => $request->site_id[$key],
                'invoice_amount' => $request->invoice_amount[$key] ? $request->invoice_amount[$key] : 0,

                'invoice_type' => $request->invoice_type[$key],
            ];
            $this->site_invoice->create($attributes);
        }
      	

        return redirect()->back()->with([ 'status' => '1', 'message' => 'Invoice successfully created']);

    }

    public function multipleSiteInvoiceEdit($invoideNo){
        $getInvoiceInfo = $invoices = SiteInvoice::where('invoice_no', $invoideNo)->first();

        $invoiceNo = $getInvoiceInfo->invoice_no;
        $projectId = $getInvoiceInfo->project_id;
        $invoiceDate = $getInvoiceInfo->invoice_date;


        $invoiceSites = SiteInvoice::where('invoice_no', $invoideNo)->get();
        return view('site::multiple_site_invoice_another', compact('invoiceNo', 'projectId', 'invoiceDate', 'invoiceSites'));
        //dd($invoice);
    }

    public function multipleSiteInvoiceUpdate(Request $request)
    {
        $getInvoice = SiteInvoice::where('invoice_no', $request->invoiceNo)->delete();
        //dd($request->all());
        $bytes = random_bytes(16);
        $status_key = bin2hex($bytes);
        foreach ($request->site_id as $key => $row) {
            $attributes = [
                'project_id' => $request->project_id,
                'range_id' => \Tritiyo\Project\Helpers\ProjectHelper::current_range_id($request->project_id),
                //'status_key' => $status_key,
                'invoice_date' => $request->invoice_date,
                'invoice_no' => $request->invoice_no,
                'user_id' => auth()->user()->id,
                'site_id' => $request->site_id[$key],
                'invoice_amount' => $request->invoice_amount[$key],
                'invoice_type' => $request->invoice_type[$key],
            ];
            $this->site_invoice->create($attributes);
        }
        return redirect()->route('multiple.site.invoice')->with([ 'status' => '1', 'message' => 'Invoice successfully Edited']);
    }

    public function multipleSiteInvoiceVerify($invoiceInv){
        $getInvoice = SiteInvoice::where('invoice_no', $invoiceInv)->update(['is_verified' => 1]);
        return redirect()->route('multiple.site.invoice')->with([ 'status' => '1', 'message' => 'Successfully verified']);

    }

  
  
  
  
  	/**
     * Multiple Site Invoice
     */

    public function multipleSiteInvoiceTogether() {
        return view('site::multiple_site_invoice_another');
    }
  
  
  	public function multipleSiteInvoiceTogetherStore(Request $request) {              	             
                $bytes = random_bytes(16);
                $status_key = bin2hex($bytes);     
	//dd($request);
      			$matched = SiteInvoiceInfo::where('invoice_info_no', $request->invoice_no)->where('invoice_date',  $request->invoice_date)->first();
      //dd($matched);
     			if(!empty($matched)) {
        			$invoice_total_amount = $matched->invoice_total_amount + $request->total_invoice_amount;                  
                  
                    $infoAttributes = [
                      'invoice_total_amount' => $invoice_total_amount,                               
                      'completion_status' => 'Undone'
                    ];

                    SiteInvoiceInfo::where('id', $matched->id)->update($infoAttributes);
      			} else {        			
                          $infoAttributes = [
                              'action_performed_by' => auth()->user()->id,
                              'project_id' => $request->project_id,
                              'range_id'  => \Tritiyo\Project\Helpers\ProjectHelper::current_range_id($request->project_id),
                              'range_status_key' => $request->range_status_key,
                              'invoice_info_no' => $request->invoice_no, 
                              'invoice_date' => $request->invoice_date, 
                              'invoice_total_amount' => $request->total_invoice_amount, 
                              'invoice_powo' => $request->invoice_powo,
                              'completion_status' => 'Undone'
                 			];
                   			SiteInvoiceInfo::create($infoAttributes);
      			}
             		
      
      			$attributes = array();
                foreach($request->site_id as $key => $row) {
                      $attributes = [                        	
                          'project_id' => $request->project_id,
                          'range_id'  =>  \Tritiyo\Project\Helpers\ProjectHelper::current_range_id($request->project_id),
                         'range_status_key' => $request->range_status_key,
                          'status_key' => $status_key,
                          'invoice_date' => $request->invoice_date,
                          'invoice_no' => $request->invoice_no,
                          'user_id' => auth()->user()->id,
                          'site_id' => $request->site_id[$key],
                          'invoice_amount' => 0,
                          'invoice_type' => $request->invoice_type[0],
                      ];                  	
                	$this->site_invoice->create($attributes);                        
                }      			                                
      
      			return redirect()->back()->with([ 'status' => '1', 'message' => 'Invoice successfully created']);
      			//return redirect()->route('multiple.site.invoice.together.edit.view')->with([ 'status' => '1', 'message' => 'Invoice successfully created']);
            	
    }
  
  	public function multipleSiteInvoiceTogetherEditView($invoice_row_id) {      
      $invoiceInfo = SiteInvoiceInfo::where('id', $invoice_row_id)->first();      
      $invoiceNo = $invoiceInfo->invoice_info_no;
      
      if(!empty($invoiceNo)) {
        $invoiceSites = SiteInvoice::where('invoice_no', $invoiceNo)->get();
        //dd($invoiceSites);

        return view('site::multiple_site_invoice_another_edit_view', compact('invoiceSites', 'invoiceInfo'));
      }
    }
  
  	public function multipleSiteInvoiceTogetherSingleEdit(Request $request) {
      	$id = $request->id;      	
      	$invoice_amount = $request->invoice_amount;
      	$invoice_type = $request->invoice_type;
      
        $attributes = [
          'invoice_amount' => $invoice_amount,
          'invoice_type' => $invoice_type
        ];

      	//dd($attributes);
        try {
          $site = $this->site_invoice->update($id, $attributes);

          return back()
            ->with('message', 'Successfully saved')
            ->with('status', 1);
        } catch (\Exception $e) {
          return view('site::multiple_site_invoice_another_edit_view', $site->id)->with(['status' => 0, 'message' => 'Error']);
        }
    }

    public function multipleSiteInvoiceTogetherEdit($invoideNo){
        $getInvoiceInfo = $invoices = SiteInvoice::where('invoice_no', $invoideNo)->first();

        $invoiceNo = $getInvoiceInfo->invoice_no;
        $projectId = $getInvoiceInfo->project_id;
        $invoiceDate = $getInvoiceInfo->invoice_date;


        $invoiceSites = SiteInvoice::where('invoice_no', $invoideNo)->get();
        return view('site::multiple_site_invoice_another', compact('invoiceNo', 'projectId', 'invoiceDate', 'invoiceSites'));
        //dd($invoice);
    }

    public function multipleSiteInvoiceTogetherUpdate(Request $request)
    {
      //dd($request->invoiceNo);
       // $getInvoice = SiteInvoice::where('invoice_no', $request->invoiceNo)->delete();
        //dd($request->all());
       // $bytes = random_bytes(16);
        //$status_key = bin2hex($bytes);
      	$invoices = $request->invoiceNo;
        foreach ($invoices as $key => $row) {
            $attributes = [
               // 'project_id' => $request->project_id,
                //'range_id' => \Tritiyo\Project\Helpers\ProjectHelper::current_range_id($request->project_id),
                //'status_key' => $status_key,
                //'invoice_date' => $request->invoice_date,
                //'invoice_no' => $request->invoice_no,
               // 'user_id' => auth()->user()->id,
                //'site_id' => $request->site_id[$key],
                'invoice_amount' => $request->invoice_amount[$key],
                'invoice_type' => $request->invoice_type[$key],
            ];
          	DB::table('site_invoices')->where('id', $row)->update($attributes);
        }
      		
            //$this->site_invoice->create($attributes);
        //return redirect()->route('multiple.site.invoice.together')->with([ 'status' => '1', 'message' => 'Invoice successfully Edited']);
      	return redirect()->back()->with([ 'status' => '1', 'message' => 'Invoice successfully Edited']);
    }
  
  	public function multipleSiteInvoiceTogetherEditDone(Request $request) {
      	//dd($request->all());
      	$got = SiteInvoiceInfo::where('id', $request->invoice_info_id)->first();      
      	$got->completion_status = 'Done';
      	$got->save();
      	return redirect()->back()->with([ 'status' => '1', 'message' => 'Invoice finally saved successfully']);
    }
  
  
  	public function projectBasedRange($id) {      	
            $ranges = \Tritiyo\Project\Helpers\ProjectHelper::get_all_ranges_by_project_id($id);
      		return response()->json($ranges);
    }

    public function multipleSiteInvoiceTogetherVerify($invoiceInv){
        $getInvoice = SiteInvoice::where('invoice_no', $invoiceInv)->update(['is_verified' => 1]);
        return redirect()->route('multiple.site.invoice')->with([ 'status' => '1', 'message' => 'Successfully verified']);
    }
  
  
  	/** Invoices List start here **/
  
  	public function invoicesList(Request $request) {
      
      	if(auth()->user()->isManager(auth()->user()->id)) {
          	//$lists = Tritiyo\Site\Models\SiteInvoice::paginate(20);
          	if(!empty($request->search)) {
              	$lists = SiteInvoiceInfo::where('action_performed_by', auth()->user()->id)->where('invoice_info_no', $request->invoice_no)->paginate(30);
            } else {
          		$lists = SiteInvoiceInfo::where('action_performed_by', auth()->user()->id)->paginate(30);
            }
        } else {
          	//$lists = Tritiyo\Site\Models\SiteInvoice::paginate(20);
          	if(!empty($request->search)) {
              	$lists = SiteInvoiceInfo::where('invoice_info_no', $request->invoice_no)->paginate(30);
            } else {
          		$lists = SiteInvoiceInfo::paginate(30);
            }          	
        }
      	return view('site::invoices_list', compact('lists') );
    }



}
