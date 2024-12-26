@extends('layouts.app')
@section('title')
    Create Project
@endsection
@if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
    @php
        $addUrl = route('projects.create');
    @endphp
@else
    @php
        $addUrl = '#';
    @endphp
@endif
<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Create Project',
            'spSubTitle' => 'create a single project',
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
            {{ Form::open(array('url' => route('projects.store'), 'method' => 'post', 'value' => 'PATCH', 'id' => 'add_route', 'files' => true, 'autocomplete' => 'off')) }}
            <div class="columns">
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('name', 'Project Name', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('name', $project->name ?? NULL, ['required', 'class' => 'input', 'placeholder' => 'Enter project name...']) }}
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('code', 'Project Code', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('code', $project->code ?? NULL, ['required', 'class' => 'input', 'placeholder' => 'Enter project code...']) }}
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('type', 'Project Type', array('class' => 'label')) }}
                        <div class="control">
                            <select class="input is-small" name="type" id="">
                                <option value="">Select a project type</option>
                                <option value="Recurring" >Recurring</option>
                                <option value="Not Recurring">Not Recurring</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>

            <div class="columns">
                <div class="column is-9">
                    <div class="field">
                        {{ Form::label('manager', 'Project Manager', array('class' => 'label')) }}
                        <div class="control">
                            <?php $managers = \App\Models\User::where('role', 3)->whereNotIn('employee_status', ['Left Job', 'Terminated'])->pluck('name', 'id')->prepend('Select manager', ''); ?>
                            {{ Form::select('manager', $managers, $project->manager ?? NULL, ['class' => 'input']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="columns">
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('customer', 'Project customer', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('customer', $project->customer ?? NULL, ['class' => 'input', 'placeholder' => 'Enter project customer...']) }}
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('vendor', 'Project vendor', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('vendor', $project->vendor ?? NULL, ['class' => 'input', 'placeholder' => 'Enter project vendor...']) }}
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('supplier', 'Project supplier', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('supplier', $project->supplier ?? NULL, ['required', 'class' => 'input', 'placeholder' => 'Enter project supplier...']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="columns">
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('address', 'Project address', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('address', $project->address ?? NULL, ['required', 'class' => 'input', 'placeholder' => 'Enter project address...']) }}
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('location', 'Project location', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('location', $project->location ?? NULL, ['class' => 'input', 'placeholder' => 'Enter project location...']) }}
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('office', 'Head Office', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('office', $project->office ?? NULL, ['class' => 'input', 'placeholder' => 'Enter project office...']) }}
                        </div>
                    </div>
                </div>

            </div>

            <div class="columns">
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('start', 'Project start', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::date('start', $project->start ?? NULL, ['class' => 'input', 'placeholder' => 'Enter project start...']) }}
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('end', 'Approximate project end', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::date('end', $project->end ?? NULL, ['class' => 'input', 'placeholder' => 'Enter project end...']) }}
                        </div>
                    </div>
                </div>
                <div class="column is-3" style="display:none;">
                    <div class="field">
                        {{ Form::label('budget', 'Project approximate budget', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('budget', $project->budget ?? NULL,[ 'class' => 'input', 'placeholder' => 'Enter project budget...']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="columns">
                <div class="column is-9">
                    <div class="field">
                        {{ Form::label('summary', 'Project summary', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('summary', $project->summary ?? NULL, ['required', 'class' => 'textarea', 'placeholder' => 'Enter project summary...']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="columns">
                <div class="column">
                    <div class="field is-grouped">
                        <div class="control">
                            <button class="button is-success is-small" type="submit">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>


    </article>



@endsection

@section('column_right')
    <article class="is-primary">
        <div class="box">
            <h1 class="title is-5">Important Note</h1>
            <p>
                The default password is stored in the database when the admin authority creates the user.
                <br/>
                Default password: <strong>bizradix@123</strong>
            </p>
            <br/>
            <p>
                After you provide the basic information, you create a list of users, now you will find the created user
                and
                update the information for your user.
            </p>
        </div>
    </article>
@endsection
