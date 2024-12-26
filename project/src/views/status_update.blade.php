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
                <div class="column is-6">
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

                <div class="column is-6">
                    <div style="color: red; font-size: 20px; margin: 0px 0 20px 0px;">
                        Please double check what you doing here
                    </div>

                    <div style="margin: 0px 0 20px 0px;">
                        <p>Current Project Status:</p>
                        <span style="color: green; font-size: 25px;">
                          {{ \Tritiyo\Project\Models\ProjectRange::where('project_id', $project->id)->orderBy('id', 'desc')->first()->project_status ?? NULL }}
                      </span>
                        <p>Last Project Status Updated at:</p>
                        <span
                            style="color: green; font-size: 25px;">{{ \Tritiyo\Project\Models\ProjectRange::where('project_id', $project->id)->orderBy('id', 'desc')->first()->status_update_date ?? NULL }}</span>
                    </div>

                    {{ Form::open(array('url' => route('project_ranges.store'), 'method' => 'post', 'value' => 'PATCH', 'id' => 'add_route', 'files' => true, 'autocomplete' => 'off')) }}
                    {{ Form::hidden('project_id', $project->id ?? NULL, ['required']) }}

                    <div class="field">
                        {{ Form::label('name', 'Project Status', array('class' => 'label')) }}
                        <div class="control">
                            @php
                                $exists = \Tritiyo\Project\Models\ProjectRange::where('project_id', $project->id)->orderBy('id', 'desc')->get();
                                //dd($exists);
                            @endphp
                            @if(count($exists) > 0)
                                <?php
                                $checkRunningNotRunning = \Tritiyo\Site\Models\Site::where('project_id', $project->id)
                                                                            ->where(function($query){
                                                                                $query->whereNull('completion_status')
                                                                                      ->orWhere('completion_status', 'Running');
                                                                            })->get();
                                //dump(count($checkRunningNotRunning));
                                if ($exists[0]['project_status'] == 'Active') {
                                    if(empty($checkRunningNotRunning) || $project->type == 'Recurring'){
                                        $project_status = ['' => 'Select a status', 'Inactive' => 'Inactive'];
                                        echo Form::hidden('status_key_type',    'Old', ['required']);
                                    }elseif(count($checkRunningNotRunning) == 0){
                                     	$project_status = ['' => 'Select a status', 'Inactive' => 'Inactive'];
                                        echo Form::hidden('status_key_type',    'Old', ['required']);
                                    } else {
                                        $project_status = [];
                                        echo 'You have '.count($checkRunningNotRunning).' running or not started sites under this project. You can not inactive this project. Please update those sites status.';
                                    }
                                } else if ($exists[0]['project_status'] == 'Inactive') {
                                    $project_status = ['' => 'Select a status', 'Active' => 'Active'];
                                    echo Form::hidden('status_key_type', 'New', ['required']);
                                }
                                ?>
                          	@if(empty($checkRunningNotRunning) || $project->type == 'Recurring')
                                    {{ Form::select('project_status', $project_status, $project_status->manager ?? NULL, ['class' => 'input']) }}
                          	
                            @elseif(count($checkRunningNotRunning) == 0)
                                   {{ Form::select('project_status', $project_status, $project_status->manager ?? NULL, ['class' => 'input']) }}
                            @endif
                            @else
                                <?php
                                $project_status = ['' => 'Select a status', 'Active' => 'Active'];
                          		echo Form::hidden('status_key_type', 'New', ['required']);
                                ?>
                                {{ Form::select('project_status', $project_status, $project_status->manager ?? NULL, ['class' => 'input']) }}
                            @endif
                        </div>
                    </div>
                    <div class="field is-grouped">
                        <div class="control">
                            @if(empty($checkRunningNotRunning) || $project->type == 'Recurring')
                                <button class="button is-success is-small">Save Changes</button>
                          	@elseif(count($checkRunningNotRunning) == 0)
                          		<button class="button is-success is-small">Save Changes</button>
                            @else
                                <a class="button is-success is-small" href="{{route('projects.site', $project->id)}}" target="_blank">Check here</a>
                            @endif
                        </div>
                    </div>

                    {{ Form::close() }}

                    <div style="margin-top: 20px;">
                        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                            <tbody>
                            <tr>
                                <th>Row ID</th>
                                <th>Project ID</th>
                                <th>Project Status Update Date</th>
                                <th>Project Status</th>
                            </tr>

                            @php
                                $project_ranges = \Tritiyo\Project\Models\ProjectRange::where('project_id', $project->id)->orderBy('id', 'desc')->get();
                            @endphp
                            @foreach($project_ranges as $status)
                                <tr>
                                    <td>{{ $status->id }}</td>
                                    <td>{{ $status->project_id }}</td>
                                    <td>{{ $status->status_update_date }}</td>
                                    <td>{{ $status->project_status }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
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
