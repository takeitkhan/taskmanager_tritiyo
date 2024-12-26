@extends('layouts.app')
@section('title')
    Create Material
@endsection
@if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
    @php
        $addUrl = route('materials.create');
    @endphp
@else
    @php
        $addUrl = '#';
    @endphp
@endif
<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Create Material',
            'spSubTitle' => 'create a single material',
            'spShowTitleSet' => true
        ])

        @include('component.button_set', [
            'spShowButtonSet' => true,
            'spAddUrl' => null,
            'spAddUrl' => $addUrl,
            'spAllData' => route('materials.index'),
            'spSearchData' => route('materials.search'),
        ])

        @include('component.filter_set', [
            'spShowFilterSet' => true,
            'spAddUrl' => route('materials.create'),
            'spAllData' => route('materials.index'),
            'spSearchData' => route('materials.search'),
            'spPlaceholder' => 'Search materials...',
            'spMessage' => $message = $message ?? NULl,
            'spStatus' => $status = $status ?? NULL
        ])
    </nav>
</section>
@section('column_left')
    <article class="panel is-primary">
        <p class="panel-tabs">
            <a class="is-active">Material Information</a>
        </p>

        <div class="customContainer">
            {{ Form::open(array('url' => route('materials.store'), 'method' => 'post', 'value' => 'PATCH', 'id' => 'add_route', 'files' => true, 'autocomplete' => 'off')) }}
            <div class="columns">
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('name', 'Name', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('name', $material->name ?? NULL, ['class' => 'input', 'placeholder' => 'Enter material name...']) }}
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('unit', 'Material Unit', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('unit', $material->unit ?? NULL, ['class' => 'input', 'placeholder' => 'Enter Material Unit...']) }}
                        </div>
                    </div>
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
