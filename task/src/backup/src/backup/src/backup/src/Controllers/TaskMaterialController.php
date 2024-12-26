<?php

namespace Tritiyo\Task\Controllers;
use Tritiyo\Task\Models\Task;
use Tritiyo\Task\Helpers\TaskHelper;
use Tritiyo\Task\Models\TaskMaterial;
use Tritiyo\Task\Repositories\TaskMaterial\TaskMaterialInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class TaskMaterialController extends Controller
{
    /**
     * @var TaskSiteInterface
     */
    private $taskmaterial;

    /**
     * RoutelistController constructor.
     * @param TaskMaterialInterface $taskmaterial
     */
    public function __construct(TaskMaterialInterface $taskmaterial)
    {
        $this->taskmaterial = $taskmaterial;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taskmaterials = $this->tasksite->getAll();
        return view('task::index', ['tasksites' => $taskmaterials]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('task::taskmaterial.create');
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
                'material_id' => 'required',
                'material_qty' => 'required',
            ]
        );
        // process the login
        if ($validator->fails()) {
            return redirect('tasks.create')
                ->withErrors($validator)
                ->withInput();
        } elseif ($request->material_id) {
            // store

            foreach ($request->material_id as $key => $row) {
                $attributes = [
                    'task_id' => $request->task_id,
                    'material_id' => $request->material_id[$key],
                    'material_qty' => $request->material_qty[$key],
                    'material_amount' => $request->material_amount[$key],
                    'material_note' => $request->material_note[$key],
                ];
                $taskmaterial = $this->taskmaterial->create($attributes);
            }

            try {
                //  $taskmaterial = $this->tasksite->create($arr);
                //return view('task::create', ['task' => $taskmaterial]);
                return redirect(route('tasks.index'))->with(['status' => 1, 'message' => 'Successfully created']);
            } catch (\Exception $e) {
                return view('task::create')->with(['status' => 0, 'message' => 'Error']);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \Tritiyo\Task\Models\Task $taskmaterial
     * @return \Illuminate\Http\Response
     */
    public function show(TaskSite $taskmaterial)
    {
        return view('task::show', ['task' => $taskmaterial]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Tritiyo\Task\Models\Task $taskmaterial
     * @return \Illuminate\Http\Response
     */
    public function edit(TaskSite $taskmaterial)
    {
        return view('task::taskmaterial.create', ['task' => $taskmaterial]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Tritiyo\Task\Models\Task $taskmaterial
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
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
        //dd($request->all());

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
        if ($request->task_id) {
            $t = TaskMaterial::where('task_id', $request->task_id);
            $t->delete();
            if (!empty($request->material_id)) {
                foreach ($request->material_id as $key => $row) {
                    $attributes = [
                        'task_id' => $request->task_id,
                        'material_id' => $request->material_id[$key],
                        'material_qty' => $request->material_qty[$key],
                        'material_amount' => $request->material_amount[$key],
                        'material_note' => $request->material_note[$key],
                    ];
                    $taskmaterial = $this->taskmaterial->create($attributes);
                }
            }
        }
        //dd($request->all());
        try {
            //$taskmaterial = $this->task->update($taskmaterial->id, $attributes);

            return back()->with('message', 'Successfully saved')->with('status', 1);
            // ->with('task', $taskmaterial);
        } catch (\Exception $e) {
            return view('task::edit', $taskmaterial->id)->with(['status' => 0, 'message' => 'Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Tritiyo\Task\Models\Task $taskmaterial
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->task->delete($id);
        return redirect()->back()->with(['status' => 1, 'message' => 'Successfully deleted']);
    }

}
