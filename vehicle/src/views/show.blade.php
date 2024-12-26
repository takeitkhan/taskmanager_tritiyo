@extends('layouts.app')

@section('title')
    Single Vehicle
@endsection
@if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
    @php
        $addUrl = route('vehicles.create');
    @endphp
@else
    @php
        $addUrl = '#';
    @endphp
@endif
<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Single Vehicle',
            'spSubTitle' => 'view a Vehicle',
            'spShowTitleSet' => true
        ])

        @include('component.button_set', [
            'spShowButtonSet' => true,
            'spAddUrl' => null,
            'spAddUrl' => $addUrl,
            'spAllData' => route('vehicles.index'),
            'spSearchData' => route('vehicles.search'),
            'spTitle' => 'Vehicles',
        ])

        @include('component.filter_set', [
            'spShowFilterSet' => true,
            'spAddUrl' => route('vehicles.create'),
            'spAllData' => route('vehicles.index'),
            'spSearchData' => route('vehicles.search'),
            'spPlaceholder' => 'Search vehicles...',
            'spMessage' => $message = $message ?? NULl,
            'spStatus' => $status = $status ?? NULL
        ])
    </nav>
</section>
@section('column_left')
    {{--    <article class="panel is-primary">--}}
    {{--        <div class="customContainer">--}}
    <div class="card tile is-child">
        <header class="card-header">
            <p class="card-header-title">
                <span class="icon"><i class="mdi mdi-account default"></i></span>
                Main Vehicle Data
            </p>
        </header>
        <div class="card-content">
            <div class="card-data">
                <div class="columns">
                    <div class="column is-2">Name</div>
                    <div class="column is-1">:</div>
                    <div class="column">{{ $vehicle->name }}</div>
                </div>
                <div class="columns">
                    <div class="column is-2">Vehicle size</div>
                    <div class="column is-1">:</div>
                    <div class="column">{{ $vehicle->size }}</div>
                </div>
                <div class="columns">
                    <div class="column is-2">Probably Cost</div>
                    <div class="column is-1">:</div>
                    <div class="column">{{ $vehicle->probably_cost }}</div>
                </div>
            </div>
        </div>
    </div>


    @php
        $daterange = request()->get('daterange');
        if(!empty(request()->get('daterange'))) {
            $dates = explode(' - ', $daterange);
            $start = $dates[0];
            $end = $dates[1];
            $vehicles = \Tritiyo\Task\Models\TaskVehicle::leftJoin('tasks', 'tasks.id', 'tasks_vehicle.task_id')
                    ->where('tasks_vehicle.vehicle_id', $vehicle->id)
                    ->whereBetween('tasks.task_for', [$start, $end])
                    ->paginate('50');
        } else {
            $start = date('Y-m-d', strtotime(date('Y-m-d'). ' - 30 days'));
            $end = date('Y-m-d');
            $vehicles = \Tritiyo\Task\Models\TaskVehicle::leftJoin('tasks', 'tasks.id', 'tasks_vehicle.task_id')
                    ->where('tasks_vehicle.vehicle_id', $vehicle->id)
                    ->whereBetween('tasks.task_for', [$start, $end])
                    ->paginate('50');
        }

        //dd($vehicles);
    @endphp

    <div class="card tile is-child" style="margin-top: 15px !important;">
        <header class="card-header">
            <p class="card-header-title">
                <span class="icon"><i class="fas fa-tasks default"></i></span>
                Vehicle Used

            {{ Form::open(array('url' => route('vehicles.show', $vehicle->id), 'method' => 'GET', 'value' => 'PATCH', 'id' => 'tasks_advanced_search', 'class' => 'dateFilter', 'autocomplete' => 'off')) }}


            <div class="columns">
                <div class="column">
                    <a href="{{ route('download_excel_vehicle') }}?id={{ $vehicle->id }}&daterange={{ request()->get('daterange') ??  date('Y-m-d', strtotime(date('Y-m-d'). ' - 30 days')). ' - ' . date('Y-m-d') }}"
                       class="button is-primary is-small">
                        Download as excel
                    </a>
                </div>
                <div class="column">
                    <input class="input is-small" type="text" name="daterange" value="{{ request()->get('daterange') ?? null }}"/>
                </div>
                <div class="column">
                    <input name="search" type="submit" class="button is-small is-primary has-background-primary-dark"
                           value="Search"/>
                </div>
            </div>
            {{ Form::close() }}
            </p>
        </header>
        <div class="card-content">
            <div class="card-data">
                @if($vehicles->count() > 0)
                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                        <tr>
                            <th title="Task date">Task Name</th>
                            <th title="Task date">Task For</th>
                            <th title="Task Type">Task Type</th>
                            <th title="Site Code">Site Code</th>
                            <th title="Project Name">Project Name</th>
                            <th title="Project Manager">Project Manager</th>
                            <th title="Site head">Site Head</th>
                            <th title="Vehicle Rent">Vehicle Rent</th>
                            <th title="Vehicle Note">Vehicle Note</th>
                            <th title="Resource Used">Resource Used</th>
                        </tr>
                        @php
                            $in_total = [];
                        @endphp

                        @foreach($vehicles as $vehicle)
                            <tr>
                                @php
                                  $task =  \Tritiyo\Task\Models\Task::where('id', $vehicle->task_id)->first();
                                @endphp
                                <td title="Task ID">
                                    <a href="{{ route('tasks.show', $vehicle->task_id) }}" target="_blank">
                                        {{ $task->task_name}}
                                    </a>
                                </td>
                                <td title="Task date">
                                    {{ $task->task_for }}
                                </td>
                                <td>
                                    {{ $task->task_type}}
                                </td>
                                <td title="Site Code">
                                    @php
                                        $siteUsed = \Tritiyo\Task\Models\TaskSite::leftjoin('sites', 'sites.id', 'tasks_site.site_id')
                                                  ->select('sites.site_code')
                                                  ->where('tasks_site.task_id', $vehicle->task_id)
                                                  ->groupBy('tasks_site.site_id')
                                                  ->get()->toArray();
                                            echo implode('<br>',array_column($siteUsed, 'site_code'));
                                    @endphp
                                </td>
                                <td title="Project Name">
                                    <a href="{{ route('projects.show', $task->project_id) }}" target="_blank">
                                        {{ \Tritiyo\Project\Models\Project::where('id', $task->project_id)->first()->name }}
                                    </a>
                                </td>
                                <td title="Project Manager">
                                    @if(!empty($task->user_id))
                                    <a href="{{ route('hidtory.user', $task->user_id) }}" target="_blank">
                                        {{\App\Models\User::where('id', $task->user_id)->first()->name}}
                                    </a>
                                    @endif
                                </td>
                                <td title="Task head">
                                    @if(!empty($task->site_head))
                                    <a href="{{ route('hidtory.user', $task->site_head) }}" target="_blank">
                                        {{ \App\Models\User::where('id', $task->site_head)->first()->name }}
                                    </a>
                                    @endif
                                </td>
                                <td title="Vehicle Rent">

                                        {{ $in_total[] = $vehicle->vehicle_rent }}
                                </td>
                                <td title="Vehicle Note">
                                    {{ $vehicle->vehicle_note }}
                                </td>
                                <td title="Resource Used">
                                    @php
                                        $rseourceUse = \Tritiyo\Task\Models\TaskSite::leftJoin('users', 'users.id', 'tasks_site.resource_id')
                                                ->select('users.name')
                                                ->where('task_id', $vehicle->task_id)
                                                ->groupBy('resource_id')
                                                ->get()
                                                ->toArray();
                                        echo implode(',', array_column($rseourceUse, 'name'));
                                        //dump($data);
                                    @endphp
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    <div class="pagination_wrap pagination is-centered">
                        {{ $vehicles->links('pagination::bootstrap-4') }}
                    </div>
                @else
                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                        <tr>
                            <td title="Task date" width="20%">No vehicle used based on your selected date range.
                            </th>
                        </tr>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('cusjs')
    <script type="text/javascript"
            src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript"
            src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript"
            src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>


    <script>
        $(function () {
            $('input[name="daterange"]').daterangepicker({
                opens: 'left',
                locale: {
                    format: 'YYYY-MM-DD'
                }
            }, function (start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
        });
    </script>
    <style type="text/css">
        .table.is-fullwidth {
            width: 100%;
            font-size: 15px;
            text-align: center;
        }
    </style>
@endsection
