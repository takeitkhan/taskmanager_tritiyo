<?php

namespace Tritiyo\Site\Controllers;

use Tritiyo\Site\Models\Site;
use Tritiyo\Site\Repositories\SiteInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

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
                'project_id' => 'required',
                'site_code' => 'required'
            ]
        );

        // process the login
        if ($validator->fails()) {
            return redirect('sites.create')
                ->withErrors($validator)
                ->withInput();
        } else {
            // store
            $attributes = [
                'project_id' => $request->project_id,
                'location' => $request->location,
                'site_code' => $request->site_code,
                'budget' => $request->budget,
                'completion_status' => 'Running',
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
        // store
        $attributes = [
            'project_id' => $request->project_id,
            'location' => $request->location,
            'site_code' => $request->site_code,
            'budget' => $request->budget,
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


    public function search(Request $request) {

        if(!empty($request->key)) {
            $default = [
                'search_key' => $request->key ?? '',
                'limit' => 10,
                'offset' => 0
            ];        
            $sites = $this->site->getDataByFilter($default);            
        } else {
            $sites = $this->site->getAll();        
        }
        return view('site::index', ['sites' => $sites]);        
    }
}
