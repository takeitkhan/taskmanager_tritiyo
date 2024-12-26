@extends('layouts.app')
@section('title')
    Include site information for task
@endsection

<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'include Site Of Task',
            'spSubTitle' => 'Add a Site of task',
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
            'spPlaceholder' => 'Search tasks...',
            'spMessage' => $message = $message ?? NULl,
            'spStatus' => $status = $status ?? NULL
        ])
    </nav>
</section>
@section('column_left')
    <article class="panel is-primary" id="app">
        <?php
        $task_id = request()->get('task_id');
        if (empty($taskSites)) {
            $taskId = $task_id; //taskId variable asign in tasksite controller
        }
        //$taskId = !empty($task_id) ?? $task_id;
        ?>

        @include('task::layouts.tab')

        <div class="customContainer">
            <?php
            if (!empty($tasksite) && $tasksite->id) {
                $routeUrl = route('tasksites.update', $task->id);
                $method = 'PUT';
            } elseif (!empty($taskId)) {
                $routeUrl = route('tasksites.update', $taskId);
                $method = 'PUT';
            } else {
                $routeUrl = route('tasksites.store');
                $method = 'post';
            }
            ?>
            {{ Form::open(array('url' => $routeUrl, 'method' => $method, 'value' => 'PATCH', 'id' => 'add_route', 'class' => 'task_site_table',  'files' => true, 'autocomplete' => 'off')) }}

            @if($task_id)
                {{ Form::hidden('task_id', $task_id ?? '') }}
            @endif
            @if(!empty($taskId))
                {{ Form::hidden('task_id', $taskId ?? '') }}
            @endif

            <div class="columns">
                <div class="column is-6">

                    <div class="field">

                        {{ Form::label('site_id', 'Sites', array('class' => 'label')) }}
                        <div class="control">
                            <div dclass="select is-multiple">
                                @php
                                    $projectId = \Tritiyo\Task\Models\Task::where('id', $task_id)->first()->project_id;

                                  $sites = \Tritiyo\Site\Models\Site::where('project_id', $projectId)
                                                        ->where(function($query){
                                                        $query->whereNull('completion_status')
                                                        ->orWhere('completion_status',  'Running');
                                                        })
                                                        ->get();
                                @endphp
                                <select id="site_select" multiple="multiple" name="site_id[]" class="input" required>
                                    @foreach($sites as $site)
                                        <option id="site{{$site->id}}" value="{{$site->id}}" data-result="{{$site->id}}"

                                        @if(isset($taskSites))
                                            @foreach($taskSites as $data)
                                                {{$data->site_id == $site->id ? 'selected' : ''}}
                                                @endforeach
                                            @endif

                                        >{{ $site->site_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-6">
                    <div class="field">
                        {{ Form::label('resource_id', 'Site Resources', array('class' => 'label')) }}
                        <div class="control">
                            <div sclass="select is-multiple">

                                @php
                                    $task_type = \Tritiyo\Task\Models\Task::where('id', $task_id)->first()->task_type;
                                    if($task_type == 'emergency') {
                                        $date = date('Y-m-d');
                                    } else {
                                        $date = date("Y-m-d", strtotime("+1 day"));
                                    }

                                    $resources = \DB::select("SELECT * FROM (SELECT *,
                                                (SELECT site_head FROM tasks WHERE tasks.site_head = users.id AND tasks.task_for = '$date'   LIMIT 0,1) AS site_head,
                                                (SELECT user_id FROM tasks WHERE tasks.site_head = users.id AND tasks.task_for = '$date'   LIMIT 0,1) AS manager,
                                                (SELECT resource_id FROM tasks_site WHERE tasks_site.resource_id = users.id AND tasks_site.task_for = '$date'
                                                GROUP BY tasks_site.site_id LIMIT 0,1) AS resource_used,
                                                users.id AS useriddddd
                                        FROM users WHERE users.role = 2 AND users.employee_status  NOT IN ( 'Terminated', 'Left Job', 'Long Leave', 'On Hold')
                                    ) AS mm WHERE mm.site_head IS NULL AND mm.resource_used IS NULL ORDER BY id ASC");
                                @endphp
                                <select id="resource_select" multiple="multiple" name="resource_id[]" class="input" required>
                                    <option value="2" data-resultx="No">None</option>
                                    @php
                                        $all_resources = \Tritiyo\Task\Models\TaskSite::where('task_id', $task_id)->groupBy('resource_id')->get();
                                    @endphp


                                    @foreach($all_resources as $resource)
                                        @php
                                            $count_result = \Tritiyo\Task\Helpers\TaskHelper::getPendingBillCountStatus($resource->id);
                                        @endphp
                                        <option value="{{$resource->resource_id}}"
                                                data-result="{{ $count_result ?? NULL }}" selected>
                                            {{ \App\Models\User::where('id', $resource->resource_id)->first()->name ?? NULL }}
                                        </option>
                                    @endforeach


                                    @foreach($resources as $resource)
                                        @php
                                            $count_result = \Tritiyo\Task\Helpers\TaskHelper::getPendingBillCountStatus($resource->id);
                                        @endphp
                                        <option value="{{$resource->id}}" data-resultx="{{ $count_result ?? NULL }}"
                                        @if(isset($taskSites))
                                            @foreach($taskSites as $data)
                                                {{$data->resource_id == $resource->id ? 'selected' : ''}}
                                                @endforeach
                                            @endif >
                                            {{ $resource->name }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="columns">
                <div class="column">
                    <div class="field is-grouped">
                        <div class="control button_set">

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
        $task = \Tritiyo\Task\Models\Task::where('id', $taskId)->first();
    @endphp
    @include('task::task_status_sidebar')
@endsection


@section('cusjs')


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    {{--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>--}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>


    <script>
        $(document).ready(function () {
            $('#resource_select').select2({
                placeholder: "Select Resource",
                allowClear: true,
            });
            $('#site_select').select2({
                placeholder: "Select Site",
                allowClear: true
            });
        });
    </script>


    <script>

        $('select#resource_select').on('select2:select', function (e) {
            // let v = $(this).find(':selected').data("resultx");
            //let v = $(this).find(':selected:last').val()
            //console.log("select2:select", e);
            $('.button_set').empty();
            if (!e) {
                var args = "{}";
            } else {
                var args = JSON.stringify(e.params, function (key, value) {
                    if (value && value.nodeName) return "[DOM node]";
                    if (value instanceof $.Event) return "[$.Event]";
                    //console.log(value.data);
                    let resourceId = value.data.id;
                    let name = value.data.text;
                    $.ajax({
                        type: "GET",
                        url: "{{route('project.check.resource.pending.bills', '')}}/" + resourceId,
                        success: function (data) {
                            if (data == 'Yes') {
                                //console.log(value.data.text);
                                alert(value.data.text+'<br/>Your selected resource has atleast 3 pending bills. You can\'t select this resource.');
                                $('.button_set').empty();
                                $('#closeBtn').click(function(){
                                    $('#resource_select option[value=' +resourceId+ ']:selected').prop('selected', false);
                                    $("#resource_select").select2();
                                })
                            }else{
                                let submitBtn = ' <button id="task_create_btn" class="button is-success is-small">Save Changes</button>';
                                $('.button_set').empty().append(submitBtn);
                            }

                        }
                    })

                });
            }
        });

        $('select#site_select').on('change', function () {
            //let siteId = $(this).find(':selected').attr('data-result');
            $('.button_set').empty();
            let siteId = $(this).find(':selected:last').val();
            //alert(siteId);
            console.log(siteId);
            $.ajax({
                type: "GET",
                url: "{{route('project.check.limit.site', '')}}/" + siteId,
                success: function (data) {
                    if (data == 'false') {
                        console.log(data);
                        alert('You exceeded limit of task for this site.');
                        $('.button_set').empty();
                        $('#closeBtn').click(function(){
                            $('#site_select option[value = ' + siteId + ']').prop('selected', false);
                            $("#site_select").select2();
                        })
                    }
                    if (data == 'true') {
                        let submitBtn = ' <button id="task_create_btn" class="button is-success is-small">Save Changes</button>';
                        $('.button_set').empty().append(submitBtn);
                    }
                }
            })
        })
    </script>

@endsection
