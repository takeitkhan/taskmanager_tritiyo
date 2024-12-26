{{--    <article class="panel is-primary">--}}
{{--        <div class="customContainer">--}}
@if($task->manager_override_task_chunck != null)
    <script>
        $('form.task_table button').addClass('is-hidden');
        $('form.task_table input').attr('disabled', true);
        $('form.task_table textarea').attr('disabled', true);
    </script>
@endif

@if(auth()->user()->isManager(auth()->user()->id))
    {{--    @include('task::taskaction.ready_for_assign_to_head')--}}
@endif
@if(auth()->user()->isApprover(auth()->user()->id))
    {{--    @include('task::taskaction.task_approver_accept_decline')--}}
@endif

@include('task::taskaction.accept_decline')

@include('task::task_basic_data')



@php
    $taskStatus = \Tritiyo\Task\Models\TaskStatus::where('task_id', $task->id)->orderBy('id', 'desc')->first();
  	//dump($taskStatus);
    $proofs = \Tritiyo\Task\Models\TaskProof::where('task_id', $task->id)->first();
@endphp

@if(auth()->user()->isManager(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id) || auth()->user()->isCFO(auth()->user()->id) || auth()->user()->isAccountant(auth()->user()->id))
    @include('task::taskaction.task_proof_images')
@else
    @if(empty($taskStatus) || $taskStatus->code == 'declined' && auth()->user()->isResource(auth()->user()->id))
        @include('task::taskaction.task_accept_decline')
    @elseif(empty($taskStatus) || $taskStatus->code == 'head_accepted')
        @include('task::taskaction.task_proof_form')
    @elseif(empty($taskStatus) || $taskStatus->code == 'proof_given')
        @include('task::taskaction.task_proof_images')
    @endif
@endif

@include('task::taskrequisitionbill.requistion_data')


