@if(!empty($task))
<tr>
    <td>
        <small>
            <a href="{{ route('tasks.show', $task->main_task_id) }}"
                title="View task" target="_blank">
                <strong style="color: #555;">Task Name: </strong>
                {{ $task->task_name }}
            </a>
            <br/>
            <strong>Task Type:</strong>
            {{ $task->task_type }}<br/>
            <strong>Task Date:</strong>
            {{ $task->task_for }}<br/>
        </small>
    </td>
    <td>
        @php
            $task_status = \Tritiyo\Task\Models\TaskStatus::where('task_id', $task->main_task_id)->orderBy('created_at', 'desc')->first();
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
            {{ \App\Models\User::where('id', $project->manager)->first()->name }}
            ({{  $project->manager }})<br/>
            <strong>Site Head:</strong>
                <a href="{{ route('hidtory.user', $task->site_head) }}" target="_blank">
                    {{ \App\Models\User::where('id', $task->site_head)->first()->name }}
                </a>
            <br/>
        </small>
    </td>
    <td>
        <small>
            <strong>Requisition Total:</strong>
            @php $rm = new \Tritiyo\Task\Helpers\SiteHeadTotal('requisition_edited_by_accountant', $task->main_task_id, true) @endphp
            {{ $rm->getTotal() }}
            <br/>
            <strong>Bill Total:</strong>
            @php $rm = new \Tritiyo\Task\Helpers\SiteHeadTotal('bill_edited_by_accountant', $task->main_task_id, true) @endphp
            {{ $rm->getTotal() }}
            <br/>
        </small>
    </td>
</tr>
@endif
