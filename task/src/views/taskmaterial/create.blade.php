@extends('layouts.app')
@section('title')
    Include material information of task
@endsection



@php
		$task_id = request()->get('task_id');
        $task = \Tritiyo\Task\Models\Task::where('id', $task_id)->first(); 
   
@endphp      

@php
    $remaining_balance = \Tritiyo\Project\Helpers\ProjectHelper::remainingBalance($task->project_id, $task->current_range_id);
    $today_use = \Tritiyo\Task\Helpers\RequisitionData::todayManagerUsedAmount($task->project_id, $task->current_range_id);
    $of95 = $remaining_balance - $today_use;
@endphp

<div style="display: none;">
        <input type="text" value="{{ $of95 }}" id="of95" />
</div>

<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Material',
            'spSubTitle' => 'Add a metarial of task',
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

        <?php //$task_id = request()->get('task_id');?>

            @include('task::layouts.tab')



        <div class="customContainer">
            <?php
            if (!empty($task_id) && $task_id) {
                $routeUrl = route('taskmaterial.update', $task_id);
                $method = 'PUT';
            } else {
                $routeUrl = route('taskmaterial.store');
                $method = 'post';
            } ?>

            {{ Form::open(array('url' => $routeUrl, 'method' => $method, 'value' => 'PATCH', 'id' => 'add_route', 'class' => 'task_material_table',  'files' => true, 'autocomplete' => 'off')) }}

            @if($task_id)
                {{ Form::hidden('task_id', $task_id ?? '') }}
            @endif
            @if(!empty($taskId))
                {{ Form::hidden('tassk_id', $taskId ?? '') }}
            @endif

            @php
                $materials = \Tritiyo\Material\Models\Material::get();
                $getTaskMaterial = \Tritiyo\Task\Models\TaskMaterial::where('task_id', $task_id)->get()
            @endphp
            <div id="myTable">
            @if(count( $getTaskMaterial) > 0)
                @foreach( $getTaskMaterial as $mat)
                    <div class="columns s{{$mat->id}}">
                            <div class="column is-2">
                                <div class="field">
                                    {{ Form::label('material_id', 'Material', array('class' => 'label')) }}
                                    <div class="control">
                                        <select name="material_id[]" id="material_select" class="input" required>
                                            <option value="">Select Material</option>
                                            @foreach($materials as $material)
                                                <option
                                                    value="{{$material->id}}" {{ $mat->material_id == $material->id ? 'selected' : '' }} >{{$material->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-2">
                                {{ Form::label('material_qty', 'Material Qty', array('class' => 'label')) }}
                                {{ Form::number('material_qty[]', $mat->material_qty, array('class' => 'input is-small')) }}
                            </div>

                            <div class="column is-2">
                                {{ Form::label('material_amount', 'Amount', array('class' => 'label')) }}
                                {{ Form::number('material_amount[]', $mat->material_amount, array('class' => 'input is-small')) }}
                            </div>
                            <div class="column is-5">
                                {{ Form::label('material_note', 'Note', array('class' => 'label')) }}
                                {{ Form::text('material_note[]', $mat->material_note, array('class' => 'input is-small')) }}
                            </div>

                            <div class="column is-1">
                                <label></label> <br/>
                                <a><span class="tag is-danger is-small ibtnDel">Delete</span></a>
                            </div>

                        </div>
                @endforeach
            @else
                    <div class="columns">
                        <div class="column is-2">
                            <label for="material_id" class="label">Material</label>
                            <select name="material_id[]" id="material_select" class="input is-small" required>
                                <option></option>
                                @foreach($materials as $material)
                                    <option value="{{$material->id}}">{{$material->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="column is-2">
                            <label for="material_qty" class="label">Material Qty</label>
                            <input name="material_qty[]" type="number" value="" class="input is-small" required>
                        </div>
                        <div class="column is-2">
                            <label for="material_amount" class="label">Material Amount</label>
                            <input name="material_amount[]" type="number" value="" class="input is-small material_amount">
                        </div>
                        <div class="column is-5">
                            <label for="material_note" class="label">Note</label>
                            <input name="material_note[]" type="text" value="" class="input is-small">
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
                            <button class="button is-success is-small" id="save_changes">Save Changes</button>
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
        //$task = \Tritiyo\Task\Models\Task::where('id', $task_id)->first();
    @endphp
    @include('task::task_status_sidebar')

@endsection



@section('cusjs')
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script>
        //Add Row Function
        $(document).ready(function () {
            var counter = 1;

            $("#addrow").on("click", function () {
                var cols = '<div class="columns r' + counter + '">';
                cols += '<div class="column is-2">';
                cols += '<label for="material_id" class="label">Material</label>';
                cols += '<select name="material_id[]" id="material_select" class="input" required>';
                cols += '<option></option>';
                cols += '<?php foreach($materials as $material){?>';
                cols += '<option value="<?php echo $material->id;?>"><?php echo $material->name;?></option>';
                cols += '<?php } ?>';
                cols += '<select>';
                cols += '</div>';
                cols += '<div class="column is-2">';
                cols += '<label for="material_qty" class="label">Material Qty</label>';
                cols += '<input name="material_qty[]" type="number" value="" class="input is-small" required>';
                cols += '</div>';
                cols +=  '<div class="column is-2">';
                cols +=  '<label for="material_amount" class="label">Material Amount</label>';
                cols +=  '<input name="material_amount[]" type="number" value="" class="input is-small material_amount">';
                cols +=  '</div>';
                cols +=  '<div class="column is-5">';
                cols +=  '<label for="material_note" class="label">Note</label>';
                cols +=  '<input name="material_note[]" type="text" value="" class="input is-small">';
                cols +=  '</div>';
                cols += '<div class="column is-1">';
                cols += '<br/><a><span class="tag is-danger is-small ibtnDel">Delete</span></a>';
                cols += '</div>';
                cols += '</div>';

                $("div#myTable").append(cols);
                materialSelectRefresh();
                counter++;
            });
                          
                          
                          
                        
          	$(document).on("change", "input", function() {
                var of95 = parseInt($('#of95').val());
                           
                var sum = 0;
                $(".material_amount").each(function(){
                    sum += +$(this).val();
                });
                           
                           
               var summation = parseInt(sum);
               //var live_usages_total = summation + actual_usage;
               var live_usages_total = summation;
               if(live_usages_total > of95) {
                           $('#save_changes').hide();
                           alert('Your entered amount exceeding 100% of your current budget. Please enter lower amount to proceed.');
               } else {
                           $('#save_changes').show();
               }
            });


            $("div#myTable").on("click", ".ibtnDel", function (event) {
                $(this).closest("div.columns").remove();
                counter -= 1
            });
        });

    </script>

{{--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>--}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

    <script>

        //Select 2
        function materialSelectRefresh() {
            $('select#material_select').select2({
                placeholder: "Select Material",
                allowClear: true
            });
        }
        materialSelectRefresh()
    </script>


@endsection

