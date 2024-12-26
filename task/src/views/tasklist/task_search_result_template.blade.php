@if(!empty($task))
<?php
  $latest = \Tritiyo\Task\Models\TaskStatus::where('task_id', $task->main_task_id)->where('code', 'approver_approved')->orderBy('id', 'desc')->first();
  $requisition = \Tritiyo\Task\Models\TaskRequisitionBill::where('task_id', $task->main_task_id)->first();

  if ($latest) {
    if ($requisition) {
      $taskEditUrl = url('taskrequisitionbill/' . $requisition->id . '/edit/?task_id=' . $task->main_task_id . '&information=requisitionbillInformation');
    } else {
      $taskEditUrl = url('taskrequisitionbill/create?task_id=' . $task->main_task_id . '&information=requisitionbillInformation');
    }
  } else {
    $taskEditUrl = route('tasks.edit', $task->main_task_id) . '?task_id=' . $task->main_task_id . '&information=taskinformation';
  }


?>
<tr>
    <td>
        <small>
            <a href="{{ $taskEditUrl }}"
                title="View task" target="_blank">
                <strong style="color: #555;">Task Name: </strong>
                {{ $task->task_name }}
            </a>
           <br/>
          	<strong>Task ID:</strong>
            {{ $task->main_task_id }}<br/>
            <strong>Task Type:</strong>
            {{ $task->task_type }}<br/>
            <strong>Task Date:</strong>
            {{ $task->task_for }}<br/>
        </small>
    </td>
    <td>
        
        @if(isset($task_status->message))
            @if($task_status->code == 'head_declined' || $task_status->code == 'approver_declined' || $task_status->code == 'requisition_declined_by_cfo' || $task_status->code == 'requisition_declined_by_accountant')
                @php
                    $red = 'statusDangerMessage';
                @endphp
            @endif
            <div class="{{ !empty($red) ? $red : 'statusSuccessMessage' }}" style="display: inline-block">
              	{{ $task_status->message ?? NULL }}  <br/>
                
              	 @if(auth()->user()->isAdmin(auth()->user()->id))
                   <div class="has-text-black-ter has-text-weight-medium">
                        Action performed by: {{ \App\Models\User::where('id', $task_status->action_performed_by)->first()->name }}
                   </div>
                  @endif
            </div>
        @endif<br/>

    </td>
    <td>
        <small>
            <strong>Project: </strong>
            @php $project = \Tritiyo\Project\Models\Project::where('id', $task->project_id)->first() @endphp
            {{  $project->name }} ({{ $task->project_id }})<br/>

            <strong>Project Manager: </strong>
            @php
                $project = \Tritiyo\Project\Models\Project::where('id', $task->project_id)->first();
            @endphp
            {{ \App\Models\User::where('id', $project->manager)->first()->name ?? NULL }}
            ({{  $project->manager }})<br/>
            <strong>Site Head:</strong>
            	@if($task->site_head)
                  <a href="{{ route('hidtory.user', $task->site_head) }}" target="_blank">
                      {{ \App\Models\User::where('id', $task->site_head)->first()->name ?? NULL }}
                  </a>
                @endif
            <br/>
        </small>
    </td>
    <td>
        <small>
            <strong>Requisition Total:</strong>
            <?php
          			$rm = new \Tritiyo\Task\Helpers\SiteHeadTotal('reba_amount', $task->main_task_id, false);          			
          	?>
          	{{ $rm->ttrbAmountPicker('reba_amount', $task->main_task_id, false) ?? 0 }}
            <br/>
            <strong>Bill Total:</strong>
            <?php
          			$rm = new \Tritiyo\Task\Helpers\SiteHeadTotal('beba_amount', $task->main_task_id, false);          			
          	?>
          	{{ $rm->ttrbAmountPicker('beba_amount', $task->main_task_id, false) ?? 0 }}
            <br/>
        </small>
    </td>
</tr>
@endif
