@extends('layouts.app')
@section('title')
    Include requisition information of this task
@endsection

<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Vehicle',
            'spSubTitle' => 'Add Requisition Bill of task',
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
<?php
$task_id = request()->get('task_id');
$task = \Tritiyo\Task\Models\Task::where('id', $task_id)->first();
?>
@if(empty($task))
    {{ Redirect::to('/dashboard') }}
@else
@section('column_left')


    <article class="panel is-primary" id="app">
        @include('task::layouts.tab')
        <div class="customContainer">
            <?php  if (!empty($taskrequisitionbill) && $taskrequisitionbill) {
                $routeUrl = route('taskrequisitionbill.update', $taskrequisitionbill->id);
                $method = 'PUT';
            } else {
                $routeUrl = route('taskrequisitionbill.store');
                $method = 'post';
            } ?>
            <?php

            if (!empty($taskrequisitionbill) && $taskrequisitionbill) {
                //MAnager Data when Login
                if (auth()->user()->isManager(auth()->user()->id)) {
                    $rData = $taskrequisitionbill->requisition_prepared_by_manager;
                }
                //CFO Data When Login
                if (auth()->user()->isCFO(auth()->user()->id)) {
                    if (!empty($taskrequisitionbill->requisition_edited_by_cfo)) {
                        $rData = $taskrequisitionbill->requisition_edited_by_cfo;
                    } else {
                        $rData = $taskrequisitionbill->requisition_prepared_by_manager;
                    }
                }

                //Accountant DAta When Login
                if (auth()->user()->isAccountant(auth()->user()->id)) {
                    if (!empty($taskrequisitionbill->requisition_edited_by_accountant)) {
                        $rData = $taskrequisitionbill->requisition_edited_by_accountant;
                    } else {
                        $rData = $taskrequisitionbill->requisition_edited_by_cfo;
                    }
                }

                if (!empty($rData)) {
                    $requistion_data = json_decode($rData);
                    $da = $requistion_data->task_regular_amount->da;
                    $labour = $requistion_data->task_regular_amount->labour;
                    $other = $requistion_data->task_regular_amount->other;
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
                                    <?php $projectManager = \App\Models\User::where('id', $task->site_head)->first();?>
                                    <input type="hidden" name="project_manager_id" class="input is-small"
                                           value="{{$task->site_head}}"/>
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
                    <?php $getTaskVehicle = \Tritiyo\Task\Models\TaskVehicle::where('task_id', $task_id)->get(); ?>

                    <fieldset class="pb-5">
                        <label>Vehicle Information
                            <a href="{{route('taskvehicle.create')}}?task_id={{$task_id}}&information=vehicleinformation"
                               class="is-link is-size-7 is-small"> Edit </a>
                        </label>
                        @if(is_array($getTaskVehicle->toArray()))
                            @foreach( $getTaskVehicle as $veh)
                                <div class="columns">
                                    <div class="column is-3">
                                        <div class="field">
                                            {{ Form::label('vehicle_id', 'Vehicle', array('class' => 'label')) }}
                                            <div class="control">
                                                <?php $vehicleName = \Tritiyo\Vehicle\Models\Vehicle::where('id', $veh->vehicle_id)->first()->name;?>
                                                <input type="hidden" name="vehicle_id" class="input is-small"
                                                       value="{{$veh->vehicle_id}}">
                                                <input type="text" name="" class="input is-small"
                                                       value="{{$vehicleName}}"
                                                       readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="column is-3">
                                        {{ Form::label('vehicle_rent', 'Vehicle Rent', array('class' => 'label')) }}
                                        {{ Form::text('vehicle_rent[]', $veh->vehicle_rent, array('class' => 'input is-small', 'readonly' => true)) }}
                                    </div>
                                    <div class="column is-6">
                                        {{ Form::label('vehicle_note', 'Note', array('class' => 'label')) }}
                                        {{ Form::text('vehicle_note[]', $veh->vehicle_note, array('class' => 'input is-small', 'readonly' => true)) }}
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </fieldset>
                    <!-- End Vehicle -->

                    <!-- Material -->
                    <?php $getTaskMaterial = \Tritiyo\Task\Models\TaskMaterial::where('task_id', $task_id)->get(); ?>

                    <fieldset class="pb-5">
                        <label>Material Information
                            <a href="{{route('taskmaterial.create')}}?task_id={{$task_id}}&information=materialInformation"
                               class="is-link is-size-7 is-small"> Edit </a>
                        </label>
                        @if(is_array($getTaskMaterial->toArray()))
                            @foreach( $getTaskMaterial as $mat)
                                <div class="columns">
                                    <div class="column is-3">
                                        <div class="field">
                                            {{ Form::label('material_id', 'Material', array('class' => 'label')) }}
                                            <div class="control">
                                                <?php $materialName = \Tritiyo\Material\Models\Material::where('id', $mat->material_id)->first()->name;?>
                                                <input type="hidden" name="material_id" class="input is-small"
                                                       value="{{$mat->material_id}}">
                                                <input type="text" name="" class="input is-small"
                                                       value="{{$materialName}}"
                                                       readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="column is-2">
                                        {{ Form::label('material_qty', 'Material Qty', array('class' => 'label')) }}
                                        {{ Form::text('material_qty[]', $mat->material_qty, array('class' => 'input is-small', 'readonly' => true)) }}
                                    </div>

                                    <div class="column is-2">
                                        {{ Form::label('material_amount', 'Amount', array('class' => 'label')) }}
                                        {{ Form::text('material_amount[]', $mat->material_amount, array('class' => 'input is-small', 'readonly' => true)) }}
                                    </div>
                                    <div class="column is-5">
                                        {{ Form::label('material_note', 'Note', array('class' => 'label')) }}
                                        {{ Form::text('material_note[]', $mat->material_note, array('class' => 'input is-small', 'readonly' => true)) }}
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </fieldset>

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
                    <div class="columns">
                        <div class="column">
                            <strong>Transport Allowances Breakdown</strong>
                            @if(!empty($task_transport_breakdown))
                                <a style="float: right">
                                            <span style="cursor: pointer;" class="tag is-normal is-success"
                                                  id="addrowTa">Add &nbsp; <strong>+</strong></span>
                                </a>
                            @endif
                        </div>
                        <div class="block">
                        </div>
                    </div>
                    <div id="ta_wrap">
                        @php $ta_count = 0; @endphp
                        @if(!empty($task_transport_breakdown))
                            @foreach($task_transport_breakdown as $key => $item)
                                <div class="columns">
                                    <div class="column is-1">
                                        <div class="block" style="margin-top: 3px;">
                                            {{--                                            <span style="cursor: pointer;" class="tag is-danger ibtnDelTa">--}}
                                            {{--                                            Del <button class="delete is-small"></button>--}}
                                            {{--                                            </span>--}}
                                        </div>
                                    </div>
                                    <div class="column is-3">
                                        <input type="text" name="transport[{{$ta_count = $key}}][where_to_where]"
                                               class="where_to_where input is-small" value="{{$item->where_to_where}}" required/>
                                    </div>
                                    <div class="column is-2">
                                        <div class="control">
                                            <div class="select is-small">
                                                <?php
                                                $transports = [
                                                    'Bus', 'Rickshaw', 'CNG', 'Taxi', 'Auto', 'Tempo', 'Van', 'Train', 'Boat', 'Other'
                                                ];
                                                ?>
                                                <select name="transport[{{$ta_count = $key}}][transport_type]" required>
                                                    <option value="">Select Transport Type</option>
                                                    @foreach($transports as $transport)
                                                        <option
                                                            value="{{ $transport }}" {{$item->transport_type == $transport ? 'selected' : ''}}>{{ $transport }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-2">
                                        <input class="input is-small" name="transport[{{$ta_count = $key}}][ta_amount]"
                                               type="number" min="0"
                                               step=".01" value="{{$item->ta_amount}}" required/>
                                    </div>
                                    <div class="column">
                                        <input class="input is-small" name="transport[{{$ta_count = $key}}][ta_note]"
                                               type="text"
                                               value="{{$item->ta_note}}" required/>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="columns">
                                <div class="column is-1">
                                    <div class="block" style="margin-top: 3px;">
                                        <a style="display: block">
                                            <span style="cursor: pointer;" class="tag is-success"
                                                  id="addrowTa">Add &nbsp; <strong>+</strong></span>
                                        </a>
                                    </div>
                                </div>
                                <div class="column is-3">
                                    <input type="text" name="transport[0][where_to_where]"
                                           class="where_to_where input is-small"
                                           placeholder="Where to Where" required/>
                                </div>
                                <div class="column is-2">
                                    <div class="control">
                                        <div class="select is-small">
                                            <?php
                                            $transports = [
                                                'Bus', 'Rickshaw', 'CNG', 'Taxi', 'Auto', 'Tempo', 'Van', 'Train', 'Boat', 'Other'
                                            ];
                                            ?>
                                            <select name="transport[0][transport_type]" required>
                                                <option>Select Transport Type</option>
                                                @foreach($transports as $transport)
                                                    <option value="{{ $transport }}">{{ $transport }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-2">
                                    <input class="input is-small" name="transport[0][ta_amount]" type="number" min="0"
                                           step=".01" placeholder="TA Amount" required/>
                                </div>
                                <div class="column">
                                    <input class="input is-small" name="transport[0][ta_note]" type="text"
                                           placeholder="TA Note" required/>
                                </div>
                            </div>
                        @endif
                    </div>
                    <!-- End Transport -->
                    <br/>
                    <!-- Purchase  -->
                    <div class="columns">
                        <div class="column">
                            <strong>Purchase Breakdown</strong>
                            @if(!empty($task_purchase_breakdown))
                                <a style="float: right">
                                            <span style="cursor: pointer;" class="tag is-success"
                                                  id="addrowPa">Add &nbsp; <strong>+</strong></span>
                                </a>
                            @endif
                        </div>
                        <div class="block">
                        </div>
                    </div>
                    <div id="pa_wrap">
                        @php $pa_count = 0; @endphp
                        @if(!empty($task_purchase_breakdown))
                            @foreach($task_purchase_breakdown as $key => $item)
                                <div class="columns">
                                    <div class="column is-1">
                                        <div class="block" style="margin-top: 3px;">
                                            {{--                                        <span style="cursor: pointer;" class="tag is-danger ibtnDelPa">--}}
                                            {{--                                            Del <button class="delete is-small"></button>--}}
                                            {{--                                        </span>--}}
                                        </div>
                                    </div>
                                    <div class="column is-2">
                                        <input class="input is-small" name="purchase[{{$pa_count = $key}}][pa_amount]"
                                               type="number" min="0"
                                               step=".01"
                                               value="{{$item->pa_amount}}" required/>
                                    </div>
                                    <div class="column">
                                        <input class="input is-small" name="purchase[{{$pa_count = $key}}][pa_note]"
                                               type="text"
                                               value="{{$item->pa_note}}" required/>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="columns">
                                <div class="column is-1">
                                    <div class="block" style="margin-top: 3px;">
                                        <a style="display: block">
                                            <span style="cursor: pointer;" class="tag is-success"
                                                  id="addrowPa">Add &nbsp; <strong>+</strong></span>
                                        </a>
                                    </div>
                                </div>
                                <div class="column is-2">
                                    <input class="input is-small" name="purchase[0][pa_amount]" type="number" min="0"
                                           step=".01"
                                           placeholder="PA Amount" required/>
                                </div>
                                <div class="column">
                                    <input class="input is-small" name="purchase[0][pa_note]" type="text"
                                           placeholder="PA Note" required/>
                                </div>
                            </div>
                        @endif
                    </div>
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
    @include('task::task_status_sidebar')
@endsection

@endif
@section('cusjs')

    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://ajax.aspnetcdn.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js"
            type="text/javascript"></script>


    <script id="ta_form" type="text/template">
        <div class="columns" counter="">
            <div class="column is-1">
                <div class="block" style="margin-top: 3px;">
                    <span style="cursor: pointer;" class="tag is-danger ibtnDelTa">
                    Del <button class="delete is-small"></button>
                    </span>
                </div>
            </div>
            <div class="column is-3">
                <input type="text" name=""
                       class="input is-small where_to_where"
                       placeholder="Where to Where" required/>
            </div>
            <div class="column is-2">
                <div class="control">
                    <div class="select is-small">
                        <?php
                        $transports = [
                            'Bus', 'Rickshaw', 'CNG', 'Taxi', 'Auto', 'Tempo', 'Van', 'Train', 'Boat', 'Other'
                        ];
                        ?>
                        <select name="" class="transport_type" required>
                            <option>Select Transport Type</option>
                            @foreach($transports as $transport)
                                <option value="{{ $transport }}">{{ $transport }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="column is-2">
                <input class="input is-small ta_amount" name="" type="number" min="0" step=".01"
                       placeholder="TA Amount" required/>
            </div>
            <div class="column">
                <input class="input is-small ta_note" name="" type="text"
                       placeholder="TA Note" required/>
            </div>
        </div>
    </script>

    <script id="pa_form" type="text/template">
        <div class="columns">
            <div class="column is-1">
                <div class="block" style="margin-top: 3px;">
                    <span style="cursor: pointer;" class="tag is-danger ibtnDelPa">
                    Del <button class="delete is-small"></button>
                    </span>
                </div>
            </div>
            <div class="column is-2">
                <input class="input is-small pa_amount" name="" type="number" min="0" step=".01"
                       placeholder="PA Amount" required/>
            </div>
            <div class="column">
                <input class="input is-small pa_note" name="" type="text"
                       placeholder="PA Note" required/>
            </div>
        </div>
    </script>

    <script>
        //Add Row Function
        $(document).ready(function () {
            var ta_counter = '{{$ta_count +1}}';
            var pa_counter = '{{$pa_count + 1}}';
            //Transport
            $("#addrowTa").on("click", function () {
                var cols = '<div class="ta' + ta_counter + '">';
                cols += $('#ta_form').html();
                cols += '</div>';
                $("div#ta_wrap").append(cols);

                $(".ta" + ta_counter + " .where_to_where").attr('name', "transport[" + ta_counter + "][where_to_where]");
                $(".ta" + ta_counter + " .transport_type").attr('name', "transport[" + ta_counter + "][transport_type]");
                $(".ta" + ta_counter + " .ta_amount").attr('name', "transport[" + ta_counter + "][ta_amount]");
                $(".ta" + ta_counter + " .ta_note").attr('name', "transport[" + ta_counter + "][ta_note]");

                ta_counter++;
            });

            //Purchase
            $("#addrowPa").on("click", function () {
                var cols_pa = '<div class="pa' + pa_counter + '">';
                cols_pa += $('#pa_form').html();
                cols_pa += '</div>';

                $("div#pa_wrap").append(cols_pa);

                $(".pa" + pa_counter + " .pa_amount").attr('name', "purchase[" + pa_counter + "][pa_amount]");
                $(".pa" + pa_counter + " .pa_note").attr('name', "purchase[" + pa_counter + "][pa_note]");

                pa_counter++;
            });

            $("div#ta_wrap").on("click", ".ibtnDelTa", function (event) {
                $(this).closest("div.columns").remove();
                counter -= 1
            });
            $("div#pa_wrap").on("click", ".ibtnDelPa", function (event) {
                $(this).closest("div.columns").remove();
                counter -= 1
            });
        });

    </script>


@endsection

