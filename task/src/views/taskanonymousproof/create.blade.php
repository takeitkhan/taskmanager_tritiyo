@extends('layouts.app')
@section('title')
    Include Anonymous Proof information of task
@endsection

<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Vehicle',
            'spSubTitle' => 'Add Anonymous Proof of task',
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

        <?php
        $disabled = 'disabled="disabled"';
        $task_id = request()->get('task_id');
        ?>

        @include('task::layouts.tab')


        <div class="customContainer">

            {{ Form::open(array('url' => route('tasks.update', $task_id), 'method' => 'PUT', 'value' => 'PATCH', 'id' => 'add_route', 'files' => true, 'autocomplete' => 'off')) }}

            <div class="columns">
                <div class="column is-9">
                    <div class="field">
                        <label for="task_details" class="label">Task Anonymous Proof Details</label>
                        <div class="control">
                            @if(auth()->user()->isManager(auth()->user()->id))
                                @php
                                    $disabled = '';
                                @endphp
                            @endif
                            <input type="hidden" name="anonymousproof_details" value="anonymousproof_details">
                            <textarea  class="textarea" rows="5" placeholder="Enter Task Anonymous Proof Details..."
                                      name="anonymous_proof_details" cols="50"
                                      id="anonymous_proof_details" {{ $disabled }}>{{$task->anonymous_proof_details}}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            @if(auth()->user()->isManager(auth()->user()->id))
                <div class="columns">
                    <div class="column">
                        <div class="field is-grouped">
                            <div class="control">
                                <button class="button is-success is-small">Save Changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            @else

            @endif
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


@endsection

