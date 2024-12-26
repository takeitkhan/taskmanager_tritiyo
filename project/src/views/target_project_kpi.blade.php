
@extends('layouts.app')

<?php
$project = \Tritiyo\Project\Models\Project::where('id', Request::get('project_id'))->first();
?>

@section('title')
   Set Target of Key Performance indicators
@endsection
@if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
    @php
        $addUrl = route('target.projects.kpi.create', ['project_id' => $project->id]);
    @endphp
@else
    @php
        $addUrl = '#';
    @endphp
@endif

<?php

?>
<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Set Target of Key Performance indicators',
            'spSubTitle' => '',
            'spShowTitleSet' => true
        ])

        @include('component.button_set', [
            'spShowButtonSet' => true,
            'spAddUrl' => null,
            'spAddUrl' => $addUrl,
            'spAllData' => route('projects.index'),
            'spSearchData' => route('projects.search'),
            'spTitle' => 'Projects',
        ])

        @include('component.filter_set', [
            'spShowFilterSet' => true,
            'spAddUrl' => route('projects.create'),
            'spAllData' => route('projects.index'),
            'spSearchData' => route('projects.search'),
            'spPlaceholder' => 'Search projects...',
            'spMessage' => $message = $message ?? NULl,
            'spStatus' => $status = $status ?? NULL
        ])
    </nav>
</section>
@section('column_left')
    <article class="panel is-primary">
        <p class="panel-tabs">
            <a class="is-active">Project Information</a>
        </p>
        <div class="customContainer">
            <div class="columns">
                <div class="column is-2">
                    <div class="field">
                        {{ Form::label('name', 'Project Name', array('class' => 'label')) }}
                        <div class="control">
                            {{ $project->name ?? NULL }}
                        </div>
                    </div>
                    <div class="field">
                        {{ Form::label('code', 'Project Code', array('class' => 'label')) }}
                        <div class="control">
                            {{ $project->code ?? NULL }}
                        </div>
                    </div>
                    <div class="field">
                        {{ Form::label('type', 'Project Type', array('class' => 'label')) }}
                        <div class="control">
                            {{ $project->type ?? NULL }}
                        </div>
                    </div>
                    <div class="field">
                        {{ Form::label('manager', 'Project Manager', array('class' => 'label')) }}
                        <div class="control">
                            <?php $manager = \App\Models\User::where('id', $project->manager ?? NULL)->first()->name; ?>
                            {{ $manager }}
                        </div>
                    </div>
                    <div class="field">
                        {{ Form::label('customer', 'Project customer', array('class' => 'label')) }}
                        <div class="control">
                            {{ $project->customer ?? NULL }}
                        </div>
                    </div>
                    <div class="field">
                        {{ Form::label('vendor', 'Project vendor', array('class' => 'label')) }}
                        <div class="control">
                            {{ $project->vendor ?? NULL }}
                        </div>
                    </div>
                    <div class="field">
                        {{ Form::label('supplier', 'Project supplier', array('class' => 'label')) }}
                        <div class="control">
                            {{ $project->supplier ?? NULL }}
                        </div>
                    </div>
                    <div class="field">
                        {{ Form::label('address', 'Project address', array('class' => 'label')) }}
                        <div class="control">
                            {{ $project->address ?? NULL }}
                        </div>
                    </div>
                    <div class="field">
                        {{ Form::label('location', 'Project location', array('class' => 'label')) }}
                        <div class="control">
                            {{ $project->location ?? NULL }}
                        </div>
                    </div>
                    <div class="field">
                        {{ Form::label('office', 'Head Office', array('class' => 'label')) }}
                        <div class="control">
                            {{ $project->office ?? NULL }}
                        </div>
                    </div>
                    <div class="field">
                        {{ Form::label('start', 'Project start', array('class' => 'label')) }}
                        <div class="control">
                            {{ $project->start ?? NULL }}
                        </div>
                    </div>
                    <div class="field">
                        {{ Form::label('end', 'Approximate project end', array('class' => 'label')) }}
                        <div class="control">
                            {{ $project->end ?? NULL }}
                        </div>
                    </div>
                    <div class="field">
                        {{ Form::label('budget', 'Project approximate budget', array('class' => 'label')) }}
                        <div class="control">
                            {{ $project->budget ?? NULL }}
                        </div>
                    </div>
                    <div class="field">
                        {{ Form::label('summary', 'Project summary', array('class' => 'label')) }}
                        <div class="control">
                            {{ $project->summary ?? NULL }}
                        </div>
                    </div>
                </div>

                <div class="column is-4">
                    <div class="columns mt-1">
                        <div class="column is-12">
                            @php
                                $exists = \Tritiyo\Project\Models\ProjectRange::where('project_id', $project->id)->orderBy('id', 'desc')->get();
                                //dd($exists);
                            @endphp


                            <div style="margin: 0px 0 20px 0px;">
                                <div class="columns">
                                    <div class="column is-6">
                                        <p>Current Project Status:</p>
                                        <span style="color: green; font-size: 25px;">
                                            {{ \Tritiyo\Project\Models\ProjectRange::where('project_id', $project->id)->orderBy('id', 'desc')->first()->project_status ?? NULL }}
                                        </span>
                                    </div>
                                    <div class="column is-6">
                                        <p>Last Project Status Updated at:</p>
                                        <span style="color: green; font-size: 25px;">
                                            {{ \Tritiyo\Project\Models\ProjectRange::where('project_id', $project->id)->orderBy('id', 'desc')->first()->status_update_date ?? NULL }}
                                        </span>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
@php
    $checkRange =  \Tritiyo\Project\Models\TargetProjectKpi::where('target_range', 'april_to_june')->where('year', date('Y'))->get();
//dd($checkRange);
@endphp
                    <div class="customContainer">
                        @if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
                            <?php
                            $routeUrl = !empty($editKpi) ? route('target.projects.kpi.update') : route('target.projects.kpi.store');
                            $method = 'post';
                            ?>
                            @if(count($exists) > 0)
                                <?php if($exists[0]['project_status'] == 'Active') { ?>
                                {{ Form::open(array('url' => $routeUrl, 'method' => $method, 'value' => 'PATCH', 'id' => 'add_route', 'class' => 'project_budgets_table',  'files' => true, 'autocomplete' => 'off')) }}

                                @if(!empty($editKpi))
                                        <input name="status_key" type="hidden" value="{{$editKpi[0]->status_key ?? Null}}"  required>
                                @endif

                                <input name="project_id" type="hidden" value="{{ $project->id ?? NULL }}"  required>
                                <input name="manager" type="hidden" value="{{ $project->manager ?? NULL }}"  required>
                                <input name="current_range_id" type="hidden" value="{{ $exists[0]->id ?? NULL }}" required>
                                <input name="year" type="hidden" value="{{ date('Y') }}"  required>
                                <div class="field">
                                    <label class="label">Select Range</label>
                                    <select class="input is-small" name="target_range" id="targetRange">
                                        <option>Select Range</option>
                                <?php
                                    $perPeriod = 3;

                                    $yearFirstDate = date('Y-m-d',strtotime(date('Y-01-01')));
                                    $yearLastDate = date('Y-m-d',strtotime(date('Y-12-31')));
                                    $period = \Carbon\CarbonPeriod::create($yearFirstDate, $perPeriod.' month', $yearLastDate);
                                    foreach ($period as $dt) {
                                        $monthStart =  $dt->format("F");
                                        $monthEnd = date("F", strtotime($dt->format("Y-m-d"). " +".$perPeriod." Month -1 Day"));
                                        $optionName = $monthStart. ' To '. $monthEnd;
                                        $optionValue = $monthStart. '_to_'. $monthEnd;

                                        //Check
                                        $start_date = $dt->format("Y-m-d");
                                        $end_date = date("Y-m-d", strtotime($dt->format("Y-m-d"). " +".$perPeriod." Month -1 Day"));;
                                        $date_check = date('Y-m-d');


                                        if (\Tritiyo\Project\Helpers\ProjectKpiHelper::checkInRange($start_date, $end_date, $date_check)) {
                                            $rangeCheck = 'Yes';
                                        } else {
                                            $rangeCheck = 'No';
                                        }
                                        //check in DB if this range is exists
                                        $checkRange =  \Tritiyo\Project\Models\TargetProjectKpi::where('target_range', $optionValue)
                                                        ->where('year', date('Y'))->first();
                                        ?>
                                        <?php /*
                                        @if(empty($checkRange))
                                            <option data-range="{{$start_date.' | '.$end_date}}"  {{$optionValue}} value="{{strtolower($optionValue)}}" style="display: {{$rangeCheck == 'Yes' ? '' : 'none'}}">{{$optionName}}</option>
                                        @endif
                                        */?>
                                        <option data-range="{{$start_date.' | '.$end_date}}"  {{$optionValue}} value="{{strtolower($optionValue)}}" {{!empty($editKpi) && $editKpi[0]->target_range == strtolower($optionValue) ? 'selected' : ''}}  style="display: {{$rangeCheck == 'Yes' ? '' : 'none'}}">{{$optionName}}</option>
                                   <?php  } ?>
                                    </select>
                                </div>
                                <input type="text" class="input is-small my-2" name="target_start_end_date" value="{{$editKpi[0]->target_range_date ?? Null}}"  id="start_end_date" readonly/>
                                <div class="field">
                                    <div class="field w_49">
                                        <label for="projcet_costing" class="label">Target of Project Costing</label>
                                        <input name="meta_key[projcet_costing][target]" type="number" value="{{$editKpi[0]->target_project_costing ?? Null}}" class="input is-small"
                                               required>
                                        <input name="meta_key[projcet_costing][type]" type="hidden" value="Reverse" class="input is-small">
                                    </div>
                                    <div class="field w_49">
                                        <label for="projcet_costing" class="label">Mark of Project Costing</label>
                                        <input name="meta_key[projcet_costing][mark]" type="number" value="{{$editKpi[0]->mark_project_costing ?? 30}}" class="input is-small">
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field w_49">
                                        <label for="task_limit" class="label">Target of Task Limit</label>
                                        <input name="meta_key[task_limit][target]" type="number" value="{{$editKpi[0]->target_task_limit ?? Null}}" class="input is-small" required>
                                        <input name="meta_key[task_limit][type]" type="hidden" value="Reverse" class="input is-small">
                                    </div>
                                    <div class="field w_49">
                                        <label for="task_limit" class="label">Mark of Task Limit</label>
                                        <input name="meta_key[task_limit][mark]" type="number" value="{{$editKpi[0]->mark_task_limit ?? 20}}" class="input is-small">
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field w_49">
                                        <label for="site_completion" class="label">Target of Site Completion</label>
                                        <input name="meta_key[site_completion][target]" type="number" value="{{$editKpi[0]->target_site_completion ?? Null}}" class="input is-small" required>
                                        <input name="meta_key[site_completion][type]" type="hidden" value="Forward" class="input is-small">
                                    </div>
                                    <div class="field w_49">
                                        <label for="site_completion" class="label">Mark of Site Completion</label>
                                        <input name="meta_key[site_completion][mark]" type="number" value="{{$editKpi[0]->mark_site_completion ?? 20}}" class="input is-small">
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field w_49">
                                        <label for="invoice_submit" class="label">Target of Invoice Submission</label>
                                        <input name="meta_key[invoice_submission][target]" type="number" value="{{$editKpi[0]->target_invoice_submission ?? Null}}" class="input is-small" required>
                                        <input name="meta_key[invoice_submission][type]" type="hidden" value="Forward" class="input is-small">
                                    </div>
                                    <div class="field w_49">
                                        <label for="invoice_submit" class="label">Mark of Invoice Submission</label>
                                        <input name="meta_key[invoice_submission][mark]" type="number" value="{{$editKpi[0]->mark_invoice_submission ?? 30}}" class="input is-small">
                                    </div>
                                </div>

                                <div class="field">
                                    <label for="bonus_85_89" class="label">Bonus for performance (85%-89%)</label>
                                    <div class="field">
                                        <input name="meta_key[bonus_85_89][target]" type="number" value="{{$editKpi[0]->bonus_85_89 ?? Null}}" class="input is-small">
                                    </div>
                                </div>

                                <div class="field">
                                    <label for="bonus_90_94" class="label">Bonus for performance (90%-94%)</label>
                                    <div class="field">
                                        <input name="meta_key[bonus_90_94][target]" type="number" value="{{$editKpi[0]->bonus_90_94 ?? Null}}" class="input is-small">
                                    </div>
                                </div>

                                <div class="field">
                                    <label for="bonus_95_100" class="label">Bonus for performance (95%-100%)</label>
                                    <div class="field">
                                        <input name="meta_key[bonus_95_100][target]" type="number" value="{{$editKpi[0]->bonus_95_100 ?? Null}}" class="input is-small">
                                    </div>
                                </div>


                                <div class="field is-grouped">
                                    <div class="control">
                                        <button class="button is-success is-small" style="margin-top: 10px;">
                                            {{!empty($editKpi) ? 'Update KPI' : 'Set KPI'}}
                                        </button>
                                    </div>
                                </div>

                                {{ Form::close() }}
                                <?php } else { ?>
                                This project currently inactive. You can not add any budget to this project anymore.
                                <?php } ?>
                            @endif
                        @endif

                    </div>


                </div>

                <div class="column is-6">
                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                        <tbody>
                        <tr style="text-align: center;">
                            <th colspan="3"></th>
                            <th colspan="4">Target</th>
                            <th colspan="3"></th>
                        </tr>
                        <tr>
                            <th>SL</th>
                            <th>Range</th>
                            <th>Year</th>
                            <th>Costing</th>
                            <th>Task Limit</th>
                            <th>Site Completion</th>
                            <th>Invoice Submission</th>
                            <th>Bonus Segment</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>

                        @php
                            $project_kpi = \Tritiyo\Project\Models\TargetProjectKpi::where('project_id', $project->id)
                                            ->orderBy('id', 'desc')
                                            ->groupBy('status_key')
                                            ->get();
                        @endphp

                        @foreach($project_kpi as $key => $kpi)
                            <tr style="background: {{$kpi->status_key == request()->get('status_key') ? '#faebd7' : ''}}">
                                <td>{{++$key}}</td>
                                <td>
                                   <strong>{{$kpi->target_range}} </strong> <br/>
                                    {{$kpi->target_range_date}}
                                </td>
                                <td>{{$kpi->year}}</td>
                                @php
                                    $single_kpi = \Tritiyo\Project\Models\TargetProjectKpi::where('project_id', $kpi->project_id)
                                            ->where('status_key', $kpi->status_key)
                                            ->get();
                                @endphp
                                <td>
                                    <strong>Target:</strong> {{ $single_kpi[0]['meta_value'] }}<br/>
                                    <strong>Mark:</strong> {{ $single_kpi[0]['mark'] }}<br/>
                                </td>
                                <td>
                                    <strong>Target:</strong> {{ $single_kpi[1]['meta_value'] }}<br/>
                                    <strong>Mark:</strong> {{ $single_kpi[1]['mark'] }}<br/>
                                </td>
                                <td>
                                    <strong>Target:</strong> {{ $single_kpi[2]['meta_value'] }}<br/>
                                    <strong>Mark:</strong> {{ $single_kpi[2]['mark'] }}<br/>
                                </td>
                                <td>
                                    <strong>Target:</strong> {{ $single_kpi[3]['meta_value'] }}<br/>
                                    <strong>Mark:</strong> {{ $single_kpi[3]['mark'] }}<br/>
                                </td>
                                <td>
                                    <strong>85% - 89%:</strong> {{ $single_kpi[4]['meta_value'] }}<br/>
                                    <strong>90% - 94%:</strong> {{ $single_kpi[5]['meta_value'] }}<br/>
                                    <strong>95% - 100%:</strong> {{ $single_kpi[6]['meta_value'] }}<br/>
                                </td>
                                <td>{{$kpi->created_at}}</td>
                                <td>
                                    <a href="{{route('target.projects.kpi.edit')}}?status_key={{$kpi->status_key}}&&project_id={{$kpi->project_id}}">
                                        Edit
                                    </a>
                                    <a href="{{route('target.projects.kpi.delete')}}?status_key={{$kpi->status_key}}" onclick="confirm('Are you sure?')">
                                        Delete
                                    </a>

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </article>
@endsection




@section('cusjs')


    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

    <script>
        // Select 2
        function siteSelectRefresh() {
            $('select#site_select').select2({
                placeholder: "Select Site",
                allowClear: true,
            });
        }
        siteSelectRefresh()
    </script>

    <script>
        $('select#targetRange').change(function(){
            let date = $(this).find(':selected').data('range');
            $('input#start_end_date').val(date);
        })
    </script>

    <style>
        .w_49 {
            width: 49% !important;
            display: inline-block;
        }
    </style>
@endsection
