<?php

namespace Tritiyo\Task\Controllers;

use Carbon\Carbon;

use Tritiyo\Task\Helpers\TaskHelper;
use Tritiyo\Task\Models\Task;
use Tritiyo\Task\Models\TaskSite;
use Tritiyo\Task\Models\TaskStatus;
use Tritiyo\Task\Models\TaskVehicle;
use Tritiyo\Task\Models\TaskMaterial;
use Tritiyo\Task\Repositories\TaskInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Session;
use App\Models\CheckAttack;

class TaskController extends Controller
{
    /**
     * @var TaskInterface
     */
    private $task;

    /**
     * RoutelistController constructor.
     * @param TaskInterface $task
     */
    public function __construct(TaskInterface $task)
    {
        $this->task = $task;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$tasks = $this->task->getAll();
        $tasks = Task::orderBy('task_for', 'desc')->paginate('100');
        return view('task::index', ['tasks' => $tasks]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $catchAttack = [
        'attack_name' => 'Task Create Page',
        'attack_content' =>  '',
        'attack_by' => auth()->user()->id,
      ];
       CheckAttack::create($catchAttack);
        return view('task::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

      
        // Validation on server side
        // Check site head have pending bills
        $count_result = \Tritiyo\Task\Helpers\TaskHelper::getPendingBillCountStatus($request->site_head);
        // dd($count_result);
        if ($count_result == 'Yes') {
            return redirect()->back()->with('message', 'Your selected resource has atleast 3 pending bills. You can\'t select this resource.')->with('status', 0);
        }

        //Check If project has not budget

        $validator = Validator::make($request->all(),
            [
                'project_id' => 'required',
                'task_name' => 'required',
            ]
        );
        // process the login
        if ($validator->fails()) {
            return redirect('tasks.create')
                ->withErrors($validator)
                ->withInput();
        } else {

            if ($request->task_type == 'emergency') {
                $dt = Carbon::now();
                $task_for = $dt->toDateString();
              	$checkSiteHeadAvailable = \Tritiyo\Task\Models\Task::where('site_head', $request->site_head)->where('task_for', $task_for)->first();
               
            } else {
                $dt = Carbon::tomorrow();
                $task_for = $dt->toDateString();
                $checkSiteHeadAvailable = \Tritiyo\Task\Models\Task::where('site_head', $request->site_head)->where('task_for', $task_for)->first();
            }
          if(!empty($checkSiteHeadAvailable)){
          	return redirect()->back()->with(['status' => 0, 'message' => 'Site head already used in another task']);
          }else{
              // store
              $attributes = [
                  'user_id' => auth()->user()->id,
                  'task_type' => $request->task_type,
                  'project_id' => $request->project_id,
                  'current_range_id' => \Tritiyo\Project\Helpers\ProjectHelper::current_range_id($request->project_id),
                  'task_code' => $request->task_code ?? null,
                  'task_name' => $request->task_name,
                  'site_head' => $request->site_head,
                  'task_details' => $request->task_details,

                  'task_for' => $task_for ?? NULL,
              ];
           }

            try {
                $task = $this->task->create($attributes);
				
                 $catchAttack = [
                      'attack_name' => 'Task Created',
                      'attack_content' =>  'Task ID '.$task->id,
                      'attack_by' => auth()->user()->id,
                   ];
                  CheckAttack::create($catchAttack);
              
                TaskHelper::statusUpdate([
                    'code' => TaskHelper::getStatusKey('task_created'),
                    'task_id' => $task->id,
                    'action_performed_by' => auth()->user()->id,
                    'performed_for' => null,
                    'requisition_id' => null,
                    'message' => TaskHelper::getStatusMessage('task_created')
                ]);
              
              
              		
               

                //return view('task::edit', ['task' => $task]);
                return redirect(route('tasks.edit', $task->id) . '?task_id=' . $task->id . '&information=taskinformation')->with(['status' => 1, 'message' => 'Successfully created']);
                //return redirect(route('tasks.index'))->with(['status' => 1, 'message' => 'Successfully created']);
            } catch (\Exception $e) {
                return view('task::create')->with(['status' => 0, 'message' => 'Error']);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \Tritiyo\Task\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        return view('task::show', ['task' => $task]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Tritiyo\Task\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {

        if (auth()->user()->isApprover(auth()->user()->id)) {
            $chunck = array(
                'task' => $task,
                'task_site' => \Tritiyo\Task\Models\TaskSite::where('task_id', $task->id)->get()->toArray(),
                'task_vehicle' => \Tritiyo\Task\Models\TaskVehicle::where('task_id', $task->id)->get()->toArray(),
                'task_material' => \Tritiyo\Task\Models\TaskMaterial::where('task_id', $task->id)->get()->toArray(),
                'task_proof' => \Tritiyo\Task\Models\TaskProof::where('task_id', $task->id)->get()->toArray(),
                'task_status' => \Tritiyo\Task\Models\TaskStatus::where('task_id', $task->id)->get()->toArray(),
            );

            //$chunck_update = \Tritiyo\Task\Models\TaskChunck::update();
            $chunck = \Tritiyo\Task\Models\TaskChunck::updateOrCreate(
                array('task_id' => $task->id),
                array('manager_data' => json_encode($chunck))
            );
        }
        return view('task::edit', ['task' => $task]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Tritiyo\Task\Models\Task $task
     * @return \Illuminate\Http\Response
     */


    public function update(Request $request, Task $task)
    {
        // Validation on server side
        // Check site head have pending bills
		
        $count_result = \Tritiyo\Task\Helpers\TaskHelper::getPendingBillCountStatus($request->site_head);
        // dd($count_result);
        if ($count_result == 'Yes') {
            return redirect()->back()->with('message', 'Your selected resource has atleast 3 pending bills. You can\'t select this resource.')->with('status', 0);
        }

        //Check If project has not budget
        $task_id = $task->id;

        if (auth()->user()->isManager(auth()->user()->id)) {
            if ($request->finish_editing == 'Yes') {
                $put = Task::find($task_id);
                $put->override_status = 'Yes';
                $put->save();
                return redirect()->back()->with('message', 'send to approver successfully')->with('status', 1);
            } else {
                /**
                 * if manager edited any data during requisition after approver data
                 * action delete this approver approved status from tasksstatus table
                 */
                if ($task->manager_override_chunck == null) {
                    TaskHelper::ManagerOverrideData($task_id);
                }
            }
        }

        //End

        $getResource = TaskSite::select('resource_id')->where('task_id', $task->id)->get();
        if (isset($getResource)) {
            $checkResource = TaskHelper::arrayExist($getResource, 'resource_id', $request->site_head);
            if ($checkResource == true) {
                return redirect()->back()->with('message', 'This person already assign as resource.please at first remove from resource')->with('status', 0);
            }
        }
        if ($request->anonymousproof_details) {
            if (auth()->user()->isManager(auth()->user()->id)) {
                /*
                 && $request->anonymous_proof_details
                Task::where('id', $task->id)
                      ->update(['anonymous_proof_details' => $request->anonymous_proof_details]);
                */


                $atts = Task::find($task->id);
                $atts->anonymous_proof_details = $request->anonymous_proof_details;
                $atts->save();

            }
            return redirect()->back()->with('message', 'Saved successfully')->with('status', 1);
        }

        if (auth()->user()->isManager(auth()->user()->id) && $request->task_assigned_to_head == 'Yes') {			
          
          	$msisdn = \App\Models\User::where('id', \Tritiyo\Task\Models\Task::where('id', $request->task_id)->first()->site_head)->first()->phone;
          	$messageBody = 'A new task is assigned to you. Please accept the task and submit multiple proofs for requisition.';
         	$random = substr(md5(mt_rand()), 0, 7);
          	$csmsId = $random;
          	
            $atts = Task::find($task->id);
            $atts->task_assigned_to_head = $request->task_assigned_to_head;
            $atts->save();

            $xx = TaskHelper::statusUpdate([
                'code' => TaskHelper::getStatusKey('task_assigned_to_head'),
                'task_id' => $request->task_id,
                'action_performed_by' => auth()->user()->id,
                'performed_for' => null,
                'requisition_id' => null,
                'message' => TaskHelper::getStatusMessage('task_assigned_to_head')
            ]);
          
          	if($xx) {
              	\App\Helpers\Mail::singleSms($msisdn, $messageBody, $csmsId);
            }
          
            return redirect()->back()->with('message', 'Saved successfully')->with('status', 1);
        }

        if (auth()->user()->isApprover(auth()->user()->id)) {
            TaskHelper::statusUpdateOrInsert([
                'code' => TaskHelper::getStatusKey('task_approver_edited'),
                'task_id' => $request->task->id,
                'action_performed_by' => auth()->user()->id,
                'performed_for' => null,
                'requisition_id' => null,
                'message' => TaskHelper::getStatusMessage('task_approver_edited')
            ]);
        }

        //dd($request->anonymous_proof_details);
        // Update Store
        $attributes = [
            'task_type' => $request->task_type,
            'project_id' => $request->project_id,
            'current_range_id' => \Tritiyo\Project\Helpers\ProjectHelper::current_range_id($request->project_id),
            'task_code' => $request->task_code ?? null,
            'task_name' => $request->task_name,
            'site_head' => $request->site_head,
            'task_details' => $request->task_details,
            'task_assigned_to_head' => $request->task_assigned_to_head,
        ];

        //return redirect()->back()->with('message', 'Edited Successfully')->with('status', 1);
        //dd($attributes);
        try {

            $task = $this->task->update($task->id, $attributes);

            return back()
                ->with('message', 'Successfully saved')
                ->with('status', 1)
                ->with('task', $task);
        } catch (\Exception $e) {
            return view('task::edit', $task->id)->with(['status' => 0, 'message' => 'Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Tritiyo\Task\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    
        $this->task->delete($id);
        TaskMaterial::where('task_id', $id)->delete();
        TaskVehicle::where('task_id', $id)->delete();
        TaskSite::where('task_id', $id)->delete();
        TaskStatus::where('task_id', $id)->delete();
       
        
       $catchAttack = [
       	'attack_name' => 'Delete',
        'attack_content' =>  'Task ID '.$id,
         'attack_by' => auth()->user()->id,
       ];
      CheckAttack::create($catchAttack);
      
       return redirect()->route('tasks.index')->with(['status' => 1, 'message' => 'Successfully deleted']);
    }


    //Vehicle
    public function taskVehicleCreate(Request $request)
    {
        //dd($request->all());
        return view('task::taskvehicle.create');
    }

    public function taskVehicleStore(Request $request)
    {
        dd($request->all());
    }

    //Task Anonymous Proof Details
    public function anonymousProof($id)
    {
        $task = Task::find($id);
        return view('task::taskanonymousproof.create', compact('task'));
    }


    // Search
    public function search(Request $request)
    {
        //dd($request);
        if ($request->search) {
            if (!empty($request->daterange)) {
                $dates = explode(' - ', $request->daterange);
                $start = $dates[0];
                $end = $dates[1];
            } else {
                $start = NULL;
                $end = NULL;
            }

            $options = [
                'q' => $request->key,
                'task_type' => $request->task_type,
                'bill_status' => $request->bill_status,
                'project_id' => $request->project_id,
                'site_head_id' => $request->site_head_id,
                'start' => $start ?? NULL,
                'end' => $end ?? NULL
            ];
            //dd($options);
            $search_result = $this->task->advanced_search($options);
            //dd($search_result);
        } else {
            $search_result = [];
        }
        return view('task::tasklist.search', compact('search_result'));
    }


    //Remaining Balance show after project select
    public function remainingBalanceOfProjectBudget($project_id)
    {

        $ranges = \Tritiyo\Project\Helpers\ProjectHelper::all_ranges($project_id);
      //dd($ranges);
        $i = 0;
        foreach ($ranges as $range) {
            if ($i == 0) {

                $exploded = explode(',', $range->status_string);
                //dump($exploded[0]);
                $range_datas0 = explode('|', $exploded[0]);
                if (count($exploded) > 1) {
                    $range_datas1 = explode('|', $exploded[1]);
                } else {
                    $today = explode('|', $exploded[0]);
                    $range_datas1 = [
                        '0' => $today[0],
                        '1' => $today[1],
                        '2' => date('Y-m-d'),
                        '3' => $today[3],
                        '4' => $today[4]
                    ];
                }

                $allCurrentBudget = \Tritiyo\Project\Helpers\ProjectHelper::current_range_budgets($range_datas0[1], $range_datas0[0]);


                //Get Use Budget of this project
                $multiple_tasks = \Tritiyo\Task\Models\Task::where('project_id', $project_id)->whereBetween('task_for', [$range_datas0[2], $range_datas1[2]])->get();


                $mobileBillx = \Tritiyo\Project\Models\MobileBill::where('project_id', $project_id)->where('range_id', $range_datas0[0])->get()->sum('received_amount');
                //dd($mobileBillx);
                //$mobileBill = (int) $mobileBillx;

                //dd($mobileBill);
                $total_requisition = \Tritiyo\Project\Helpers\ProjectHelper::ttrbGetTotalByProject('reba_amount', $project_id, $range_datas0[0]);
                $usedCurrentBudget = $total_requisition + $mobileBillx;
                //return $allCurrentBudget - $usedCurrentBudget;

                $allCurrentBudget = (int)$allCurrentBudget;

                $remaining_balance = \Tritiyo\Project\Helpers\ProjectHelper::remainingBalance($project_id, $range_datas0[0]);
                $today_use = \Tritiyo\Task\Helpers\RequisitionData::todayManagerUsedAmount($project_id, $range_datas0[0]);

                return [
                    'total' => $allCurrentBudget,
                    'usage' => $usedCurrentBudget+$today_use,
                    'remain' => round($remaining_balance - $today_use, 2)
                ];
            }
            $i++;
        }

    }

    //check site limit
    public function checkSiteLimit($site_id)
    {
        $siteUseTask = TaskSite::where('site_id', $site_id)->groupBy('task_id')->get()->count();
        $individual_site = \Tritiyo\Site\Models\Site::where('id', $site_id)->first();
        if (!empty($individual_site->site_type) && $individual_site->site_type == 'Recurring') {
            return 'true';
        } else {
            $siteLimit = \Tritiyo\Site\Models\Site::where('id', $site_id)->first()->task_limit ?? NULL;
            if ($siteLimit > $siteUseTask) {
                return 'true';
                //return $siteUseTask;
            } elseif (empty($siteLimit)) {
                return 'true';
            } elseif ($siteLimit <= $siteUseTask) {
                return 'false';
            }
        }
    }

    //cheeck resource Pending bills
    public function resourcePendingBills($reqource_id)
    {
        $count_result = \Tritiyo\Task\Helpers\TaskHelper::getPendingBillCountStatus($reqource_id);
        return $count_result;
    }

    //AJAX Total Requisition & Bill Data for Index
    public function ajaxTotalRequisitionBillIndex($task_id)
    {
        $calculate = \Tritiyo\Task\Models\TaskRequisitionBill::select('bpbr_amount', 'rpbm_amount', 'bebm_amount', 'rebc_amount', 'bebc_amount', 'reba_amount', 'beba_amount',)
            ->where('task_id', $task_id)->first();
        return view('task::tasklist.ajax_rb_total_index', compact('calculate'));
    }

}
