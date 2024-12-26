<?php

namespace Tritiyo\Project\Http\Controllers;

use App\Exports\MailAttachment;
use App\Helpers\Mail;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Tritiyo\Project\Models\Project;
use Tritiyo\Project\Models\ProjectBudget;
use Tritiyo\Project\Repositories\Project\ProjectInterface;
use Tritiyo\Project\Repositories\Project\ProjectRangeInterface;
use Tritiyo\Project\Repositories\Project\ProjectBudgetInterface;
use Tritiyo\Site\Models\Site;
use Tritiyo\Site\Repositories\SiteInterface;
use Validator;

class ProjectBudgetController extends Controller
{
    private $project;
    private $project_range;
    private $project_budget;
    private $site;

    public function __construct(ProjectInterface $project, ProjectRangeInterface $project_range, ProjectBudgetInterface $project_budget, SiteInterface $site)
    {
        $this->project = $project;
        $this->project_range = $project_range;
        $this->project_budget = $project_budget;
        $this->site = $site;
    }
    public function index(){

    }
    public function create()
    {
        return view('project::project_budget');
    }

    public function store(Request $request, Project $project)
    {


        $validator = Validator::make($request->all(),
            [
                'project_id' => 'required',
                'budget_amount' => 'required'
            ]
        );

        if ($request->budget_amount == null) {

        } else {
          	if(!empty($request->site_id)) {
            $site_ids = implode(',',  $request->site_id);
            } else {
              $site_ids = NULL;
            }
            // store
            $attributes = [
                'project_id' => $request->project_id,
                'current_range_id' => $request->current_range_id,
                'budget_amount' => $request->budget_amount,
                'site_id' => $site_ids,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            try {
                //dd($attributes);
                $project_budget = $this->project_budget->create($attributes);

                // Mail funvction
                $projectInfo = Project::where('id', $request->project_id)->first();
                if(!empty($site_ids)) {
                    $text = 'Dear Concern, <br> BDT ' . $request->budget_amount . ' has been added to ' . $projectInfo->name . ' as a budget for the folowing sites';
                } else {
                    $text = 'Dear Concern, <br> BDT ' . $request->budget_amount . ' has been added to ' . $projectInfo->name;
                }
                $emSite = !empty($site_ids) ? explode(',', $site_ids) : Null;
                $projectSite = [];
                if(!empty($emSite)) {
                    foreach ($emSite as $site) {
                        $projectSite [] = (object)[
                            'site_code' => Site::where('id', $site)->first()->site_code,
                            'manager' => User::where('id', $projectInfo->manager)->first()->name,
                        ];
                    }
                }
                $html = Mail::textMessageGenerator($text);
                if(!empty($projectSite)) {
                    $html .= Mail::tableGenerator($projectSite, ['Site Code', 'Manager'], ['site_code', 'manager'], 'width: 100%; text-align: left;');
                }
                $subject = 'Budget has been added to '.$projectInfo->name;
                $emailAddress = User::where('id', $projectInfo->manager)->first()->email;
                //$emailAddress = 'nipun@tritiyo.com';

                \Tritiyo\Task\Helpers\MailHelper::send($html, $subject, $emailAddress);


                //dd($html);
                return redirect()->back()->with(['status' => 1, 'message' => 'Successfully created']);
            } catch (\Exception $e) {
                return view('project::project_budget')->with(['status' => 0, 'message' => 'Error']);
            }
        }


    }
  
  
  public function destroy($id){
  	$data = ProjectBudget::find($id);
    $data->delete();
    return redirect()->back()->with(['status' => 0, 'message' => 'Successfully deleted']);
  }
}
