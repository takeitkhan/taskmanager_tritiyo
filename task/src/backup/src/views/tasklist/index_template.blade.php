<div class="column is-3">
    <div class="borderedCol {{$task->task_type == 'emergency' ? 'has-background-danger-light' : ''}}" style="position: relative;">
        <?php
        $latest = \Tritiyo\Task\Models\TaskStatus::where('task_id', $task->id)->where('code', 'approver_approved')->orderBy('id', 'desc')->first();
        $requisition = \Tritiyo\Task\Models\TaskRequisitionBill::where('task_id', $task->id)->first();

        if ($latest) {
            if ($requisition) {
                $taskEditUrl = url('taskrequisitionbill/' . $requisition->id . '/edit/?task_id=' . $task->id . '&information=requisitionbillInformation');
            } else {
                $taskEditUrl = url('taskrequisitionbill/create?task_id=' . $task->id . '&information=requisitionbillInformation');
            }
        } else {
            $taskEditUrl = route('tasks.edit', $task->id) . '?task_id=' . $task->id . '&information=taskinformation';
        }

        ?>
        <div style="position: absolute; right: 5px; top: 5px;">
            @if(!empty($requisition->requisition_approved_by_accountant) && $requisition->requisition_approved_by_accountant == 'Yes')
                @if(auth()->user()->isResource(auth()->user()->id))
                    @if($requisition->bill_submitted_by_resource == NULL)
                        <a href="{{ route('tasks.add_bill', $task->id) }}"
                            class="button is-small is-tag is-link is-light is-rounded">
                            Submit&nbsp;Bill
                        </a>
                    @endif
                @endif

                @if($requisition->bill_submitted_by_resource == 'Yes')

                    @if($requisition->bill_approved_by_accountant == Null)
                        <a href="{{ route('tasks.add_bill', $task->id) }}" class="button is-small is-tag is-link is-light is-rounded">
                            Approve&nbsp;Bill
                        </a>
                    @elseif($requisition->bill_approved_by_accountant == 'Yes')
                        <span class="button is-small is-tag has-text-light has-background-primary-dark is-rounded">
                            Bill Approved
                        </span>
                    @endif

                @endif
            @endif
        </div>
        <article class="media">
            <div class="media-content">
                <div class="content">

                    <strong>
                        <a href="{{ route('tasks.show', $task->id) }}"
                           title="View route">
                            <strong style="color: #555;">TaskName: </strong>
                            {{ $task->task_name }}
                        </a>
                    </strong>
                    <br/>
                    <small>
                        <strong>Project: </strong>
                        @php $project = \Tritiyo\Project\Models\Project::where('id', $task->project_id)->first() @endphp
                        {{  $project->name }}
                    </small>
                    <br/>
                    <small>
                        <strong>Project Manager: </strong>
                        @php
                            $project = \Tritiyo\Project\Models\Project::where('id', $task->project_id)->first();
                        @endphp
                        <a href="{{ route('hidtory.user', $project->manager) }}" target="_blank">
                            {{ \App\Models\User::where('id', $project->manager)->first()->name }}
                        </a>
                    </small>
                    <br/>
                    <small>
                        <strong>Site Head: </strong>
                        <a href="{{ route('hidtory.user', $task->site_head) }}" target="_blank">
                            {{ \App\Models\User::where('id', $task->site_head)->first()->name }}
                        </a>
                    </small>
                    <br/>
                    <strong>Task Date: </strong>
                    {{ $task->task_for ?? NULL }}
                    <br/>
                    @php
                        $task_status = \Tritiyo\Task\Models\TaskStatus::where('task_id', $task->id)->orderBy('created_at', 'desc')->first();
                    @endphp
                    @if(isset($task_status->message))
                        @if($task_status->code == 'head_declined' || $task_status->code == 'approver_declined' || $task_status->code == 'cfo_declined' || $task_status->code == 'accountant_declined')
                            @php
                                $red = 'statusDangerMessage';
                            @endphp
                        @endif
                        <div class="{{ !empty($red) ? $red : 'statusSuccessMessage' }}">
                            {{ $task_status->message ?? NULL }}
                        </div>
                    @endif
                </div>
                <nav class="level is-mobile">
                    <div class="level-left">

                        <a href="{{ route('tasks.show', $task->id) }}"
                           class="level-item"
                           title="View task and requisition data">
                            <span class="icon is-small"><i class="fas fa-eye"></i></span>
                        </a>

                        <!-- userAccess() assign to  index.blade   -->
                        @if (userAccess('isManager') || userAccess('isApprover') || userAccess('isCFO') || userAccess('isAccountant') || userAccess('isAdmin'))

                        <a href="{{ $taskEditUrl }}" class="level-item" title="Edit task and requisition data">
                                <span class="icon is-info is-small"><i class="fas fa-edit"></i></span>
                            </a>

                            {{--  {!! delete_data('tasks.destroy',  $task->id) !!}--}}
                        @endif
                    </div>
                </nav>
            </div>
        </article>
    </div>
</div>
