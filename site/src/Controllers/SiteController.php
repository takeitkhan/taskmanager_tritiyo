<?php

namespace Tritiyo\Site\Controllers;

use App\Helpers\Mail;
use App\Models\User;
use Tritiyo\Project\Models\Project;
use Tritiyo\Site\Models\Site;
use Tritiyo\Site\Repositories\SiteInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

use Maatwebsite\Excel\Facades\Excel;
use Tritiyo\Site\Excel\SiteImport;
use Illuminate\Support\Facades\Session;

class SiteController extends Controller
{
    /**
     * @var SiteInterface
     */
    private $site;

    /**
     * RoutelistController constructor.
     * @param SiteInterface $site
     */
    public function __construct(SiteInterface $site)
    {
        $this->site = $site;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sites = $this->site->getAll();
        return view('site::index', ['sites' => $sites]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //return view('site::create');
        return redirect()->to('/site/import');
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
                'project_id' => 'required',
                'site_code' => 'required'
            ]
        );

        $range_id = \Tritiyo\Project\Helpers\ProjectHelper::current_range_id($request->project_id);
        $getManager = \Tritiyo\Project\Models\Project::where('id', $request->project_id)->first()->manager;
        // process the login
        if ($validator->fails()) {
            return redirect('sites.create')
                ->withErrors($validator)
                ->withInput();
        } else {
            // store
            $attributes = [
                'project_id' => $request->project_id,
                'user_id' => auth()->user()->id,
                'pm' => $getManager,
                'location' => $request->location,
                'site_code' => $request->site_code,
                'budget' => $request->budget,
                'range_ids' => $range_id,
                'task_limit' => $request->task_limit,
                'completion_status' => NULL,
            ];

            try {
                $site = $this->site->create($attributes);
                return redirect(route('sites.index'))->with(['status' => 1, 'message' => 'Successfully created']);
            } catch (\Exception $e) {
                return view('site::create')->with(['status' => 0, 'message' => 'Error']);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \Tritiyo\Site\Models\Site $site
     * @return \Illuminate\Http\Response
     */
    public function show(Site $site)
    {      	
        return view('site::show', ['site' => $site]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Tritiyo\Site\Models\Site $site
     * @return \Illuminate\Http\Response
     */
    public function edit(Site $site)
    {
        return view('site::edit', ['site' => $site]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Tritiyo\Site\Models\Site $site
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Site $site)
    {
        $getManager = \Tritiyo\Project\Models\Project::where('id', $request->project_id)->first()->manager;

        //dd($request);
        // store
        $attributes = [
            'project_id' => $request->project_id,
            'pm' => $getManager,
            'location' => $request->location,
            'site_code' => $request->site_code,
            'budget' => $request->budget,
            'task_limit' => $request->task_limit,
            'completion_status' => $request->completion_status,
        ];

        try {
            $site = $this->site->update($site->id, $attributes);

            return back()
                ->with('message', 'Successfully saved')
                ->with('status', 1)
                ->with('site', $site);
        } catch (\Exception $e) {
            return view('site::edit', $site->id)->with(['status' => 0, 'message' => 'Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Tritiyo\Site\Models\Site $site
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
            $sites = $this->site->getDataByFilter($default);
        } else {
            $sites = $this->site->getAll();
        }

        //dd($sites);
        return view('site::index', ['sites' => $sites]);
    }


    //Import

    public function siteImport(Request $request)
    {
//        session()->forget('sitematched');
//        session()->forget('siteunmatched');
        return view('site::import');
    }


    public function siteImportExcel(Request $request)
    {
      	if($request->reset){
            $xyz = Session::all();
         	// dd($xyz);
            unset($xyz['siteunmatched']);
          	session()->forget('siteunmatched');
          
            unset($xyz['sitematched']);
          	session()->forget('sitematched');
          
             unset($xyz['matechedSiteButNotPending']);
          	session()->forget('matechedSiteButNotPending');
          
           	return redirect()->back();
        }
      
        if ($request->upload) {
            $xyz = Session::all();
            unset($xyz['siteunmatched']);
            unset($xyz['sitematched']);

//            session()->get('');
//            session()->put();
            //Session::save();
            //dd($xyz);


            $request->validate([
                'import' => 'required|max:10000|mimes:xlsx,xls',
            ]);

            //$path = $request->file('import')->getRealPath();
            $path = $request->file('import');
            Excel::import(new SiteImport, $path);
            return redirect()->back();
        } else {
//            $data = Session::all();
//            Session::forget($data['sitematched']);
//            $data = Session::all();
//            dd($data);
        }

    }


    public function updateMatchedSite(Request $request)
    {
        foreach ($request->matched as $key => $row) {
            $data = Site::find($row['site_id']);
            $data->completion_status = Null;
            $data->pending_note = Null;
            $data->site_type = $row['site_type'];
            if (!empty($row['task_limit'])) {
                $data->task_limit = $row['task_limit'];
            }
            $data->activity_details = $row['activity_details'];
            $cuurentRangeIdOfProject = \Tritiyo\Project\Helpers\ProjectHelper::current_range_id($row['project_id']);
            if ($row['site_type'] == 'Old') {
                //dd($cuurentRangeIdOfProject);
                $updateRangId = $data->range_ids . ',' . $cuurentRangeIdOfProject;
                $data->range_ids = $updateRangId;
            } else {
                $data->range_ids = $cuurentRangeIdOfProject;
            }
            $data->save();
        }
        session()->forget('sitematched');
        return redirect()->back()->with(['status' => 1, 'message' => 'Successfully updated']);
    }

    public function storeUnmatchedSite(Request $request)
    {      
      	//dd($request);
        foreach ($request->unmatched as $key => $row) {
            $cuurentRangeIdOfProjectCheck = \Tritiyo\Project\Helpers\ProjectHelper::current_range_id_for_import($row['project_id']);          
          	//dd($row['project_id']);
            if($cuurentRangeIdOfProjectCheck != NULL) {
                //dd($row['project_id']);
                $data = new Site();
                $data->site_code = $row['site_code'];
                $data->user_id = auth()->user()->id;
                $data->location = $row['location'];
                $data->task_limit = $row['task_limit'] ?? NULL;
                $data->project_id = $row['project_id'];
                $data->pm = $row['pm'];
                $data->site_type = 'Fresh';
                $cuurentRangeIdOfProject = \Tritiyo\Project\Helpers\ProjectHelper::current_range_id($row['project_id']);
                $data->range_ids = $cuurentRangeIdOfProject;
                $data->save();
            } else {
                return redirect()->back()->with(['status' => 0, 'message' => 'There are any/some project/s range has not been updated.']);
            }
        }
        /* Mail Function */
        $getSites = [];
        foreach ($request->unmatched as $key => $row) {
            $cuurentRangeIdOfProjectCheck = \Tritiyo\Project\Helpers\ProjectHelper::current_range_id_for_import($row['project_id']);
            if($cuurentRangeIdOfProjectCheck != NULL) {
                $anagerId = Project::where('id', $row['project_id'])->first()->manager;
                $getSites[$anagerId][] = (object)[
                    'site_code' => $row['site_code'],
                    'location' => $row['location'],
                    'project' => Project::where('id', $row['project_id'])->first()->name,
                    'manager_email' => User::where('id', Project::where('id', $row['project_id'])->first()->manager)->first()->email,
                    'manager_name' => User::where('id', Project::where('id', $row['project_id'])->first()->manager)->first()->name,
                ];
            }
        }
        if(!empty($getSites)){
            foreach($getSites as $key => $data){
                $text = 'New Sites has been added. these are given below';
                $html = Mail::textMessageGenerator($text);
                $html .= Mail::tableGenerator($data, ['Site code', 'Location', 'Project', 'Manager'],['site_code', 'location', 'project', 'manager_name'], 'width: 100%; text-align: left;');
                $subject = 'New Sites has been added.';
                $emailAddress = $data[0]->manager_email;
                \Tritiyo\Task\Helpers\MailHelper::send($html, $subject, $emailAddress);
            }
        }
        session()->forget('siteunmatched');
        return redirect()->back()->with(['status' => 1, 'message' => 'Successfully inserted']);
    }
}
