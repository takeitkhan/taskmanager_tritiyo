<?php

namespace Tritiyo\Task\Controllers;

use Tritiyo\Task\Helpers\TaskHelper;
use Tritiyo\Task\Models\Task;
use Tritiyo\Site\Models\Site;
use Tritiyo\Task\Models\TaskSite;
use Tritiyo\Task\Repositories\TaskSiteInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class TaskSiteController extends Controller
{
    /**
     * @var TaskSiteInterface
     */
    private $tasksite;

    /**
     * RoutelistController constructor.
     * @param TaskSiteInterface $tasksite
     */
    public function __construct(TaskSiteInterface $tasksite)
    {
        $this->tasksite = $tasksite;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasksites = $this->tasksite->getAll();
        return view('task::index', ['tasksites' => $tasksites]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('task::tasksite.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Not in action
        $validator = Validator::make($request->all(),
            [
                'task_id' => 'required',
                'site_id' => 'required',
                'resource_id' => 'required',
            ]
        );
        // process the login
        if ($validator->fails()) {
            return redirect('tasks.create')
                ->withErrors($validator)
                ->withInput();
        } else {

        }
    }

    /**
     * Display the specified resource.
     *
     * @param \Tritiyo\Task\Models\Task $tasksite
     * @return \Illuminate\Http\Response
     */
    public function show(TaskSite $tasksite)
    {
        return view('task::show', ['task' => $tasksite]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Tritiyo\Task\Models\Task $tasksite
     * @return \Illuminate\Http\Response
     */
    public function edit(TaskSite $tasksite)
    {
        return view('task::edit', ['task' => $tasksite]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Tritiyo\Task\Models\Task $tasksite
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        //wwwwwwwdd($request->site_id);
        foreach($request->site_id as $site) {
            //$count_result = \Tritiyo\Task\Helpers\TaskHelper::getPendingBillCountStatus($reqource_id);
        }

        foreach($request->resource_id as $resource_id) {
            $count_result = \Tritiyo\Task\Helpers\TaskHelper::getPendingBillCountStatus($resource_id);
        }
        /**
         * if manager edited any data during requisition after approver data
         * action delete this approver approved status from tasksstatus table
         */
        if (auth()->user()->isManager(auth()->user()->id)) {
            $task_id = $request->task_id;

            if(Task::where('id', $task_id)->first()->manager_override_chunck == null){
                TaskHelper::ManagerOverrideData($task_id);
            }
        }
        //End

        if (auth()->user()->isApprover(auth()->user()->id)) {
            TaskHelper::statusUpdateOrInsert([
                'code' => TaskHelper::getStatusKey('task_approver_edited'),
                'task_id' => $request->task_id,
                'action_performed_by' => auth()->user()->id,
                'performed_for' => null,
                'requisition_id' => null,
                'message' => TaskHelper::getStatusMessage('task_approver_edited')
            ]);
        }

        $arr = array();
        $t = TaskSite::where('task_id', $request->task_id);
        $t->delete();
        foreach ($request->site_id as $key => $row) {
            foreach ($request->resource_id as $k => $r) {
                $arr['task_id'] = $request->task_id;
                $arr['site_id'] = $row;
                $arr['resource_id'] = $r;
              	$arr['task_type'] = \Tritiyo\Task\Models\Task::where('id', $request->task_id)->first()->task_type;
                $arr['task_for'] = \Tritiyo\Task\Models\Task::where('id', $request->task_id)->first()->task_for;
                $arr['created_at'] = now();
                $arr['updated_at'] = now();

              	//dd($arr);
                $t->insert($arr);
            }
        }


        //Site completion status update to running
        foreach($request->site_id as $key => $row){
          $site = Site::find($row);
          $site->completion_status = 'Running';
          $site->save();
        }
        //dd($request->all());
        //return redirect()->route('tasks.site.edit', $request->task_id)->with('message', 'Successfully saved')->with('status', 1);
        try {
            //$tasksite = $this->task->update($tasksite->id, $attributes);

            return back()->with('message', 'Successfully saved')->with('status', 1);
            // ->with('task', $tasksite);
        } catch (\Exception $e) {
            return view('task::edit', $tasksite->id)->with(['status' => 0, 'message' => 'Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Tritiyo\Task\Models\Task $tasksite
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->task->delete($id);
        return redirect()->back()->with(['status' => 1, 'message' => 'Successfully deleted']);
    }

    public function taskSitebyTaskId($id)
    {
        $taskSites = TaskSite::where('task_id', $id)->get();
        $taskId = $id;
        return view('task::tasksite.create', compact('taskSites', 'taskId'));
        // }
    }

}
