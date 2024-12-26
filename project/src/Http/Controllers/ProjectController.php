<?php

namespace Tritiyo\Project\Http\Controllers;

use Tritiyo\Project\Models\Project;
use Tritiyo\Project\Repositories\Project\ProjectInterface;
use Tritiyo\Site\Models\Site;
use Tritiyo\Site\Repositories\SiteInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Tritiyo\Project\Models\MobileBill;

class ProjectController extends Controller
{
    /**
     * @var ProjectInterface
     */
    private $project;
    private $site;

    /**
     * RoutelistController constructor.
     * @param ProjectInterface $project
     */
    public function __construct(ProjectInterface $project, SiteInterface $site)
    {
        $this->project = $project;
        $this->site = $site;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = $this->project->getAll();
        return view('project::index', ['projects' => $projects]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('project::create');
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
                'name' => 'required',
                'code' => 'required',
                //'budget' => 'required'
            ]
        );

        // process the login
        if ($validator->fails()) {
            return redirect('projects.create')
                ->withErrors($validator)
                ->withInput();
        } else {
            // store
            $attributes = [
                'name' => $request->name,
                'code' => $request->code,
                'type' => $request->type,
                'manager' => $request->manager,
                'customer' => $request->customer,
                'address' => $request->address,
                'vendor' => $request->vendor,
                'supplier' => $request->supplier,
                'location' => $request->location,
                'office' => $request->office,
                'start' => $request->start,
                'end' => $request->end,
                'budget' => $request->budget,
                'summary' => $request->summary,
                'budget_history' => $request->budget_history,
                'is_active' => 1
            ];


            try {
                $project = $this->project->create($attributes);


                $bytes = random_bytes(16);

                \Tritiyo\Project\Models\ProjectRange::create([
                    'project_id' => $project->id,
                    'project_status' => 'Active',
                    'status_update_date' => date('Y-m-d'),
                    'status_key' => bin2hex($bytes)
                ]);

                return redirect(route('projects.index'))->with(['status' => 1, 'message' => 'Successfully created']);
            } catch (\Exception $e) {
                return view('project::create')->with(['status' => 0, 'message' => 'Error']);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        return view('project::show', ['project' => $project]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        return view('project::edit', ['project' => $project]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        // store
        $attributes = [
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'manager' => $request->manager,
            'customer' => $request->customer,
            'address' => $request->address,
            'vendor' => $request->vendor,
            'supplier' => $request->supplier,
            'location' => $request->location,
            'office' => $request->office,
            'start' => $request->start,
            'end' => $request->end,
            'budget' => $request->budget,
            'summary' => $request->summary,
            'budget_history' => $request->budget_history,
            'is_active' => 1
        ];

        try {
            $project = $this->project->update($project->id, $attributes);

            return back()
                ->with('message', 'Successfully saved')
                ->with('status', 1)
                ->with('project', $project);
        } catch (\Exception $e) {
            return view('project::edit', $project->id)->with(['status' => 0, 'message' => 'Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->project->delete($id);
        return redirect()->back()->with(['status' => 1, 'message' => 'Successfully deleted']);
    }


    public function site($id)
    {
        $sites = $this->site->getByAnyWithPaginate('project_id', $id);
        return view('project::site', ['sites' => $sites, 'projectId' => $id]);
    }

    public function range($project_id)
    {
        $project = Project::find($project_id);
        return view('project::range', ['project' => $project]);
    }

    public function currentRange($project_id)
    {
        $project = Project::find($project_id);
        return view('project::current-range', ['project' => $project]);
    }


    public function search(Request $request)
    {

        if (!empty($request->key)) {
            $default = [
                'search_key' => $request->key ?? '',
                'limit' => 10,
                'offset' => 0
            ];
            //dd($default);
            $projects = $this->project->getDataByFilter($default);
            //dd($projects);
        } else {
            $projects = $this->project->getAll();
        }
        return view('project::index', ['projects' => $projects]);
    }


    /**
     * Extra features to add mobile bill
     * @param Request $request
     */
    public function add_mobile_bill(Request $request){

        return view('project::add_mobile_bill');
    }

    public function add_mobile_bill_store(Request $request)
    {

        //$data = DB::SELECT("SELECT *, (SELECT id FROM project_ranges WHERE project_id = projects.id ORDER BY id DESC LIMIT 0,1) AS range_id FROM `projects` WHERE manager = " . $request->post('project_id'));
        $preparing_insert_data = [];
        foreach($request->project_id as $i => $data){
               $preparing_insert_data []= [
                'manager_id' => Project::where('id', $data)->first()->manager,
                'project_id' => $data,
                'range_id' => \Tritiyo\Project\Helpers\ProjectHelper::current_range_id($data),
                'mobile_number' => $request->mobile_number[$i],
                'received_amount' => $request->received_amount[$i],
                'received_date' => date('Y-m-d')
               ];
        }


		/*
        $preparing_insert_data = [];
        foreach ($data as $key => $val) {
            $preparing_insert_data[] = array(
                'manager_id' => $request->post('manager_id'),
                'project_id' => $val->id,
                'range_id' => $val->range_id,
                'mobile_number' => $request->mobile_number,
                'received_amount' => $request->received_amount / count($data),
                'received_date' => date('Y-m-d')
            );
        }
        */
        //dd($preparing_insert_data);
        MobileBill::insert($preparing_insert_data);
        return redirect()->back()->with(['status'=> 1, 'message' => 'Successfully added']);
    }



    public function updateSiteRecurring(Request $request){
      //dd($request->all());
      $selectedSiteStatus = $request->selected_site_status;
      $recurringLoop = $request->recurring;
      if(!empty($recurringLoop)){
          foreach($recurringLoop as $key => $site){
              $data = Site::find($site['site_id']);
            if($request->selected_site_recurring == 'Submit For Recurring'){
              $data->site_type = 'Recurring';
              $text = 'Recurring';
            } 
            if(!empty($selectedSiteStatus)){
              $data->completion_status = $selectedSiteStatus;
              $text = $selectedSiteStatus;
            }
              $data->save();
          }
      		return redirect()->back()->with(['status'=> 1, 'message' => $text.' Successfully']);
      	} else {
    		return redirect()->back()->with(['status'=> 0, 'message' => 'You did not select any site']);
    	}
    }



    public function projectBasedSite($projectId, $arrSiteList){
      	$siteList = explode(',', $arrSiteList);
      	//return $siteList;
        return \Tritiyo\Site\Models\Site::where('project_id', $projectId)
                ->whereNotIn('id',$siteList)
                ->get();
    }


    public function sitesInfo($projectId){
        $project = \Tritiyo\Project\Models\Project::where('id', $projectId)->first();
        return view ('project::project_sites_information', compact('project'));
    }
}
