<?php

namespace Tritiyo\Task\Controllers;

use Tritiyo\Task\Models\TaskProof;
use Tritiyo\Task\Helpers\TaskHelper;
use Tritiyo\Task\Repositories\TaskProof\TaskProofInterface;
use Tritiyo\Task\Repositories\TaskStatus\TaskStatusInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class TaskProofController extends Controller
{
    /**
     * @var TaskSiteInterface
     */
    private $taskproof;

    /**
     * @var TaskSiteInterface
     */
    private $taskstatus;

    /**
     * RoutelistController constructor.
     * @param TaskProofInterface $taskproof
     * @param TaskStatusInterface $taskstatus
     */
    public function __construct(TaskProofInterface $taskproof, TaskStatusInterface $taskstatus)
    {
        $this->taskproof = $taskproof;
        $this->taskstatus = $taskstatus;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taskproofs = $this->tasksite->getAll();
        return view('task::index', ['tasksites' => $taskproofs]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('task::taskproof.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->file('resource_proof'));
        if (isset($request->task_id)) {

            $request->validate([
                //'resource_proof' => 'required', //|max:2048,
                //'vehicle_proof' => 'required', //|max:2048,
                //'material_proof' => 'required' //|max:2048,
            ]);

            //dd($request->anonymous_proof);
            //Resource Image Proof
            if($request->file('resource_proof') != null){
                $resource_proof_image = [];
                foreach ($request->resource_proof as $items) {
                    //dd($items);
                    $resource_proof = time() . $items->getClientOriginalName();
                    $resource_proof_image[] = date('Y') . date('m') . '/' . time() . $items->getClientOriginalName();
                    $resource_proof_data = $items->move(public_path('proofs/' . date('Y') . date('m')), $resource_proof);
                }
                $resourceProofImage =  implode(' | ', $resource_proof_image);
            } else {
                $resourceProofImage = null;
            }
            //Vehicle Image Proof
            if($request->file('vehicle_proof') != null){
                $vehicle_proof_image = [];
                foreach ($request->vehicle_proof as $items) {
                    $vehicle_proof = time() . $items->getClientOriginalName();
                    $vehicle_proof_image[] = date('Y') . date('m') . '/' . time() . $items->getClientOriginalName();
                    $vehicle_proof_data = $items->move(public_path('proofs/' . date('Y') . date('m')), $vehicle_proof);
                }
                $vehicleProofImage = implode(' | ', $vehicle_proof_image);
            }else {
                $vehicleProofImage = null;
            }
            
            //Material Image Proof
            if($request->file('material_proof') != null){
                $material_proof_image = [];
                foreach ($request->material_proof as $items) {
                    $material_proof = time() . $items->getClientOriginalName();
                    $material_proof_image[] = date('Y') . date('m') . '/' . time() . $items->getClientOriginalName();
                    $material_proof_data = $items->move(public_path('proofs/' . date('Y') . date('m')), $material_proof);
                }
                $materialProofImage = implode(' | ', $material_proof_image);
            } else {
                $materialProofImage = null;
            }
            //Anonymous image proof
            if($request->file('anonymous_proof') != null){
                $anonymous_proof_image = [];
                foreach ($request->anonymous_proof as $items) {
                    $anonymous_proof = time() . $items->getClientOriginalName();
                    $anonymous_proof_image[] = date('Y') . date('m') . '/' . time() . $items->getClientOriginalName();
                    $anonymous_proof_data = $items->move(public_path('proofs/' . date('Y') . date('m')), $anonymous_proof);
                }
                $anonymousProofImage = implode(' | ', $anonymous_proof_image);
            } else {
                $anonymousProofImage = null;
            }
            //dd($anonymous_proof_image);
            

            $attributes = [
                'task_id' => $request->task_id,
                'proof_sent_by' => auth()->user()->id,
                'resource_proof' => $resourceProofImage,
                'vehicle_proof' => $vehicleProofImage,
                'material_proof' => $materialProofImage,
                'anonymous_proof' => $anonymousProofImage,
                'lat_proof' => null,
                'long_proof' => null
            ];
            //dd($attributes);
            $taskproof = $this->taskproof->create($attributes);

            TaskHelper::statusUpdate([
                'code' => TaskHelper::getStatusKey('proof_given'),
                'task_id' => $request->task_id,
                'action_performed_by' => auth()->user()->id,
                'performed_for' => null,
                'requisition_id' => null,
                'message' => TaskHelper::getStatusMessage('proof_given')
            ]);

            if ($taskproof == true) {
                return back()
                    ->with('message', 'You have successfully upload image.')->with('status', '1');
            }
        } else {
            return 'You have not posted any proof under this task.';
        }

    }

    /**
     * Display the specified resource.
     *
     * @param \Tritiyo\Task\Models\Task $taskproof
     * @return \Illuminate\Http\Response
     */
    public function show(TaskSite $taskproof)
    {
        return view('task::show', ['task' => $taskproof]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Tritiyo\Task\Models\Task $taskproof
     * @return \Illuminate\Http\Response
     */
    public function edit(TaskSite $taskproof)
    {
        return view('task::taskproof.create', ['task' => $taskproof]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Tritiyo\Task\Models\Task $taskproof
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //dd($request->all());

        $t = TaskProof::where('task_id', $request->task_id);
        $t->delete();
        foreach ($request->vehicle_id as $key => $row) {
            $attributes = [
                'task_id' => $request->task_id,
                'vehicle_id' => $request->vehicle_id[$key],
                'vehicle_rent' => $request->vehicle_rent[$key],
            ];
            $taskproof = $this->taskproof->create($attributes);
        }
        //dd($request->all());
        try {
            //$taskproof = $this->task->update($taskproof->id, $attributes);

            return back()->with('message', 'Successfully saved')->with('status', 1);
            // ->with('task', $taskproof);
        } catch (\Exception $e) {
            return view('task::edit', $taskproof->id)->with(['status' => 0, 'message' => 'Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Tritiyo\Task\Models\Task $taskproof
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
