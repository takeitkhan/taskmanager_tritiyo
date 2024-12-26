@extends('layouts.app')
@section('title')
    Create Task
@endsection
@php
    if(request()->get('type') == 'emergency') {
    $title = 'you are creating an emergency task';
    } else {
    $title = 'you are creating a general task';
    }

@endphp
<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Create Task',
            'spSubTitle' => $title,
            'spShowTitleSet' => true
        ])

        @include('component.button_set', [
            'spShowButtonSet' => true,
            'spAddUrl' => null,
            'spAddUrl' => route('tasks.create'),
            'spAllData' => route('tasks.index'),
            'spSearchData' => route('tasks.search'),
            'spTitle' => 'Tasks',
        ])

        @include('component.filter_set', [
            'spShowFilterSet' => true,
            'spAddUrl' => route('tasks.create'),
            'spAllData' => route('tasks.index'),
            'spSearchData' => route('tasks.search'),
            'spPlaceholder' => 'Search tasks...',
            'spMessage' => $message = $message ?? NULl,
            'spStatus' => $status = $status ?? NULL
        ])
    </nav>
</section>
@section('column_left')

  <?php 
      		if(auth()->user()->isManager(auth()->user()->id)){
      		 $billPendingTask =  Tritiyo\Task\Models\TaskRequisitionBill::leftJoin('tasks', 'tasks.id', '=', 'tasks_requisition_bill.task_id')
                                   ->where('tasks_requisition_bill.requisition_approved_by_accountant', 'Yes')
                                   ->where('tasks_requisition_bill.bill_submitted_by_resource', 'Yes')
                                   ->where('tasks_requisition_bill.bill_approved_by_manager', Null)
          							->where('tasks.user_id', auth()->user()->id)
                                   ->orderBy('tasks.id', 'desc')
                                   ->count();
            }
      
      		elseif(auth()->user()->isCFO(auth()->user()->id)) {
            	$billPendingTask =  \Tritiyo\Task\Models\TaskRequisitionBill::leftJoin('tasks', 'tasks.id', '=', 'tasks_requisition_bill.task_id')
                        			->select('tasks.*', 'tasks_requisition_bill.task_id')
                                    ->where('tasks_requisition_bill.requisition_approved_by_accountant', 'Yes')
                                    ->where('tasks_requisition_bill.bill_submitted_by_resource', 'Yes')
                                    ->where('tasks_requisition_bill.bill_approved_by_manager', 'Yes')
                                    ->where('tasks_requisition_bill.bill_approved_by_CFO', Null)
                                    ->orderBy('tasks.id', 'desc')
                                    ->count();
            } elseif(auth()->user()->isAccountant(auth()->user()->id)){
            	$billPendingTask = Tritiyo\Task\Models\TaskRequisitionBill::leftJoin('tasks', 'tasks.id', '=', 'tasks_requisition_bill.task_id')
                                   ->where('tasks_requisition_bill.requisition_approved_by_accountant', 'Yes')
                                   ->where('tasks_requisition_bill.bill_submitted_by_resource', 'Yes')
                                   ->where('tasks_requisition_bill.bill_approved_by_manager', 'Yes')
                                   ->where('tasks_requisition_bill.bill_approved_by_cfo', 'Yes')
                                   ->where('tasks_requisition_bill.bill_approved_by_accountant', Null)
                                   ->orderBy('tasks.id', 'desc')
                                   ->count();
            } 
     		 elseif(auth()->user()->isResource(auth()->user()->id)){
            	$billPendingTask = Tritiyo\Task\Models\TaskRequisitionBill::leftJoin('tasks', 'tasks.id', '=', 'tasks_requisition_bill.task_id')
          							->where('tasks.task_assigned_to_head', 'Yes')
          							->where('tasks.site_head', auth()->user()->id)
                                   ->where('tasks_requisition_bill.requisition_approved_by_accountant', 'Yes')
                                   ->where('tasks_requisition_bill.bill_submitted_by_resource', NULL)
                                   ->orderBy('tasks.id', 'desc')
                                   ->count();
            } 
      		else{
            	$billPendingTask = '';
            }
      		//echo $billPendingTask;
      			
     		 ?>
	@if($billPendingTask > 50 && auth()->user()->isManager(auth()->user()->id))
  <span class="tag is-small is-tag is-danger  is-rounded" style="font-weight: 600;">You can not create any task, cause you have {{$billPendingTask}} pending bills to clear. please clear your pending bills. </span>
      <a class="tag is-small is-tag is-warning is-dark is-rounded" href="{{route('tasks.index')}}?bill=pending">Click to view Pending Bills</a>
	@else
      <?php
          //echo date('hia');
          $taskCreationEndTime = \App\Models\Setting::timeSettings('task_creation_end');
          //echo $taskCreationEndTime;
          if(!empty($taskCreationEndTime) && date('Hi') > $taskCreationEndTime){
              echo '<div class="notification is-danger py-1">Task Creation Time is Over. You can not create task after '.numberToTimeFormat($taskCreationEndTime).' </div>';
          }else{

     ?>
    <article class="panel is-primary">
        @if(request()->get('type') != 'emergency')
            <a style="display:block; float:right;" href="{{ route('tasks.create') }}?type=emergency"
               class="button is-small is-danger" aria-haspopup="true" aria-controls="dropdown-menu" style="    height: 24px;
    margin-bottom: 6px;">
                <span><i class="fas fa-plus"></i> Emergency Task</span>
            </a>
        @endif

        @if(!empty($task) && $task->id)
            @include('task::layouts.tab')
        @endif


        <div class="customContainer">
            <?php  if (!empty($task) && $task->id) {
                $routeUrl = route('tasks.update', $task->id);
                $method = 'PUT';
            } else {
                $routeUrl = route('tasks.store');
                $method = 'post';
            } ?>
            {{ Form::open(array('url' => $routeUrl, 'method' => $method, 'value' => 'PATCH', 'id' => 'add_route', 'files' => true, 'autocomplete' => 'off')) }}

            @if(request()->get('type') == 'emergency')
                {{ Form::hidden('task_type', $task->task_type ?? 'emergency') }}
            @else
                {{ Form::hidden('task_type', $task->task_type ?? 'general') }}
            @endif
            <div class="columns">
                <div class="column is-4 p-2">
                    <div class="field">
                        {{ Form::label('project_id', 'Project', array('class' => 'label', 'style' => 'display: inline-block')) }}


                        <div class="scontrol">
                            @if(auth()->user()->isManager(auth()->user()->id) || auth()->user()->isHR(auth()->user()->id))
                                <?php
                                /**
                                 * $projects = \Tritiyo\Project\Models\Project::leftJoin('project_ranges', function ($join) {
                                $join->on('project_ranges.project_id', '=', 'projects.id')
                                ->where('project_ranges.project_status', '=', 'Active')
                                ->orderBy('project_ranges.id', 'desc')
                                ->limit(1);
                                })->select('projects.*', 'project_ranges.*')
                                ->where('projects.manager', auth()->user()->id)
                                ->pluck('projects.name', 'projects.id')
                                ->prepend('Select Project', '');
                                 * */
                                $projects = DB::select(
                                    DB::raw("SELECT * FROM (
                                        SELECT *, (SELECT project_status FROM project_ranges WHERE project_id = projects.id ORDER BY id DESC LIMIT 0,1) AS qq FROM projects WHERE manager = '" . auth()->user()->id . "'
                                    ) AS mm WHERE mm.qq = 'Active'")
                                );
                                //dd($projects);
                                ?>
                            @else
                                <?php
                                $projects = \Tritiyo\Project\Models\Project::pluck('name', 'id')->prepend('Select Project', '');
                                ?>
                            @endif
                            <select name="project_id" class="input" id="project_select" required>
                                <option value="">Select project</option>
                                @foreach($projects as $project)
                                    <option
                                        value="{{ $project->id }}" {{ (!empty($task->project_id) && ($project->id == $task->project_id)) ? 'selected="selected"' : NULL }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                            {{--                            {{ Form::select('project_id', $projects, $task->project_id ?? NULL, ['class' => 'input', 'required' => true,  'id' => 'project_select']) }}--}}

                        </div>
                    </div>
                </div>
                <div class="column is-3 p-2">
                    <div class="field">
                        {{ Form::label('site_head', 'Site Head', array('class' => 'label')) }}
                        <div class="control">
                            <?php //$siteHead = \App\Models\User::where('role', 2)->pluck('name', 'id')->prepend('Select Site Head', ''); ?>
                            <?php
                            if (request()->get('type') == 'emergency') {
                                $date = date('Y-m-d');
                            } else {
                                $date = date("Y-m-d", strtotime("+1 day"));

                            }

                            $siteHead = \DB::select("SELECT * FROM (SELECT *,
                              			(SELECT site_head FROM tasks WHERE tasks.site_head = users.id AND tasks.task_for = '$date' LIMIT 0,1) AS site_head,
                                    	(SELECT user_id FROM tasks WHERE tasks.site_head = users.id AND tasks.task_for = '$date' LIMIT 0,1) AS manager,
                                    	(SELECT resource_id FROM tasks_site WHERE tasks_site.resource_id = users.id AND tasks_site.task_for = '$date' GROUP BY tasks_site.site_id LIMIT 0,1) AS resource_used,
                                    	users.id AS useriddddd
                                FROM users WHERE users.role = 2 AND users.employee_status  NOT IN ( 'Terminated', 'Left Job', 'Long Leave', 'On Hold')
                            ) AS mm WHERE mm.site_head IS NULL AND mm.resource_used IS NULL");

                            /**
                            $siteHead = \DB::select("SELECT * FROM (SELECT *, (SELECT site_head FROM tasks WHERE tasks.site_head = users.id AND DATE_FORMAT(`tasks`.`created_at`, '%Y-%m-%d') = '$today') AS site_head,
                            (SELECT user_id FROM tasks WHERE tasks.site_head = users.id AND DATE_FORMAT(`tasks`.`created_at`, '%Y-%m-%d') = '$today') AS manager,
                            (SELECT resource_id FROM tasks_site WHERE tasks_site.resource_id = users.id AND DATE_FORMAT(`tasks_site`.`created_at`, '%Y-%m-%d') = '$today' GROUP BY tasks_site.site_id LIMIT 0,1) AS resource_used,
                            users.id AS useriddddd
                            FROM users WHERE users.role = 2) AS mm WHERE mm.site_head IS NULL AND mm.resource_used IS NULL");
                             **/

                            //dump($siteHead);
                            ?>
 							<select class="input" name="site_head" id="sitehead_select" required>
                                <option></option>
                                @foreach($siteHead as $resource)
                                    @php
                                        $count_result = \Tritiyo\Task\Helpers\TaskHelper::getPendingBillCountStatus($resource->id);
                                    @endphp
                                    <option value="{{ $resource->id }}" data-result="{{ $count_result }}">
                                      {{ $resource->name }}
                              		</option>
                                @endforeach
                            </select>
                           
                        </div>
                    </div>
                </div>

                <div class="column is-3 p-2">
                    <div class="field">
                        {{ Form::label('task_name', 'Task Name', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('task_name', $task->task_name ?? NULL, ['class' => 'input is-small', 'required' => true, 'placeholder' => 'Enter Task Name...']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="columns">
                <div class="column is-9">
                    <div id="remaining_project_budget">

                    </div>
                </div>
            </div>
            <div class="columns">

                <div class="column is-9">
                    <div class="field">
                        {{ Form::label('task_details', 'Task Details [Please put all the activity details here]', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::textarea('task_details', $task->task_details ?? NULL, ['class' => 'textarea', 'required' => true,  'rows' => 5, 'placeholder' => 'Enter task details...']) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="columns">
                <div class="column">
                    <div class="field is-grouped">
                        <div class="control button-save">
                            <button id="task_create_btn" class="button is-success is-small">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </article>
	<?php } ?>
@endif
@endsection

@section('column_right')

@endsection

@section('cusjs')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

    <script>
        $('#sitehead_select').select2({
            placeholder: "Select Head of Site",
            allowClear: true
        });

        $('#project_select').select2({
            placeholder: "Select Project",
            allowClear: true
        });
    </script>

    <script>
        $('select#sitehead_select').on('change', function () {
            $('button#task_create_btn').attr('style', 'display:none');
            var v = $(this).find(':selected').attr('data-result')
            if (v == 'Yes') {
                alert('Your selected resource has atleast 3 pending bills. You can\'t select this resource.');
                $('button#task_create_btn').attr('style', 'display:none');
                $('.button-save').empty();
                 //$("#project_select").select2().val(null).trigger("change");
                 $("#sitehead_select").select2().val(null).trigger("change");
            }
            if (v == 'No') {
                $('button#task_create_btn').attr('style', 'display:block');
                let html = ' <button id="task_create_btn" class="button is-success is-small ">Save Changes</button>';
                $('.button-save').html(html);
            }
        })
    </script>


    <script>
        //Remain Budget Balanace of a project
        $('#project_select').change(function(){
            //alert($(this).val());

            $(".tap2").show();
            $(".tap1").show();
            let projectId = $(this).val();
            $.ajax({
                type: "GET",
                url: "{{route('project.remain.balance', '')}}/"+projectId,
                success: function(data){
                   // console.log(data);
                    let html = '<div class="statusSuccessMessage is-warning mb-2" style="height: 26px; display: block; padding: 11px;">'
                    html += '<div class="columns">';
                    html += '<div class="column is-4 has-text-weight-bold">Total Balance BDT '+data.total;
                    html += '</div>';
                    html += '<div class="column is-4has-text-centered has-text-weight-bold">Usage Balance BDT '+data.usage;
                    html += '</div>';
                    html += '<div class="column is-4 has-text-right has-text-weight-bold">Remaining Balance BDT '+data.remain;
                    html += '</div>';
                    html += '</div></div>';
                    $(".tap2").hide();
                    $(".tap1").hide();
                    $("#remaining_project_budget").empty().append(html);

                      let projectLockPercent = "{{ \Tritiyo\Project\Helpers\ProjectHelper::projectLockPercentage() }}";

                    if(data.usage * 100 / data.total >= projectLockPercent){

                        alert('Already you have used '+projectLockPercent+'% of budget. So you can not create any task under this project.') ;
                        $('a#closeBtn').click(function(e){
                        	e.preventDefault();
                            $(".tap2").attr('style', 'display:none');
                   			 $(".tap1").attr('style', 'display:none');
                        })
                        $("#task_create_btn").attr('style', 'display: none');
                             $("#project_select").select2().val(null).trigger("change");
                 			//$("#sitehead_select").select2().val(null).trigger("change");
                    } else {
                        $("#task_create_btn").attr('style', 'display: block');
                        //$(".control").attr('style', 'display: block');
                    }

                    if(data.total == 0){
                        alert('Budget has not assigned in this project yet');
                        $("#task_create_btn").attr('style', 'display: none');
                        $("#project_select").select2().val(null).trigger("change");
                        //$("#sitehead_select").select2().val(null).trigger("change");
                    } else {
                        //$("#task_create_btn").attr('style', 'display: block');
                    }
                }
            })
        })

    </script>




@endsection


<?php
/**
$siteHead = \DB::select("
SELECT * FROM (
SELECT *,
(
SELECT created_at FROM tasks WHERE tasks.site_head = users.id AND DATE_FORMAT(`tasks`.`created_at`, '%Y-%m-%d') = '2021-03-14'
) AS task_created,
(
SELECT site_head FROM tasks WHERE tasks.site_head = users.id AND DATE_FORMAT(`tasks`.`created_at`, '%Y-%m-%d') = '2021-03-14'
) AS site_head

FROM users WHERE users.role = 2
) AS QQ
WHERE QQ.site_head IS NULL
");
 **/
?>
