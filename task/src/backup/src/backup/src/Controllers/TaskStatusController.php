<?php

namespace Tritiyo\Task\Controllers;

use Tritiyo\Task\Models\Task;
use Tritiyo\Task\Models\TaskRequisitionBill;
use Tritiyo\Task\Models\TaskStatus;
use Tritiyo\Task\Helpers\TaskHelper;
use Tritiyo\Task\Repositories\TaskStatus\TaskStatusInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class TaskStatusController extends Controller
{
    /**
     * @var TaskSiteInterface
     */
    private $taskstatus;

    /**
     * RoutelistController constructor.
     * @param TaskStatusInterface $taskstatus
     */
    public function __construct(TaskStatusInterface $taskstatus)
    {
        $this->taskstatus = $taskstatus;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taskstatuss = $this->tasksite->getAll();
        return view('task::index', ['tasksites' => $taskstatuss]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('task::taskstatus.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $taskInfo = Task::where('id', $request->task_id)->first();
        if (auth()->user()->isApprover(auth()->user()->id) && $taskInfo->override_status == 'Yes') {
            $put = Task::find($taskInfo->id);
            $put->override_status = 'Overriden';
            $put->save();
        }
//        dd($request->all());
        $validator = Validator::make($request->all(),
            [
                'task_id' => 'required'
            ]
        );
        // process the login
        if ($validator->fails()) {
            return redirect('tasks.show', $request->task_id)
                ->withErrors($validator)
                ->withInput();
        } else {
            //$taskMsgHandler = $request->task_message_handler;

            if (!empty($request->accept['submit']) || !empty($request->accept['approve'])  && $request->accept['approve'] == 'Approve') {
                $status = !empty($request->accept['status']) ? $request->accept['status'] : null;
                $key = TaskHelper::getStatusKey($request->accept['approve_code']);
                $message = TaskHelper::getStatusMessage($request->accept['approve_code']);
            } elseif (!empty($request->decline['submit']) || !empty($request->decline['decline']) && $request->decline['decline'] == 'Decline') {
                $status = !empty($request->decline['status']) ? $request->decline['status']: null;
                $key = TaskHelper::getStatusKey($request->decline['decline_code']);
                $message = TaskHelper::getStatusMessage($request->decline['decline_code']);
            } else {
                return redirect(route('tasks.index'))->with(['status' => 1, 'message' => 'Nothing performed']);
            }

            TaskHelper::statusUpdate([
                'code' => $key,
                'task_id' => $request->task_id,
                'action_performed_by' => auth()->user()->id,
                'performed_for' => null,
                'requisition_id' => $request->requisition_id,
                'message' => $message
            ]);


            //Requistion Bill Table Data Update
            if (!empty($status) && !empty($request->requisition_id)) {
                \Tritiyo\Task\Helpers\RequisitionBillHelper::RequisitionBillstatusUpdateOrInsert([
                    'code' => $key,
                    'columnName' => $request->accept['approve_code'],
                    'task_id' => $request->task_id,
                    'requisition_id' => $request->requisition_id,
                    'message' => $status
                ]);
            }


            try {
                //return redirect(route('tasks.show', $request->task_id))->with(['status' => 1, 'message' => 'Successfully created']);
                return redirect()->back()->with(['status' => 1, 'message' => 'Successfully Applied']);

            } catch (\Exception $e) {
                return view('task::create')->with(['status' => 0, 'message' => 'Error']);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \Tritiyo\Task\Models\Task $taskstatus
     * @return \Illuminate\Http\Response
     */
    public function show(TaskSite $taskstatus)
    {
        return view('task::show', ['task' => $taskstatus]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Tritiyo\Task\Models\Task $taskstatus
     * @return \Illuminate\Http\Response
     */
    public function edit(TaskSite $taskstatus)
    {
        return view('task::taskstatus.create', ['task' => $taskstatus]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Tritiyo\Task\Models\Task $taskstatus
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //dd($request->all());

        $t = TaskStatus::where('task_id', $request->task_id);
        $t->delete();
        foreach ($request->vehicle_id as $key => $row) {
            $attributes = [
                'task_id' => $request->task_id,
                'vehicle_id' => $request->vehicle_id[$key],
                'vehicle_rent' => $request->vehicle_rent[$key],
            ];
            $taskstatus = $this->taskstatus->create($attributes);
        }
        //dd($request->all());
        try {
            //$taskstatus = $this->task->update($taskstatus->id, $attributes);

            return back()->with('message', 'Successfully saved')->with('status', 1);
            // ->with('task', $taskstatus);
        } catch (\Exception $e) {
            return view('task::edit', $taskstatus->id)->with(['status' => 0, 'message' => 'Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Tritiyo\Task\Models\Task $taskstatus
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->task->delete($id);
        return redirect()->back()->with(['status' => 1, 'message' => 'Successfully deleted']);
    }

}
