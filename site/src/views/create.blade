@extends('layouts.app')
@section('title')
    Create Site
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
            'spTitle' => 'Create Site',
            'spSubTitle' => 'create a single site',
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
            {{ Form::open(array('url' => route('sites.store'), 'method' => 'post', 'value' => 'PATCH', 'id' => 'add_route', 'files' => true, 'autocomplete' => 'off')) }}
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
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('location', 'Location', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('location', $site->location ?? NULL, ['class' => 'input is-small', 'placeholder' => 'Enter location...']) }}
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('site_code', 'Site Code', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('site_code', $site->site_code ?? NULL, ['class' => 'input is-small', 'placeholder' => 'Enter Site Code...']) }}
                        </div>
                    </div>
                </div>
            </div>
            {{--            <div class="columns">--}}
            {{--                 <div class="column is-3">--}}
            {{--                    <div class="field">--}}
            {{--                        {{ Form::label('budget', 'Budget', array('class' => 'label')) }}--}}
            {{--                        <div class="control">--}}
            {{ Form::hidden('budget', $site->budget ?? NULL, ['class' => 'input', 'placeholder' => 'Enter budget...']) }}
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
