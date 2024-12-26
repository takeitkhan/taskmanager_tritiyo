@extends('layouts.app')

@section('title')
    Tasks Advanced Search
@endsection

@php
    if (auth()->user()->isManager(auth()->user()->id)) {
        $addbtn = route('tasks.create');
        $alldatas = route('tasks.index');
    } else {
        $addbtn = '#';
        $alldatas = '#';
    }
@endphp
<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Tasks Advanced Search',
            'spSubTitle' => 'view a Task',
            'spShowTitleSet' => true
        ])

        @include('component.button_set', [
            'spShowButtonSet' => false,
            'spAddUrl' => null,
            'spAddUrl' => $addbtn,
            'spAllData' => route('tasks.index'),
            'spSearchData' => route('tasks.search'),
            'spTitle' => 'Tasks',
        ])

        @include('component.filter_set', [
            'spShowFilterSet' => false,
            'spPlaceholder' => 'Search tasks...',
            'spMessage' => $message = $message ?? NULl,
            'spStatus' => $status = $status ?? NULL
        ])
    </nav>
</section>
@section('column_left')
<div class="card tile is-child xquick_view pt-0">

    <header class="card-header">
        <p class="card-header-title">
            <span class="icon"><i class="fas fa-tasks default"></i></span>
                Task Advanced Search
        </p>
    </header>
    <div class="card-content">
        <div class="card-data">
        {{ Form::open(array('url' => route('tasks.search'), 'method' => 'GET', 'value' => 'PATCH', 'id' => 'tasks_advanced_search', 'autocomplete' => 'off')) }}
            <div class="columns">
                <div class="column is-5">
                    <input type="text" name="key" class="input is-small" id="textboxID" placeholder="Search by any keyword" value="{{ request()->key ? request()->key : null }}" />
                </div>
                <div class="column">
                    @php
                        $projects = \Tritiyo\Project\Models\Project::get();
                    @endphp
                    <select id="task_type" class="input is-small" name="task_type">
                        <option value="">Select task type</option>
                        <option value="general" {{ request()->get('task_type') == 'general' ? 'selected' : ''}}>General</option>
                        <option value="emergency" {{ request()->get('task_type') == 'emergency' ? 'selected' : ''}}>Emergency</option>
                    </select>
                </div>
                <div class="column">
                    @php $projects = \Tritiyo\Project\Models\Project::get(); @endphp
                    <select id="project_id" class="input is-small" name="project_id">
                        <option value=""></option>
                        @foreach($projects as $data)
                            <option value={{$data->id}} {{ $data->id == request()->get('project_id') ? 'selected' : ''}}>
                                {{$data->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="column">
                    <select id="site_head_id" class="input is-small" name="site_head_id">
                    @php $resources = \App\Models\User::where('role', '2')->get(); @endphp
                        <option value="">Select site head</option>
                        @foreach($resources as $data)
                        <option value={{$data->id}} {{ $data->id == request()->get('site_head_id') ? 'selected' : ''}}>{{$data->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="column">
                    <select id="" class="input is-small" name="bill_status">

                        <option value="">Filter By Bill Status</option>

                        <option value='pending_bill'>Pending Bill</option>

                    </select>
                </div>
                <div class="column">
                    <input class="input is-small" type="text" name="daterange" value="{{ request()->get('daterange') }}" />
                </div>
                <div class="column">
                    <input name="search" type="submit" class="button is-small is-primary has-background-primary-dark" value="Search"/>
                </div>
            </div>
        {{ Form::close() }}
{{--        @dump($search_result)--}}
        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
            <tr>
                <th>Task Basic</th>
                <th>Task & Bill Status</th>
                <th>Project and Site Info</th>
                <th>Transaction</th>
            </tr>
            @if(!empty($search_result))
                @foreach($search_result as $task)
                    @if(!empty($task->main_task_id))
                        @if(auth()->user()->isManager(auth()->user()->id))

                            @if(auth()->user()->id == $task->user_id)
                                @include('task::tasklist.task_search_result_template')
                            @endif
                        @else
                            @include('task::tasklist.task_search_result_template')
                        @endif
                    @endif
                @endforeach
            @else
                <tr>
                    <td colspan="3">
                        No results found
                    </td>
                </tr>
            @endif
        </table>

        <?php
            //dump($search_result);
        ?>

        </div>
    </div>
</div>
<script type="text/javascript">
    document.getElementById('textboxID').select();
</script>
@endsection

@section('cusjs')
    <script type="text/javascript"
    src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript"
    src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript"
    src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


<script>
$(function() {
  $('input[name="daterange"]').daterangepicker({
    opens: 'left',
    locale: {
        format: 'YYYY-MM-DD'
    }
  }, function(start, end, label) {
    console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
  });
});
</script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

<script>
    $('select#project_id').select2({
        placeholder: "Select Project",
        allowClear: true,
    });
    $('select#site_head_id').select2({
        placeholder: "Select Site Head",
        allowClear: true,
    });
    $('select#task_type').select2({
        placeholder: "Select task type",
        allowClear: true,
    });
</script>

    <style type="text/css">
        .table.is-fullwidth {
            width: 100%;
            font-size: 15px;
        }

        .task_status {
            padding: .30rem .50rem;
            margin-bottom: .30rem;
            border: 1px solid transparent;
            border-radius: .25rem;
            font-size: 11px;
        }

        .task_status.success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .task_status.danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
@endsection
