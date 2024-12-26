<?php
if (request()->get('task_id')) {
    $task_id = request()->get('task_id');
} elseif (!empty($task)) {
    $task_id = $task->id;
}

$requisition = \Tritiyo\Task\Models\TaskRequisitionBill::select('id')->where('task_id', $task_id)->first();

if ($requisition) {
    $requisitionUrl = url('taskrequisitionbill/' . $requisition->id . '/edit/?task_id=' . $task_id . '&information=requisitionbillInformation');
} else {
    $requisitionUrl = url('taskrequisitionbill/create?task_id=' . $task_id . '&information=requisitionbillInformation');
}

$taskInformation = 'taskinformation';
$siteInformation = 'siteinformation';
$vehicleInformation = 'vehicleinformation';
$materialInformation = 'materialInformation';
$anonymousProofInformation = 'anonymousproof';
$requisitionbillInformation = 'requisitionbillInformation';

?>

<div class="panel-tabs">
    <a class="{{request()->get('information') == $taskInformation ? 'is-active' : ''}}"
       href="{{route('tasks.edit', $task_id) }}?task_id={{$task_id}}&information={{$taskInformation}}">Task
        Information</a>
    <a class="{{request()->get('information') == $siteInformation ? 'is-active' : ''}}"
       href="{{route('tasks.site.edit', $task_id) }}?task_id={{$task_id}}&information={{$siteInformation}}">Site
        Information</a>
    <a class="{{request()->get('information') == $anonymousProofInformation ? 'is-active' : ''}}"
       href="{{route('tasks.anonymousproof.edit', $task_id)}}?task_id={{$task_id}}&information={{$anonymousProofInformation}}"
       class="">Anonymous Proof</a>
    <a class="{{request()->get('information') == $vehicleInformation ? 'is-active' : ''}}"
       href="{{route('taskvehicle.create')}}?task_id={{$task_id}}&information={{$vehicleInformation}}" class="">Vehicle
        Information</a>
    <a class="{{request()->get('information') == $materialInformation ? 'is-active' : ''}}"
       href="{{route('taskmaterial.create')}}?task_id={{$task_id}}&information={{$materialInformation}}" class="">Material
        Information</a>

    <?php
    $taskStsApproverApproved = \Tritiyo\Task\Models\TaskStatus::where('task_id', $task_id)->get();
    $collection = Tritiyo\Task\Helpers\TaskHelper::arrayExist($taskStsApproverApproved, 'code', 'approver_approved');
    ?>
    @if($collection == true)
        @if( auth()->user()->isCFO(auth()->user()->id) || auth()->user()->isManager(auth()->user()->id) || auth()->user()->isAccountant(auth()->user()->id))
            <a class="{{request()->get('information') == $requisitionbillInformation ? 'is-active' : ''}}"
               href="{{$requisitionUrl}}?task_id={{$task_id}}&information={{$requisitionbillInformation}}" class="">Requisition
                Information</a>

        @endif
    @endif
  
  
  
  
	<!-- Guard For task -->
  <?php 
  	$guardForTask = \Tritiyo\Task\Models\Task::where('id', $task_id)->first();
  	$guardForTaskRequisition = \Tritiyo\Task\Models\TaskRequisitionBill::where('task_id', $task_id)->first();
   	$guardForTaskStatus = \Tritiyo\Task\Models\TaskStatus::where('task_id', $task_id)->orderBy('id', 'desc')->first();
  ?>
   @if(auth()->user()->isResource(auth()->user()->id))
  
  	@if($guardForTask->site_head == auth()->user()->id)
  	@else
  		<?php dd('Invalid Request');?>
  	@endif
	
  	@if(Route::currentRouteName() == 'tasks.edit')
  		<?php dd('Invalid Request'); ?>
  	@endif
  	
  
  @endif
  
  
  @if(auth()->user()->isManager(auth()->user()->id))
  
  	@if($guardForTask->user_id == auth()->user()->id)
  	@else
  		<?php dd('This is not your Task');?>
  	@endif
  
  @endif
  
  
    @if(auth()->user()->isCFO(auth()->user()->id))
  
  		@if(empty($guardForTaskRequisition) || (!empty($guardForTaskRequisition) && $guardForTaskRequisition->requisition_submitted_by_manager == NULL) )
  				<style>
                  button.button {display: none}
                    input.button {display: none}
                    a.button {display: none}
  				</style>
  		@endif
  
  @endif
  
  
  @if(auth()->user()->isAccountant(auth()->user()->id))
  
  		@if(empty($guardForTaskRequisition) || (!empty($guardForTaskRequisition) && $guardForTaskRequisition->requisition_approved_by_cfo == NULL) )
  				<style>
                  button.button {display: none}
                    input.button {display: none}
                    a.button {display: none}
  				</style>
  		@endif
  
  @endif
  
    @if(auth()->user()->isApprover(auth()->user()->id))
  		
  		@if($guardForTaskStatus->code == 'proof_given' || $guardForTaskStatus->code == 'task_approver_edited' || $guardForTaskStatus->code == 'task_override_data')
  			
  		@else
  			<style>
                  button.button {display: none}
              	  input.button {display: none}
              	  a.button {display: none}
  			</style>

  		@endif
  		
  	@endif
 
  
</div>

