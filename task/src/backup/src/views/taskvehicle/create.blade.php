@extends('layouts.app')
@section('title')
    Include vehicle information of task
@endsection

<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Vehicle',
            'spSubTitle' => 'Add a vehicle of task',
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
        <a style="float: right; display: block">
            <span style="cursor: pointer;" class="tag is-success"
                  id="addrow">Add Breakdown &nbsp; <strong>+</strong></span>
        </a>

        <?php $task_id = request()->get('task_id');?>

            @include('task::layouts.tab')



        <div class="customContainer">
            <?php  if (!empty($task_id) && $task_id) {
                $routeUrl = route('taskvehicle.update', $task_id);
                $method = 'PUT';
            } else {
                $routeUrl = route('taskvehicle.store');
                $method = 'post';
            } ?>

            {{ Form::open(array('url' => $routeUrl, 'method' => $method, 'value' => 'PATCH', 'id' => 'add_route', 'class' => 'task_vehicle_table', 'files' => true, 'autocomplete' => 'off')) }}

            @if($task_id)
                {{ Form::hidden('task_id', $task_id ?? '') }}
            @endif
            @if(!empty($taskId))
                {{ Form::hidden('tassk_id', $taskId ?? '') }}
            @endif

            @php
                $vehicles = \Tritiyo\Vehicle\Models\Vehicle::get();
                $getTaskVehicle = \Tritiyo\Task\Models\TaskVehicle::where('task_id', $task_id)->get();
            @endphp
            <div id="myTable">
            @if(count( $getTaskVehicle) > 0)
                @foreach( $getTaskVehicle as $veh)

                        <div class="columns s{{$veh->id}}">
                            <div class="column is-2">
                                <div class="field">
                                    {{ Form::label('vehicle_id', 'Vehicle', array('class' => 'label')) }}
                                    <div class="control">
                                        <select name="vehicle_id[]" id="vehicle_select" class="input is-small" required>
                                            @foreach($vehicles as $vehicle)
                                                <option value="{{$vehicle->id}}" {{ $veh->vehicle_id == $vehicle->id ? 'selected' : '' }} >{{$vehicle->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="column is-2">
                                {{ Form::label('vehicle_rent', 'Vehicle Rent', array('class' => 'label')) }}
                                {{ Form::number('vehicle_rent[]', $veh->vehicle_rent, array('class' => 'input is-small')) }}
                            </div>
                            <div class="column is-6">
                                {{ Form::label('vehicle_note', 'Note', array('class' => 'label')) }}
                                {{ Form::text('vehicle_note[]', $veh->vehicle_note, array('class' => 'input is-small')) }}
                            </div>
                            <div class="column is-1">
                                <label></label> <br/>
                                <a><span class="tag is-danger is-small ibtnDel">Delete</span></a>
                            </div>

                        </div>
{{--                    </div>--}}
                @endforeach
            @else
{{--                <div id="myTable">--}}
                    <div class="columns">
                        <div class="column is-2">
                            <label for="vehicle_id" class="label">Vehicle</label>
                            <select name="vehicle_id[]" id="vehicle_select" class="input is-small" required>
                                    <option></option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{$vehicle->id}}">{{$vehicle->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="column is-2">
                            <label for="vehicle_rent" class="label">Vehicle Rent</label>
                            <input name="vehicle_rent[]" type="number" value="" class="input is-small" required>
                        </div>
                        <div class="column is-6">
                            <label for="vehicle_note" class="label">Note</label>
                            <input name="vehicle_note[]" type="text" value="" class="input is-small" required>
                        </div>
                        <div class="column is-1">
                            <label></label> <br/>
                            <a><span class="tag is-danger is-small ibtnDel">Delete</span></a>
                        </div>
                    </div>

            @endif
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
    @include('task::task_status_sidebar')
@endsection
@section('cusjs')
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script>
        //Add Row Function
        $(document).ready(function () {
            var counter = 1;

            $("#addrow").on("click", function () {
                var cols = '<div class="columns r' + counter + '">';
                cols += '<div class="column is-2">';
                cols += '<label for="vehicle_id" class="label">Vehicle</label>';
                cols += '<select name="vehicle_id[]" id="vehicle_select" class="input" required>';
                cols += '<option></option>';
                cols += '<?php foreach($vehicles as $vehicle){?>';
                cols += '<option value="<?php echo $vehicle->id;?>"><?php echo $vehicle->name;?></option>';
                cols += '<?php } ?>';
                cols += '<select>';
                cols += '</div>';
                cols += '<div class="column is-2">';
                cols += '<label for="vehicle_rent" class="label">Vehicle Rent</label>';
                cols += '<input name="vehicle_rent[]" type="number" value="" class="input is-small" required>';
                cols += '</div>';
                cols += '<div class="column is-6">';
                cols += '<label for="vehicle_note" class="label">Note</label>';
                cols += '<input name="vehicle_note[]" type="text" value="" class="input is-small" required>';
                cols +=  '</div>';
                cols += '<div class="column is-1">';
                cols += '<br/><a><span class="tag is-danger is-small ibtnDel">Delete</span></a>';
                cols += '</div>';
                cols += '</div>';
                $("div#myTable").append(cols);
                vehicleSelectRefresh();
                counter++;
            });


            $("div#myTable").on("click", ".ibtnDel", function (event) {
                $(this).closest("div.columns").remove();
                counter -= 1
            });
        });

    </script>


<!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>


    <script>
        //Select 2
        function vehicleSelectRefresh() {
            $('select#vehicle_select').select2({
                placeholder: "Select vehicle",
                allowClear: true,
            });
        }
        vehicleSelectRefresh();
    </script>





@endsection

