@extends('layouts.app')

<?php
$project = \Tritiyo\Project\Models\Project::where('id', Request::get('project_id'))->first();
?>

@section('title')
    Edit Project Status
@endsection
@if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
    @php
        $addUrl = route('project_ranges.create');
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
            'spTitle' => 'Edit Project Status',
            'spSubTitle' => 'Edit a single project',
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
                <div class="column is-4">
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

                <div class="column is-8">
                    <div class="columns">
                        <div class="column is-12">
                            @php
                                $exists = \Tritiyo\Project\Models\ProjectRange::where('project_id', $project->id)->orderBy('id', 'desc')->get();
                                //dd($exists);
                            @endphp

                            <div style="color: red; font-size: 20px; margin: 0px 0 20px 0px;">
                                Please double check what you doing here
                            </div>

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
                                <div class="columns">
                                    <div class="column is-6">
                                        <p>Current Range Budget Till Today:</p>
                                        <span style="color: green; font-size: 25px;">
                                            @php
                                                $allCurrentBudgets = \Tritiyo\Project\Helpers\ProjectHelper::current_range_budgets($project->id);
                                            @endphp
                                            {{ $allCurrentBudgets ?? NULL }}
                                        </span>
                                    </div>
                                    <div class="column is-6">
                                        <p>Total Project Budget of All Time:</p>
                                        <span style="color: green; font-size: 25px;">
                                            @php
                                                $allBudgets = \Tritiyo\Project\Helpers\ProjectHelper::all_range_budgets($project->id);
                                            @endphp
                                            {{ $allBudgets ?? NULL }}
                                        </span>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>

                    <div class="customContainer">
                        @if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
                            <?php
                            $routeUrl = route('project_budgets.store');
                            $method = 'post';
                            ?>
                            @if(count($exists) > 0)
                                <?php if($exists[0]['project_status'] == 'Active') { ?>
                                {{ Form::open(array('url' => $routeUrl, 'method' => $method, 'value' => 'PATCH', 'id' => 'add_route', 'class' => 'project_budgets_table',  'files' => true, 'autocomplete' => 'off')) }}


                                <label for="budget_amount" class="label">Budget Amount</label>
                                <input name="project_id" type="hidden" value="{{ $project->id ?? NULL }}"
                                       required>
                                <input name="current_range_id" type="hidden"
                                       value="{{ $exists[0]->id ?? NULL }}" required>
                                <div class="field">
                                    <input name="budget_amount" type="number" value="" class="input is-small"
                                           required>
                                </div>

                                @php
                                    $current_range_id = \Tritiyo\Project\Helpers\ProjectHelper::current_range_id($project->id);

                                    //SELECT count(id) FROM `sites` WHERE `project_id` = 4 AND site_type = 'Recurring' GROUP BY site_type
                                    $is_recurring = \Tritiyo\Site\Models\Site::select('id')->where('project_id', $project->id)->where('site_type', 'Recurring')->groupBy('site_type')->get();

                                      //dd();
                                      if(!empty($is_recurring) && count($is_recurring) > 0) {
                                              $sites = \Tritiyo\Site\Models\Site::where('project_id', $project->id)
                                              ->whereNotIn('site_type', ['Recurring'])
                                              ->where('range_ids', $current_range_id)
                                              ->latest()->get();
                                      } else {
                                              $sites = \Tritiyo\Site\Models\Site::where('project_id', $project->id)->latest()->get();
                                      }
                                @endphp
                                <?php /*
                                <div class="field">
                                    <label class="label">Select Site</label>
                                    <select class="input is-small" name="site_id[]" id="site_select"
                                            multiple="multiple">
                                        <option></option>
                                        @foreach($sites as $site)
                                            <option value="{{$site->id}}">{{$site->site_code}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                */ ?>


                                <div class="field">
                                    <label class="label">Select Site</label>
                                    <select id="select3" name="site_id[]" multiple="multiple" size="15">
                                        @foreach($sites as $site)
                                            <option value="{{$site->id}}">{{$site->site_code}}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="field is-grouped">
                                    <div class="control">
                                        <button class="button is-success is-small" style="margin-top: 10px;">
                                            Add Budget
                                        </button>
                                    </div>
                                </div>

                                {{ Form::close() }}
                                <?php } else { ?>
                                This project currently inactive. You can not add any budget to this project anymore.
                                <?php } ?>
                            @endif
                        @endif

                        <div style="margin-top: 20px;">
                            <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                <tbody>
                                <tr>
                                    <th>Row ID</th>
                                    <th>Project ID</th>
                                    <th>Range ID</th>
                                    <th>Budget Amount</th>
                                    <th>Site Code</th>
                                    <th>Budget Added at</th>
                                    <th>Action</th>
                                </tr>

                                @php
                                    $project_budgets = \Tritiyo\Project\Models\ProjectBudget::where('project_id', $project->id)->orderBy('id', 'desc')->get();
                                @endphp
                                @foreach($project_budgets as $budget)
                                    <tr>
                                        <td>{{ $budget->id }}</td>
                                        <td>{{ $budget->project_id }}</td>
                                        <td>{{ $budget->current_range_id }}</td>
                                        <td>{{ $budget->budget_amount }}</td>
                                        <td>
                                            @php
                                                $explode = explode(',', $budget->site_id);
                                                //dump($explode);
                                                foreach($explode as $s){
                                                    echo '<a target="_blank" href="'.route('sites.show', $s).'">';
                                                    echo \Tritiyo\Site\Models\Site::where('id', $s)->first()->site_code ?? Null;
                                                    echo '</a>  ';
                                                }

                                            @endphp
                                        </td>
                                        <td>{{ $budget->created_at }}</td>
                                        <td>
                                            @if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
                                                {!! delete_data('project_budgets.destroy',  $budget->id) !!}
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </article>
@endsection

@section('column_right')
    <article class="is-primary">
        <div class="box">
            <h1 class="title is-5">Important Note</h1>
            <p>
                Please select project manager and budget properly
            </p>
        </div>
    </article>
@endsection


@section('cusjs')


    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>



    <link rel="stylesheet" href="{{asset('/public/')}}/lib/dualselect.css" />
    <script type="text/javascript" language="javascript" src="{{asset('/public/')}}/lib/dualselect.js"></script>

    <script>
        // Select 2
        function siteSelectRefresh() {
            $('select#site_select').select2({
                placeholder: "Select Site",
                allowClear: true,
            });
        }

        siteSelectRefresh()

        // let dlb1 = new DualListbox('.select1');
    </script>







    <script type="text/javascript" language="javascript">
        jQuery(document).ready(function() {
            dualselect2 = jQuery('#select3').dualselect({
                beforeSelectOption: function (_option) {
                    if (_option.text().indexOf('option30') >= 0) {
                        alert('option30 selection not allowed');
                        return false;
                    }
                    return true;
                }
                ,beforeRemoveOption: function (_option) {
                    if (_option.text().indexOf('option25') >= 0) {
                        alert('option25 removal not allowed');
                        return false;
                    }
                    return true;
                }
                ,moveOnSelect: false
                ,showMoveButtons: true
                ,showFilters: true
            });
        });
    </script>

@endsection
