@extends('layouts.app')
@section('title')
    Include bill details of this task
@endsection

@section('headjs')
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

@endsection
<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Bill',
            'spSubTitle' => 'Prepare and submit your bill',
            'spShowTitleSet' => true
        ])

    </nav>
</section>
<?php
$task_id = $task->id;
//$task = \Tritiyo\Task\Models\Task::where('id', $task_id)->first();
$taskrequisitionbill = Tritiyo\Task\Models\TaskRequisitionBill::where('task_id', $task->id)->first();
?>



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
  
  		@if(empty($guardForTaskRequisition) || (!empty($guardForTaskRequisition) && $guardForTaskRequisition->bill_approved_by_manager == NULL) )
  				<style>
                  button.button {display: none}
                    input.button {display: none}
                    a.button {display: none}
  				</style>
  		@endif
  
  @endif
  
  
  @if(auth()->user()->isAccountant(auth()->user()->id))
  
  		@if(empty($guardForTaskRequisition) || (!empty($guardForTaskRequisition) && $guardForTaskRequisition->bill_approved_by_cfo == NULL) )
  				<style>
                  button.button {display: none}
                    input.button {display: none}
                    a.button {display: none}
  				</style>
  		@endif
  
  @endif
  
    @if(auth()->user()->isApprover(auth()->user()->id))
  		
  			<style>
                  button.button {display: none}
              	  input.button {display: none}
              	  a.button {display: none}
  			</style>
  
  	@endif
  







@if(empty($task))
    {{ Redirect::to('/dashboard') }}
@else
@section('column_left')

    <article class="panel is-primary" id="app">
        <div class="customContainer">
            <?php
            if (!empty($taskrequisitionbill) && $taskrequisitionbill) {
                $routeUrl = route('tasks.update_bill', $taskrequisitionbill->id);
                $method = 'PUT';
            } else {
                //$routeUrl = route('taskrequisitionbill.store');
                $routeUrl = '';
                $method = 'post';
            }
            ?>
            <?php

            if (!empty($taskrequisitionbill) && $taskrequisitionbill) {
                //MAnager Data when Login
                if (auth()->user()->isManager(auth()->user()->id)) {
                    if (!empty($taskrequisitionbill->bill_edited_by_manager)) {
                        $rData = $taskrequisitionbill->	bill_edited_by_manager;
                    } else {
                        $rData = $taskrequisitionbill->bill_prepared_by_resource;
                    }
                }
                //CFO Data When Login
                if (auth()->user()->isCFO(auth()->user()->id)) {
                    if (!empty($taskrequisitionbill->bill_edited_by_cfo)) {
                        $rData = $taskrequisitionbill->bill_edited_by_cfo;
                    } else {
                        $rData = $taskrequisitionbill->bill_edited_by_manager;
                    }
                }

                //Accountant DAta When Login
                if (auth()->user()->isAccountant(auth()->user()->id)) {
                    if (!empty($taskrequisitionbill->bill_edited_by_accountant)) {
                        $rData = $taskrequisitionbill->bill_edited_by_accountant;
                    } else {
                        $rData = $taskrequisitionbill->bill_edited_by_cfo;
                    }
                }
                //Accountant DAta When Login
                if (auth()->user()->isResource(auth()->user()->id)) {
                    if (!empty($taskrequisitionbill->bill_prepared_by_resource)) {
                        $rData = $taskrequisitionbill->bill_prepared_by_resource;
                    }
                }

                if (!empty($rData)) {
                    $requistion_data = json_decode($rData);
                    $da = $requistion_data->task_regular_amount->da;
                    $labour = $requistion_data->task_regular_amount->labour;
                    $other = $requistion_data->task_regular_amount->other;
                    $task_vehicle = $requistion_data->task_vehicle;
                    $task_material = $requistion_data->task_material;
                    $task_transport_breakdown = $requistion_data->task_transport_breakdown;
                    $task_purchase_breakdown = $requistion_data->task_purchase_breakdown;
                }
            }

            //->task_regular_amount ;
            ?>

            {{ Form::open(array('url' => $routeUrl, 'method' => $method, 'value' => 'PATCH', 'id' => 'requisition_form', 'files' => true, 'autocomplete' => 'off')) }}

            @if($task_id)
                {{ Form::hidden('task_id', $task_id ?? '') }}
            @endif
            @if(!empty($taskId))
                {{ Form::hidden('tassk_id', $taskId ?? '') }}
            @endif

            <div class="columns">
                <div class="column is-12">
                    <div class="columns">
                        <div class="column">

                        </div>
                    </div>
                    <?php $projects = \Tritiyo\Project\Models\Project::where('id', $task->project_id)->first(); ?>
                    <div class="columns">
                        <div class="column is-2">
                            <div class="field">
                                {{ Form::label('project_id', 'Project', array('class' => 'label')) }}
                                <div class="control">
                                    <input type="hidden" name="project_id" class="input is-small"
                                           value="{{$task->project_id}}"/>
                                    <input type="text" class="input is-small" value="{{$projects->name}}" readonly/>
                                </div>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                {{ Form::label('project_manager', 'Project Manager', array('class' => 'label')) }}
                                <div class="control">
                                    <?php $projectManager = \App\Models\User::where('id', $task->user_id)->first();?>
                                    <input type="hidden" name="project_manager_id" class="input is-small"
                                           value="{{$task->user_id}}"/>
                                    <input type="text" class="input is-small" value="{{$projectManager->name}}"
                                           readonly/>
                                </div>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                {{ Form::label('site_name', 'Site Code', array('class' => 'label')) }}
                                <div class="control">
                                    <?php
                                    $taskSite = Tritiyo\Task\Models\TaskSite::where('task_id', $task->id)->first()->site_id;
                                    $getSite = Tritiyo\Site\Models\Site::where('id', $taskSite)->first()->site_code;
                                    ?>
                                    <input type="hidden" name="site_id" class="input is-small"
                                           value="{{$taskSite}}"/>
                                    <input type="text" class="input is-small" value="{{$getSite}}" readonly/>
                                </div>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                {{ Form::label('task_for', 'Task Created For', array('class' => 'label')) }}
                                <div class="control">
                                    {{ Form::text('task_for', $task->task_for, ['required', 'class' => 'input is-small', 'readonly' => true]) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle -->
                    @include('task::taskrequisitionbill.resource_form.vehicle_breakdown')
                    <!-- End Vehicle -->

                    <!-- Material -->
                	@include('task::taskrequisitionbill.resource_form.material_breakdown')
                    <!-- End Material -->

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                {{ Form::label('da_amount', 'DA Amount', array('class' => 'label')) }}
                                <div class="control">
                                    {{ Form::number('da_amount', !empty($da) ? $da->da_amount : '', ['class' => 'input is-small',  'placeholder' => 'Enter DA amount...', 'min' => '0']) }}
                                </div>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                {{ Form::label('da_notes', 'DA Note', array('class' => 'label')) }}
                                <div class="control">
                                    {{ Form::text('da_notes', !empty($da) ? $da->da_notes : '', ['class' => 'input is-small' , 'placeholder' => 'Enter DA notes...']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                {{ Form::label('labour_amount', 'Labour Amount', array('class' => 'label')) }}
                                <div class="control">
                                    {{ Form::number('labour_amount', !empty($labour) ? $labour->labour_amount : '', ['required', 'class' => 'input is-small', 'placeholder' => 'Enter labour amount...', 'v-model' => 'labour_amount']) }}
                                </div>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                {{ Form::label('labour_notes', 'Labour Note', array('class' => 'label')) }}
                                <div class="control">
                                    {{ Form::text('labour_notes', !empty($labour) ? $labour->labour_notes : '', ['required', 'class' => 'input is-small', 'placeholder' => 'Enter DA notes...']) }}
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                {{ Form::label('other_amount', 'Other Amount', array('class' => 'label')) }}
                                <div class="control">
                                    {{ Form::number('other_amount', !empty($other) ? $other->other_amount : '', ['required', 'class' => 'input is-small', 'placeholder' => 'Enter other amount...', 'v-model' => 'other_amount']) }}
                                </div>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                {{ Form::label('other_notes', 'Other Note', array('class' => 'label')) }}
                                <div class="control">
                                    {{ Form::text('other_notes', !empty($other) ? $other->other_notes : '', ['required', 'class' => 'input is-small', 'placeholder' => 'Enter other notes...']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <!-- Transport  -->
                @include('task::taskrequisitionbill.resource_form.transport_breakdown')
                <!-- End Transport -->
                    <br/>
                    <!-- Purchase  -->
                @include('task::taskrequisitionbill.resource_form.purchase_breakdown')
                <!-- End Purchase -->
                </div>
            </div>
            <div class="columns">
                <div class="column">
                    <div class="field is-grouped">
                        <div class="control">
                            <button class="button is-success is-small">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </article>

@endsection

@section('column_right')
    @php
        $task = \Tritiyo\Task\Models\Task::where('id', $task_id)->first();
    @endphp
        @include('task::taskrequisitionbill.bill_accept_decline')
		@include('task::task_basic_data')

    @if(auth()->user()->isManager(auth()->user()->id) || auth()->user()->isResource(auth()->user()->id) || auth()->user()->isCFO(auth()->user()->id) || auth()->user()->isAccountant(auth()->user()->id))
		
        @include('task::taskaction.task_proof_images')
    @endif



    @if(auth()->user()->isresource(auth()->user()->id))

    @else(auth()->user()->isCFO(auth()->user()->id) || auth()->user()->isManager(auth()->user()->id) || auth()->user()->isAccountant(auth()->user()->id))

        @include('task::taskrequisitionbill.requistion_data')

    @endif


@endsection

@endif
@section('cusjs')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>


    <script>
        /**
         * Vehicle
         */ 

        //Select 2
        function vehicleSelectRefresh() {
            jQuery('select#vehicle_select').select2({
                placeholder: "Select Vehicle",
                allowClear: true
            });
        }
        vehicleSelectRefresh();
    
        $('select#vehicle_select').change(function(){
            let vehicleValue = $(this).val();
            //alert($(this).val());
            if(vehicleValue == 'None'){
                $('#add_vehicle_row').attr('style', 'display: none');
            } else {
                $('#add_vehicle_row').attr('style', 'display: inline-flex');
            }
        })
      
      
    </script>


<script>
    /**
     *  Material  
     */
    //Select 2
    function materialSelectRefresh() {
        $('select#material_select').select2({
            placeholder: "Select Material",
            allowClear: true
        });
    }
    materialSelectRefresh();
  
   $('select#material_select').change(function(){
  		let MaterialValue = $(this).val();
    	//alert($(this).val());
    	if(MaterialValue == 'None'){
        	$('#add_material_row').attr('style', 'display: none');
        } else {
        	$('#add_material_row').attr('style', 'display: inline-flex');
        }
  })
</script>



@endsection

