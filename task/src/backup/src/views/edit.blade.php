@extends('layouts.app')
@section('title')
    Edit Task
@endsection

<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Edit Task',
            'spSubTitle' => 'Edit a single task',
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

    <article class="panel is-primary">
        {{--        <a style="display:block; float: right;" class="button is-small is-danger">--}}
        {{--            <span><i class="fas fa-plus"></i> {{$task->task_type}}</span>--}}
        {{--        </a>--}}
        @include('task::layouts.tab')


        <div class="customContainer">
            {{ Form::open(array('url' => route('tasks.update', $task->id), 'method' => 'PUT', 'value' => 'PATCH', 'id' => 'add_route', 'class' => 'task_table', 'files' => true, 'autocomplete' => 'off')) }}

            {{ Form::hidden('task_type', $task->task_type ?? '') }}
            {{ Form::hidden('task_assigned_to_head', $task->task_assigned_to_head != null ?? 'Yes') }}

            <div class="columns">

                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('project_id', 'Project', array('class' => 'label')) }}
                        <div class="control">
                            @if(auth()->user()->isManager(auth()->user()->id) || auth()->user()->isHR(auth()->user()->id))
                                <?php $projects = \Tritiyo\Project\Models\Project::where('manager', auth()->user()->id)->pluck('name', 'id')->prepend('Select Project', ''); ?>
                            @else
                                <?php $projects = \Tritiyo\Project\Models\Project::pluck('name', 'id')->prepend('Select Project', ''); ?>
                            @endif
                            <?php //$projects = \Tritiyo\Project\Models\Project::pluck('name', 'id')->prepend('Select Project', ''); ?>
                            {{ Form::select('project_id', $projects, $task->project_id ?? NULL, ['class' => 'input', 'id' => 'project_select']) }}
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('site_head', 'Site Head', array('class' => 'label')) }}
                        <div class="control">
                            <?php
                            //dd(date('Y-m-d'));
                            $today = date('Y-m-d');
                            $current_user = auth()->user()->id;
                            //$siteHead = \App\Models\User::where('role', 2)->get();
                            $siteHead = \DB::select("SELECT * FROM (SELECT *, (SELECT site_head FROM tasks WHERE tasks.site_head = users.id AND DATE_FORMAT(`tasks`.`created_at`, '%Y-%m-%d') = '$today') AS site_head,
                                    (SELECT user_id FROM tasks WHERE tasks.site_head = users.id AND DATE_FORMAT(`tasks`.`created_at`, '%Y-%m-%d') = '$today') AS manager,
                                    (SELECT resource_id FROM tasks_site WHERE tasks_site.resource_id = users.id AND DATE_FORMAT(`tasks_site`.`created_at`, '%Y-%m-%d') = '$today' GROUP BY tasks_site.site_id LIMIT 0,1) AS resource_used,
                                    users.id AS useriddddd
                                FROM users WHERE users.role = 2) AS mm WHERE mm.site_head IS NULL AND mm.resource_used IS NULL");
                            ?>
                            <select class="input" name="site_head" id="sitehead_select">
                                <option value="{{ $task->site_head }}" selected>
                                    {{ \App\Models\User::where('id', $task->site_head)->first()->name }}
                                </option>
                                @foreach($siteHead as $resource)
                                    @php
                                        $count_result = \Tritiyo\Task\Helpers\TaskHelper::getPendingBillCountStatus($resource->id);
                                    @endphp
                                    <option
                                        data-result="{{ $count_result }}"
                                        value="{{ $resource->id }}" {{ $resource->id == $task->site_head ? 'selected=""' : NULL }}>
                                        {{ $resource->name }}
                                    </option>
                                @endforeach
                            </select>


                            <?php //$siteHead = \App\Models\User::where('role', 2)->pluck('name', 'id')->prepend('Select Site Head', ''); ?>
                            {{--                            {{ Form::select('site_head', $siteHead, $task->site_head ?? NULL, ['class' => 'input', 'id' => 'sitehead_select']) }}--}}
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('task_name', 'Task Name', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('task_name', $task->task_name ?? NULL, ['class' => 'input is-small', 'placeholder' => 'Enter Task Name...']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="columns">
                <div class="column is-9">
                    <div class="field">
                        {{ Form::label('task_details', 'Task details', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::textarea('task_details', $task->task_details ?? NULL, ['class' => 'textarea', 'rows' => 5, 'placeholder' => 'Enter task details...']) }}
                        </div>
                    </div>
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
        $task = \Tritiyo\Task\Models\Task::where('id', $task->id)->first();
    @endphp

    @include('task::task_status_sidebar')
@endsection



@section('cusjs')

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
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
            var v = $(this).find(':selected').attr('data-result')
            if (v == 'Yes') {
                alert('Your selected resource has atleast 3 pending bills. You can\'t select this resource.');
            }
        })
    </script>

@endsection
