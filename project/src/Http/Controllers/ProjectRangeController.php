<?php

namespace Tritiyo\Project\Http\Controllers;

use Tritiyo\Project\Models\Project;
use Tritiyo\Project\Repositories\Project\ProjectInterface;
use Tritiyo\Project\Models\ProjectRange;
use Tritiyo\Project\Repositories\Project\ProjectRangeInterface;
use Tritiyo\Site\Models\Site;
use Tritiyo\Site\Repositories\SiteInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class ProjectRangeController extends Controller
{
    /**
     * @var ProjectRangeInterface
     */
    private $project;
    private $project_range;
    private $site;

    /**
     * RoutelistController constructor.
     * @param ProjectRangeInterface $project_range
     */
    public function __construct(ProjectInterface $project, ProjectRangeInterface $project_range, SiteInterface $site)
    {
        $this->project = $project;
        $this->project_range = $project_range;
        $this->site = $site;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $project_ranges = $this->project_range->getAll();
        return view('project::index', ['projects' => $projects]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('project::status_update');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(),
            [
                'project_id' => 'required',
                'project_status' => 'required',
                'status_key_type' => 'required'
            ]
        );

        if ($request->project_status == null) {

        } else {
            if ($request->status_key_type == 'New') {
                $bytes = random_bytes(16);
                $status_key = bin2hex($bytes);
            } else if ($request->status_key_type == 'Old') {
                $status_key = \Tritiyo\Project\Models\ProjectRange::where('project_id', $request->project_id)->orderBy('id', 'desc')->first()->status_key;
            } else {

            }
            //dd($status_key);
            // store
            $attributes = [
                'project_id' => $request->project_id,
                'project_status' => $request->project_status,
                'status_update_date' => date('Y-m-d'),
                'status_key' => $status_key
            ];
            try {
                $project_range = $this->project_range->create($attributes);
                return redirect(route('projects.index'))->with(['status' => 1, 'message' => 'Successfully created']);
            } catch (\Exception $e) {
                return view('project::status_update')->with(['status' => 0, 'message' => 'Error']);
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
    public function edit(Project $project, ProjectRange $project_range)
    {
        //dd($request);
        return view('project::status_update', ['project' => $project, 'project_range' => $project_range]);
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
        //dd($request);

        if ($request->project_status == null) {

        } else {
            // store
            $attributes = [
                'project_id' => $request->project_id,
                'project_status' => $request->project_status,
                'status_update_date' => date('Y-m-d')
            ];

            try {

                $project_range = $this->project_range->create($attributes);


                return back()
                    ->with('message', 'Successfully saved')
                    ->with('status', 1)
                    ->with('project', $project_range);
            } catch (\Exception $e) {
                return view('project::status_update', $project->id)->with(['status' => 0, 'message' => 'Error']);
            }
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
}
