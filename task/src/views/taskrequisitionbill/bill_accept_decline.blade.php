<div class="card tile is-child">
    <header class="card-header">
        <p class="card-header-title p-1" style="">
            <span class="icon"><i class="fas fa-tasks default"></i></span>
            Action Panel
        </p>
    </header>
    <div class="card-content">
        <div class="card-data">
            <div class="columns">
                <div class="column">


                    {{-- New Button Set --}}
                    <?php
                    global $taskID;
                    $taskID = $task->id;
                    global $requisition_id;
                    $requisition_id = !empty($taskrequisitionbill) ? $taskrequisitionbill->id : NULL;

                    function rData($arg)
                    {
                        global $taskID;
                        global $requisition_id;
                        $data = Tritiyo\Task\Models\TaskRequisitionBill::where('task_id', $taskID)->first();
                        if (isset($data) && $data[$arg]) {
                            return 'Yes';
                        } else {
                            return 'No';
                        }
                    }
                    function rDataApprove($arg)
                    {
                        global $taskID;
                        global $requisition_id;
                        $data = Tritiyo\Task\Models\TaskRequisitionBill::where('task_id', $taskID)->first();
                        if (isset($data) && $data[$arg]) {
                            return $data[$arg];
                        }
                    }
                    //dump(rDataApprove('requisition_approved_by_cfo') );
                    ?>


                    @if(auth()->user()->isResource(auth()->user()->id) && rData('bill_prepared_by_resource') == 'Yes')
                        @php
                            echo Tritiyo\Task\Helpers\RequisitionBillHelper::requisitionBillActionHelper([
                                'approve_code' => 'bill_submitted_by_resource',
                                'task_id' => $task->id,
                                'action_performed_by' => auth()->user()->id,
                                'performed_for' => null,
                                'requisition_id' => $requisition_id,
                                'message' => null,
                                'buttonValue' => 'Submit Bill',
                                'showOrNot' => false
                            ]);
                        @endphp
                    @endif

                    @if(auth()->user()->isManager(auth()->user()->id) && rData('bill_edited_by_manager') == 'Yes')
                        @php
                            echo Tritiyo\Task\Helpers\RequisitionBillHelper::requisitionBillActionHelper([
                                'approve_code' => 'bill_approved_by_manager',
                                'task_id' => $task->id,
                                'action_performed_by' => auth()->user()->id,
                                'performed_for' => null,
                                'requisition_id' => $requisition_id,
                                'message' => null,
                                'buttonValue' => 'Bill Send to CFO',
                                'showOrNot' => false
                            ]);
                        @endphp
                    @endif

                    @if(auth()->user()->isCFO(auth()->user()->id) && rData('bill_edited_by_cfo') == 'Yes')
                        @php
                            echo Tritiyo\Task\Helpers\RequisitionBillHelper::requisitionBillActionHelper([
                                'approve_code' => 'bill_approved_by_cfo',
                                'task_id' => $task->id,
                                'action_performed_by' => auth()->user()->id,
                                'performed_for' => null,
                                'requisition_id' => $requisition_id,
                                'message' => null,
                                'buttonValue' => 'Bill Send to Accountant',
                                'showOrNot' => false
                            ]);
                        @endphp
                    @endif

                    @if(auth()->user()->isAccountant(auth()->user()->id) && rData('bill_edited_by_accountant') == 'Yes')
                        @php
                            echo Tritiyo\Task\Helpers\RequisitionBillHelper::requisitionBillActionHelper([
                                'approve_code' => 'bill_approved_by_accountant',
                                'task_id' => $task->id,
                                'action_performed_by' => auth()->user()->id,
                                'performed_for' => null,
                                'requisition_id' => $requisition_id,
                                'message' => null,
                                'buttonValue' => 'Bill adjusted',
                                'showOrNot' => false
                            ]);
                        @endphp
                    @endif

                </div>
                <div class="column">

                    <div class="statusSuccessMessage">
                        {{ \Tritiyo\Task\Models\TaskStatus::where('task_id', $task->id)->orderBy('id', 'desc')->first()->message }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

