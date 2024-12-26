<div class="card tile is-child">
    <header class="card-header">
        <p class="card-header-title" style="">
            <span class="icon"><i class="fas fa-tasks default"></i></span>
            Action Panel
        </p>
    </header>
    <div class="card-content">
        <div class="card-data">
            <div class="columns">
                <div class="column">
                    @if(auth()->user()->isManager(auth()->user()->id) && $task->override_status == 'No')
                        {{ Form::open(array('url' => route('tasks.update', $task->id), 'method' => 'PUT', 'value' => 'PATCH', 'id' => 'add_route', 'class' => 'task_table', 'files' => true, 'autocomplete' => 'off')) }}
                        <input type="hidden" name="finish_editing" value="Yes"/>
                        <button onclick="return confirm('Are you sure?')" type="submit" class="button is-info is-small">
                            Finish editing and send to approver
                        </button>
                        {{ Form::close() }}
                    @endif

                    @if(auth()->user()->isManager(auth()->user()->id))
                        @include('task::taskaction.ready_for_assign_to_head')
                    @endif


                    @if(auth()->user()->isApprover(auth()->user()->id))
                        @include('task::taskaction.task_approver_accept_decline')
                    @endif


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


                    @if(auth()->user()->isManager(auth()->user()->id) && rData('requisition_prepared_by_manager') == 'Yes')
                        @php
                            echo Tritiyo\Task\Helpers\RequisitionBillHelper::requisitionBillActionHelper([
                                'approve_code' => 'requisition_submitted_by_manager',
                                'task_id' => $task->id,
                                'action_performed_by' => auth()->user()->id,
                                'performed_for' => null,
                                'requisition_id' => $requisition_id,
                                'message' => null,
                                'buttonValue' => 'Send to CFO',
                                'showOrNot' => false
                            ]);
                        @endphp
                    @endif


                    @if(auth()->user()->isCFO(auth()->user()->id) && (rData('requisition_edited_by_cfo') == 'Yes' || rDataApprove('requisition_approved_by_cfo') != null))
                        @php
                            echo Tritiyo\Task\Helpers\RequisitionBillHelper::requisitionBillActionHelper([
                                'approve_code' => 'requisition_approved_by_cfo',
                                'decline_code' => 'requisition_declined_by_cfo',
                                'task_id' => $task->id,
                                'action_performed_by' => auth()->user()->id,
                                'performed_for' => null,
                                'requisition_id' => $requisition_id,
                                'message' => null,
                                'buttonValue' => 'Send to Accountant',
                                'showOrNot' => true
                            ]);
                        @endphp
                    @endif

                    @if(auth()->user()->isAccountant(auth()->user()->id) && (rData('requisition_edited_by_accountant') == 'Yes' || rDataApprove('requisition_approved_by_accountant') != null))
                        @php
                            echo Tritiyo\Task\Helpers\RequisitionBillHelper::requisitionBillActionHelper([
                                'approve_code' => 'requisition_approved_by_accountant',
                                'decline_code' => 'requisition_declined_by_accountant',
                                'task_id' => $task->id,
                                'action_performed_by' => auth()->user()->id,
                                'performed_for' => null,
                                'requisition_id' => $requisition_id,
                                'message' => null,
                                'buttonValue' => 'Requisition Approve',
                                'showOrNot' => true
                            ]);
                        @endphp
                    @endif


                </div>
                <div class="column">
                    <div class="statusSuccessMessage">
                        {{ \Tritiyo\Task\Models\TaskStatus::where('task_id', $task->id)->orderBy('id', 'desc')->first()->message }}
                    </div>
                    @if(auth()->user()->isApprover(auth()->user()->id) && ($task->override_status == 'Yes' || $task->override_status == 'Overriden'))
                    <a href="{{route('tasks.manager.overridden.data', $task->id)}}" target="_blank">
                        <div class="statusSuccessMessage has-background-link-dark mt-2 has-text-white-ter">
                            Previous Data of Manager
                        </div>
                    </a>
                    @endif
                </div>
            </div>
            <div class="columns">
                <div class="column">
                    @if( auth()->user()->isManager(auth()->user()->id) && $task->override_status == 'No')
                        <article class="message is-danger">
                            <div class="message-body">
                                ফিনিশ এডিটিং বাটন চাপার পর আপনি কখনোই আর পরিবর্তন করতে পারবেন না। তাই ভালো ভাবে দেখে
                                এরপর ফিনিশ বাটন চাপুন।
                            </div>
                        </article>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

