
        @php
            $taskStatuss = \Tritiyo\Task\Models\TaskStatus::where('task_id', $task->id)->where('action_performed_by', auth()->user()->id)
                                  ->orderBy('id', 'desc')->first();
        @endphp

        {{ Form::open(array('url' => route('taskstatus.store'), 'method' => 'POST', 'value' => 'PATCH', 'id' => 'add_route', 'files' => true, 'autocomplete' => 'off')) }}
        {{ Form::hidden('task_id', $task->id ?? '') }}


        @if(!empty($taskStatus) && $taskStatus->code == 'declined' && auth()->user()->id == $taskStatus->action_performed_by)
{{--            <button class="button is-danger">Task Declined</button>--}}
                <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
                <script>
                    $('form#requisition_form input').attr('disabled', true);
                    $('form#requisition_form button').addClass('is-hidden');
                    $('form#add_route button').addClass('is-hidden');
                    $('form#add_route input').attr('disabled', true);
                    $('form#add_route textarea').attr('disabled', true);
                </script>

        @elseif(!empty($taskStatuss)  && $taskStatuss->code == 'task_approver_edited')
            <?php echo Tritiyo\Task\Helpers\TaskHelper::buttonInputApproveDecline('approver_approved', 'approver_declined');?>

        @elseif(!empty($taskStatuss)  && $taskStatuss->code == 'approver_approved')
{{--        <button class="button is-success">Task Approved</button>--}}
        <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
         <script>
            $('form#requisition_form input').attr('disabled', true);
            $('form#requisition_form button').addClass('is-hidden');
            $('form#add_route button').addClass('is-hidden');
            $('form#add_route input').attr('disabled', true);
            $('form#add_route textarea').attr('disabled', true);
        </script>

        @elseif(!empty($taskStatuss)  && $taskStatuss->code == 'approver_declined')

        <button class="button is-danger is-small">Task Declined</button>

        @else
            @if( $task->override_status == 'Yes' || $task->override_status == Null)				
                <?php echo Tritiyo\Task\Helpers\TaskHelper::buttonInputApproveDecline('approver_approved', 'approver_declined');?>
            @endif
        @endif

        {{ Form::close() }}


