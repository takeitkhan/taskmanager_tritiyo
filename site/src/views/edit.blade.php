@extends('layouts.app')
@section('title')
    Edit Site
@endsection
@if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
    @php
        $addUrl = route('sites.create');
    @endphp
@else
    @php
        $addUrl = '#';
    @endphp
@endif
<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Edit Site',
            'spSubTitle' => 'Edit a single site',
            'spShowTitleSet' => true
        ])

        @include('component.button_set', [
            'spShowButtonSet' => true,
            'spAddUrl' => null,
            'spAddUrl' => $addUrl,
            'spAllData' => route('sites.index'),
            'spSearchData' => route('sites.search'),
            'spTitle' => 'Sites',
        ])

        @include('component.filter_set', [
            'spShowFilterSet' => true,
            'spAddUrl' => route('sites.create'),
            'spAllData' => route('sites.index'),
            'spSearchData' => route('sites.search'),
            'spPlaceholder' => 'Search sites...',
            'spMessage' => $message = $message ?? NULl,
            'spStatus' => $status = $status ?? NULL
        ])
    </nav>
</section>
@section('column_left')
    <article class="panel is-primary">
        <p class="panel-tabs">
            <a class="is-active">Site Information</a>
        </p>


        <div class="customContainer">
            {{ Form::open(array('url' => route('sites.update', $site->id), 'method' => 'PUT', 'value' => 'PATCH', 'id' => 'add_route', 'files' => true, 'autocomplete' => 'off')) }}
            <div class="columns">
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('project_id', 'Project', array('class' => 'label')) }}
                        <div class="control">
                            <?php $projects = \Tritiyo\Project\Models\Project::pluck('name', 'id')->prepend('Select Project', ''); ?>
                            {{ Form::select('project_id', $projects, $site->project_id ?? NULL, ['class' => 'input is-small']) }}
                        </div>
                    </div>
                </div>
                <div class="column is-2">
                    <div class="field">
                        {{Form::label('location','Location',['class' => 'label'])}}
                        <div class="control">
                            <div class="select is-small">
                                <?php
                                $upazilas = \DB::table('upazilas')->get()->pluck('name', 'name');
                                ?>
                                {{ Form::select('location', $upazilas ?? NULL, $site->location ?? NULL, ['class' => 'input', 'required' => true]) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-2">
                    <div class="field">
                        {{ Form::label('site_code', 'Site Code', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('site_code', $site->site_code ?? NULL, ['class' => 'input is-small', 'placeholder' => 'Enter Site Code...']) }}
                        </div>
                    </div>
                </div>
                <div class="column is-2">
                    <div class="field">
                        {{ Form::label('task_limit', 'Limit Of Task', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('task_limit', $site->task_limit ?? NULL, ['class' => 'input is-small', 'placeholder' => 'Enter limit of task...']) }}
                        </div>
                    </div>
                </div>
                <div class="column is-2">
                    <div class="field">
                        {{ Form::label('completion_status', 'Completion Status', array('class' => 'label')) }}
                        <div class="control">
                            @php
                                $completion_statuses = ['' => '', 'Running' => 'Running', 'Rejected' => 'Rejected', 'Completed' => 'Completed', 'Pending' => 'Pending', 'Discard' => 'Discard'];
//dump($site->completion_status);
                            @endphp
                            {{ Form::select('completion_status', $completion_statuses, $site->completion_status ?? NULL, ['class' => 'input is-small', 'required' => true]) }}
                        </div>
                    </div>
                </div>

            </div>
            {{--            <div class="columns">--}}
            {{--                <div class="column is-3">--}}
            {{--                    <div class="field">--}}
            {{--                        {{ Form::label('budget', 'Budget', array('class' => 'label')) }}--}}
            {{--                        <div class="control">--}}
            {{ Form::hidden('budget', $site->budget ?? NULL, ['class' => 'input is-small', 'placeholder' => 'Enter budget...']) }}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--            </div>--}}
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
    {{--    <article class="is-primary">--}}
    {{--        <div class="box">--}}
    {{--            <h1 class="title is-5">Important Note</h1>--}}
    {{--            <p>--}}
    {{--                Please select project manager and budget properly--}}
    {{--            </p>--}}
    {{--        </div>--}}
    {{--    </article>--}}
@endsection
