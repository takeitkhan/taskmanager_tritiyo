<?php

namespace Tritiyo\Task\Controllers;

use Tritiyo\Task\Helpers\TaskHelper;
use Tritiyo\Task\Models\TaskRequisitionBill;
use Tritiyo\Task\Models\Task;
use Tritiyo\Task\Repositories\TaskRequisitionBill\TaskRequisitionBillInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class TaskRequisitionBillController extends Controller
{
    /**
     * @var TaskSiteInterface
     */
    private $taskrequisitionbill;

    /**
     * RoutelistController constructor.
     * @param TaskRequisitionBillInterface $taskrequisitionbill
     */
    public function __construct(TaskRequisitionBillInterface $taskrequisitionbill)
    {
        $this->taskrequisitionbill = $taskrequisitionbill;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taskrequisitionbills = $this->tasksite->getAll();
        return view('task::index', ['taskrequisitionbills' => $taskrequisitionbills]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('task::taskrequisitionbill.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->all());
        //dd(array_sum($request->material_amount));
        $vehicleRent = (int) !empty($request->vehicle_rent) ? array_sum($request->vehicle_rent) : 0;
        $materialAmount = (int) !empty($request->material_amount) ? array_sum($request->material_amount) : 0;
        $daAmount = $request->da_amount;
        $labourAmount = $request->labour_amount;
        $otherAmount = $request->other_amount;
        $taAmount = array_sum(array_column($request->transport, 'ta_amount'));
        $paAmount = array_sum(array_column($request->purchase, 'pa_amount'));
        $totalRequisitionAmount = $vehicleRent+$materialAmount+$daAmount+$labourAmount+$otherAmount+$taAmount+$paAmount;

        $validator = Validator::make($request->all(),
            [
                'task_id' => 'required',
            ]
        );
      
      	$checkRequisitionSubmitted = TaskRequisitionBill::where('task_id', $request->task_id)->first();
        if(auth()->user()->isManager(auth()->user()->id) && !empty($checkRequisitionSubmitted) &&  $checkRequisitionSubmitted->requisition_submitted_by_manager == 'Yes'){
          return redirect()->back()->with(['status' => 1, 'message' => 'Requisition already submitted']);
        }
      
        $task_id = $request->task_id;
        // process the login
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        } else {
            // store

            //dd((array) $request->get('transport'));

            $chunck = [
                'task' => \Tritiyo\Task\Models\Task::where('id', $task_id)->get()->toArray(),
                'task_site' => \Tritiyo\Task\Models\TaskSite::where('task_id', $task_id)->get()->toArray(),
                'task_vehicle' => \Tritiyo\Task\Models\TaskVehicle::where('task_id', $task_id)->get()->toArray(),
                'task_material' => \Tritiyo\Task\Models\TaskMaterial::where('task_id', $task_id)->get()->toArray(),
                'task_regular_amount' => array(
                    'da' => array(
                        'da_amount' => $request->get('da_amount'),
                        'da_notes' => $request->get('da_notes')
                    ),
                    'labour' => array(
                        'labour_amount' => $request->get('labour_amount'),
                        'labour_notes' => $request->get('labour_notes')
                    ),
                    'other' => array(
                        'other_amount' => $request->get('other_amount'),
                        'other_notes' => $request->get('other_notes')
                    )
                ),
                'task_transport_breakdown' => array_values($request->get('transport')),
                'task_purchase_breakdown' => array_values($request->get('purchase'))
            ];

            $chunck = TaskRequisitionBill::updateOrCreate(
                array('task_id' => $task_id),
                [
                    'requisition_prepared_by_manager' => json_encode($chunck),
                    'rpbm_amount' => $totalRequisitionAmount,
                ],
            );

            $status = TaskHelper::statusUpdate([
                'code' => \Tritiyo\Task\Helpers\TaskHelper::getStatusKey('requisition_prepared_by_manager'),
                'task_id' => $task_id,
                'action_performed_by' => auth()->user()->id,
                'performed_for' => null,
                'requisition_id' => $chunck->id,
                'message' => \Tritiyo\Task\Helpers\TaskHelper::getStatusMessage('requisition_prepared_by_manager')
            ]);
            //dd($status);

            try {
                //  $taskrequisitionbill = $this->tasksite->create($arr);
                //return view('task::create', ['task' => $taskrequisitionbill]);
                return redirect(url('taskrequisitionbill/' . $chunck->id . '/edit/?task_id=' . $task_id . '&information=requisitionbillInformation'))->with(['status' => 1, 'message' => 'Successfully Saved']);
                //return redirect()->back()->with(['status' => 1, 'message' => 'Successfully Saved']);
            } catch (\Exception $e) {
                return redirect()->back()->with(['status' => 0, 'message' => 'Error']);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \Tritiyo\Task\Models\Task $taskrequisitionbill
     * @return \Illuminate\Http\Response
     */
    public function show(TaskRequisitionBill $taskrequisitionbill)
    {
        return view('task::show', ['task' => $taskrequisitionbill]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Tritiyo\Task\Models\Task $taskrequisitionbill
     * @return \Illuminate\Http\Response
     */
    public function edit(TaskRequisitionBill $taskrequisitionbill)
    {
        return view('task::taskrequisitionbill.create', ['taskrequisitionbill' => $taskrequisitionbill]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Tritiyo\Task\Models\Task $taskrequisitionbill
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //dd($request->all());
        $vehicleRent = (int) !empty($request->vehicle_rent) ? array_sum($request->vehicle_rent) : 0;
        $materialAmount = (int) !empty($request->material_amount) ? array_sum($request->material_amount) : 0;
        $daAmount = $request->da_amount;
        $labourAmount = $request->labour_amount;
        $otherAmount = $request->other_amount;
        $taAmount = array_sum(array_column($request->transport, 'ta_amount'));
        $paAmount = array_sum(array_column($request->purchase, 'pa_amount'));
        $totalRequisitionAmount = $vehicleRent+$materialAmount+$daAmount+$labourAmount+$otherAmount+$taAmount+$paAmount;

        $validator = Validator::make($request->all(),
            [
                'task_id' => 'required',
            ]
        );
      
      		$checkRequisitionSubmitted = TaskRequisitionBill::where('task_id', $request->task_id)->first();
            if(auth()->user()->isManager(auth()->user()->id) && !empty($checkRequisitionSubmitted) &&  $checkRequisitionSubmitted->requisition_submitted_by_manager == 'Yes'){
				return redirect()->back()->with(['status' => 1, 'message' => 'Requisition already submitted']);
            }elseif(auth()->user()->isCFO(auth()->user()->id) && !empty($checkRequisitionSubmitted) &&  $checkRequisitionSubmitted->requisition_approved_by_cfo == 'Yes'){
				return redirect()->back()->with(['status' => 1, 'message' => 'Requisition already submitted']);
            }elseif(auth()->user()->isAccountant(auth()->user()->id) && !empty($checkRequisitionSubmitted) &&  $checkRequisitionSubmitted->requisition_approved_by_accountant == 'Yes'){
				return redirect()->back()->with(['status' => 1, 'message' => 'Requisition already submitted']);
            }
      
        $task_id = $request->task_id;
        // process the login
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        } else {
            // store

            $chunck = [
                'task' => \Tritiyo\Task\Models\Task::where('id', $task_id)->get()->toArray(),
                'task_site' => \Tritiyo\Task\Models\TaskSite::where('task_id', $task_id)->get()->toArray(),
                'task_vehicle' => \Tritiyo\Task\Models\TaskVehicle::where('task_id', $task_id)->get()->toArray(),
                'task_material' => \Tritiyo\Task\Models\TaskMaterial::where('task_id', $task_id)->get()->toArray(),
                'task_regular_amount' => array(
                    'da' => array(
                        'da_amount' => $request->get('da_amount'),
                        'da_notes' => $request->get('da_notes')
                    ),
                    'labour' => array(
                        'labour_amount' => $request->get('labour_amount'),
                        'labour_notes' => $request->get('labour_notes')
                    ),
                    'other' => array(
                        'other_amount' => $request->get('other_amount'),
                        'other_notes' => $request->get('other_notes')
                    )
                ),
                'task_transport_breakdown' => array_values($request->get('transport')),
                'task_purchase_breakdown' => array_values($request->get('purchase'))
            ];

            //dd($chunck);
            //Task Status Code Dynamic
            if (auth()->user()->isCFO(auth()->user()->id)) {
                $taskStatusCode = 'requisition_edited_by_cfo';
                $taskrequisitionTotal = 'rebc_amount';
            } elseif (auth()->user()->isAccountant(auth()->user()->id)) {
                $taskStatusCode = 'requisition_edited_by_accountant';
                $taskrequisitionTotal = 'reba_amount';
            } elseif(auth()->user()->isManager(auth()->user()->id)) {
                $taskStatusCode = 'requisition_prepared_by_manager';
                $taskrequisitionTotal = 'rpbm_amount';
            }
            //dd($taskrequisitionTotal);
            //Data Update
            $chunck = TaskRequisitionBill::updateOrCreate(
                array('task_id' => $task_id),
                [
                    $taskStatusCode => json_encode($chunck),
                    $taskrequisitionTotal => $totalRequisitionAmount,
                ],
            );
            //dd($totalRequisitionAmount);
            $status = TaskHelper::statusUpdate([
                'code' => \Tritiyo\Task\Helpers\TaskHelper::getStatusKey($taskStatusCode),
                'task_id' => $task_id,
                'action_performed_by' => auth()->user()->id,
                'performed_for' => null,
                'requisition_id' => $chunck->id,
                'message' => \Tritiyo\Task\Helpers\TaskHelper::getStatusMessage($taskStatusCode)
            ]);

        }
        try {
            //$taskrequisitionbill = $this->task->update($taskrequisitionbill->id, $attributes);

            return back()->with('message', 'Successfully saved')->with('status', 1);
            // ->with('task', $taskrequisitionbill);
        } catch (\Exception $e) {
            return view('task::edit', $taskrequisitionbill->id)->with(['status' => 0, 'message' => 'Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Tritiyo\Task\Models\Task $taskrequisitionbill
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->taskrequisitionbill->delete($id);
        return redirect()->back()->with(['status' => 1, 'message' => 'Successfully deleted']);
    }


    /**** Bill Part Start ****/
    public function add_bill($id)
    {
        $checkRequsionApprove = TaskRequisitionBill::where('task_id', $id)->first();
      
      	$checkBillSubmitted = TaskRequisitionBill::where('task_id', $id)->first();
        if(auth()->user()->isResource(auth()->user()->id) && !empty($checkBillSubmitted) &&  $checkBillSubmitted->bill_submitted_by_resource == 'Yes'){
          return redirect()->back()->with(['status' => 1, 'message' => 'bill already submitted']);
        }
      
        //dd($checkRequsionApprove);
        if(!empty($checkRequsionApprove) && $checkRequsionApprove->requisition_approved_by_accountant =='Yes'){
            $task = Task::find($id);
            return view('task::taskrequisitionbill.add_bill', compact('task'))->with(['status' => 0, 'message' => 'Error']);
        } else {
           return redirect()->route('tasks.index')->with('message', 'Requisition is not approve on this task')->with('status', 0);
        }
    }

    /**
     *  Bill Update
     *
     */
    public function billUpdate(Request $request){
            $vehicleRent = array_sum(array_column($request->vehicle, 'vehicle_rent'));
            $materialAmount = array_sum(array_column($request->material, 'material_amount'));
            $daAmount = $request->da_amount;
            $labourAmount = $request->labour_amount;
            $otherAmount = $request->other_amount;
            $taAmount = array_sum(array_column($request->transport, 'ta_amount'));
            $paAmount = array_sum(array_column($request->purchase, 'pa_amount'));
            $totalBillAmount = $vehicleRent+$materialAmount+$daAmount+$labourAmount+$otherAmount+$taAmount+$paAmount;
            //dd($totalBillAmount);

            $validator = Validator::make($request->all(),
                [
                    'task_id' => 'required',
                ]
            );
      
      		/** Bill Check If Allready Submitted */
      		$checkBillSubmitted = TaskRequisitionBill::where('task_id', $request->task_id)->first();
            if(auth()->user()->isManager(auth()->user()->id) && !empty($checkBillSubmitted) &&  $checkBillSubmitted->bill_approved_by_manager == 'Yes'){
				return redirect()->back()->with(['status' => 1, 'message' => 'bill already submitted']);
            }elseif(auth()->user()->isCFO(auth()->user()->id) && !empty($checkBillSubmitted) &&  $checkBillSubmitted->bill_approved_by_cfo == 'Yes'){
				return redirect()->back()->with(['status' => 1, 'message' => 'bill already submitted']);
            }elseif(auth()->user()->isAccountant(auth()->user()->id) && !empty($checkBillSubmitted) &&  $checkBillSubmitted->bill_approved_by_accountant == 'Yes'){
				return redirect()->back()->with(['status' => 1, 'message' => 'bill already submitted']);
            }
      
            $task_id = $request->task_id;
            // process the login
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            } else {
                // store
                if($request->vehicle[0]['vehicle_id'] == 'None') {
                $vehicle = NULL;
                } else {
                $vehicle = $request->get('vehicle');
                }
                if($request->material[0]['material_id']  == 'None') {
                $material = NULL;
                } else {
                $material = $request->get('material');
                }
                
            
                $chunck = [
                    'task' => \Tritiyo\Task\Models\Task::where('id', $task_id)->get()->toArray(),
                    'task_site' => \Tritiyo\Task\Models\TaskSite::where('task_id', $task_id)->get()->toArray(),
                    'task_vehicle' => $vehicle,
                    'task_material' => $material,
                    'task_regular_amount' => array(
                        'da' => array(
                            'da_amount' => $request->get('da_amount'),
                            'da_notes' => $request->get('da_notes')
                        ),
                        'labour' => array(
                            'labour_amount' => $request->get('labour_amount'),
                            'labour_notes' => $request->get('labour_notes')
                        ),
                        'other' => array(
                            'other_amount' => $request->get('other_amount'),
                            'other_notes' => $request->get('other_notes')
                        )
                    ),
                    'task_transport_breakdown' => array_values($request->get('transport')),
                    'task_purchase_breakdown' => array_values($request->get('purchase'))
                ];
            
                //dd($chunck);


                //Task Status Code Dynamic
                if (auth()->user()->isCFO(auth()->user()->id)) {
                    $taskStatusCode = 'bill_edited_by_cfo';
                    $taskBillAmount = 'bebc_amount';
                } elseif (auth()->user()->isAccountant(auth()->user()->id)) {
                    $taskStatusCode = 'bill_edited_by_accountant';
                    $taskBillAmount = 'beba_amount';
                } elseif(auth()->user()->isManager(auth()->user()->id)) {
                    $taskStatusCode = 'bill_edited_by_manager';
                    $taskBillAmount = 'bebm_amount';
                } elseif(auth()->user()->isResource(auth()->user()->id)) {
                    $taskStatusCode = 'bill_prepared_by_resource';
                    $taskBillAmount = 'bpbr_amount';
                }

                //Data Update
                $chunck = TaskRequisitionBill::updateOrCreate(
                    array('task_id' => $task_id),
                    [
                        $taskStatusCode => json_encode($chunck),
                        $taskBillAmount => $totalBillAmount,
                    ]
                );
                $status = TaskHelper::statusUpdate([
                    'code' => \Tritiyo\Task\Helpers\TaskHelper::getStatusKey($taskStatusCode),
                    'task_id' => $task_id,
                    'action_performed_by' => auth()->user()->id,
                    'performed_for' => null,
                    'requisition_id' => $chunck->id,
                    'message' => \Tritiyo\Task\Helpers\TaskHelper::getStatusMessage($taskStatusCode)
                ]);

            }
            try {
                //$taskrequisitionbill = $this->task->update($taskrequisitionbill->id, $attributes);

                return back()->with('message', 'Successfully saved')->with('status', 1);
                // ->with('task', $taskrequisitionbill);
            } catch (\Exception $e) {
                //return view('task::edit', $taskrequisitionbill->id)->with(['status' => 0, 'message' => 'Error']);
            }
       
    }


    public function requisitionApproveAmountSend(Request $request){
        $data = TaskRequisitionBill::find($request->requesition_id);
        $data->requisition_approve_amount_accountant = $request->mobile_bank_method.' | '.$request->mobile_bank_number. ' | '.$request->requesition_approve_amount;
        $data->save();
        return back()->with('message', 'Successfully Saved. Now approve requisition')->with('status', 1);
    }

}


