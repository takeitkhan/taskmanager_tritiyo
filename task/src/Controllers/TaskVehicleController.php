<?php

namespace Tritiyo\Task\Controllers;

use Tritiyo\Task\Models\Task;
use Tritiyo\Task\Helpers\TaskHelper;
use Tritiyo\Task\Models\TaskVehicle;
use Tritiyo\Task\Repositories\TaskVehicle\TaskVehicleInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class TaskVehicleController extends Controller
{
    /**
     * @var TaskSiteInterface
     */
    private $taskvehicle;

    /**
     * RoutelistController constructor.
     * @param TaskVehicleInterface $taskvehicle
     */
    public function __construct(TaskVehicleInterface $taskvehicle)
    {
        $this->taskvehicle = $taskvehicle;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taskvehicles = $this->tasksite->getAll();
        return view('task::index', ['tasksites' => $taskvehicles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('task::taskvehicle.create');
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
                'task_id' => 'required',
                'vehicle_id' => 'required',
                'vehicle_rent' => 'required',
            ]
        );
        // process the login
        if ($validator->fails()) {
            return redirect('tasks.create')
                ->withErrors($validator)
                ->withInput();
        } elseif ($request->vehicle_id) {
            // store

            foreach ($request->vehicle_id as $key => $row) {
                $attributes = [
                    'task_id' => $request->task_id,
                    'vehicle_id' => $request->vehicle_id[$key],
                    'vehicle_rent' => $request->vehicle_rent[$key],
                    'vehicle_note' => $request->vehicle_note[$key],
                ];
                $taskvehicle = $this->taskvehicle->create($attributes);
            }

            try {
                //  $taskvehicle = $this->tasksite->create($arr);
                //return view('task::create', ['task' => $taskvehicle]);
                return redirect(route('tasks.index'))->with(['status' => 1, 'message' => 'Successfully created']);
            } catch (\Exception $e) {
                return view('task::create')->with(['status' => 0, 'message' => 'Error']);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \Tritiyo\Task\Models\Task $taskvehicle
     * @return \Illuminate\Http\Response
     */
    public function show(TaskSite $taskvehicle)
    {
        return view('task::show', ['task' => $taskvehicle]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Tritiyo\Task\Models\Task $taskvehicle
     * @return \Illuminate\Http\Response
     */
    public function edit(TaskSite $taskvehicle)
    {
        return view('task::taskvehicle.create', ['task' => $taskvehicle]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Tritiyo\Task\Models\Task $taskvehicle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //dd($request->all());
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
        //dd($request->all());
        if ($request->task_id) {
            $t = TaskVehicle::where('task_id', $request->task_id);
            $t->delete();
            if (!empty($request->vehicle_id)) {
                foreach ($request->vehicle_id as $key => $row) {
                    $attributes = [
                        'task_id' => $request->task_id,
                        'vehicle_id' => $request->vehicle_id[$key],
                        'vehicle_rent' => $request->vehicle_rent[$key],
                        'vehicle_note' => $request->vehicle_note[$key],
                    ];
                    $taskvehicle = $this->taskvehicle->create($attributes);
                }
            }
        }
        //dd($request->all());
        try {
            //$taskvehicle = $this->task->update($taskvehicle->id, $attributes);

            return back()->with('message', 'Successfully saved')->with('status', 1);
            // ->with('task', $taskvehicle);
        } catch (\Exception $e) {
            return view('task::edit', $taskvehicle->id)->with(['status' => 0, 'message' => 'Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Tritiyo\Task\Models\Task $taskvehicle
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
