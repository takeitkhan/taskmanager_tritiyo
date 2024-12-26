@extends('layouts.app')

@section('title')
    Single Task
@endsection

@php
    if (auth()->user()->isManager(auth()->user()->id)) {
        $addbtn = route('tasks.create');
        $alldatas = route('tasks.index');
    } else {
        $addbtn = '#';
        $alldatas = '#';
    }
@endphp

<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Single Task',
            'spSubTitle' => 'view a Task',
            'spShowTitleSet' => true
        ])

        @include('component.button_set', [
            'spShowButtonSet' => true,
            'spAddUrl' => null,
            'spAddUrl' => $addbtn,
            'spAllData' => route('tasks.index'),
            'spSearchData' => route('tasks.search'),
            'spTitle' => 'Tasks',
        ])

        @include('component.filter_set', [
            'spShowFilterSet' => true,
            'spAddUrl' => route('tasks.create'),
            'spAllData' => route('tasks.index'),
            'spSearchData' => route('tasks.search'),
            'spPlaceholder' => 'Search sites...',
            'spMessage' => $message = $message ?? NULl,
            'spStatus' => $status = $status ?? NULL
        ])
    </nav>
</section>

@section('column_left')
    {{--    <article class="panel is-primary">--}}
    {{--        <div class="customContainer">--}}

    @include('task::task_basic_data')

    @php
        $taskStatus = \Tritiyo\Task\Models\TaskStatus::where('task_id', $task->id)->where('action_performed_by', auth()->user()->id)
                      ->orderBy('id', 'desc')->first();
        $proofs = \Tritiyo\Task\Models\TaskProof::where('task_id', $task->id)->first();
    @endphp

    @if(auth()->user()->isManager(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
        @include('task::taskaction.task_proof_images')
        {{--        @include('task::taskaction.task_approver_accept_decline')--}}
    @else
        @if(empty($taskStatus) || $taskStatus->code == 'declined' && auth()->user()->isResource(auth()->user()->id))
            @include('task::taskaction.task_accept_decline')
        @elseif(empty($taskStatus) || $taskStatus->code == 'head_accepted')
            @include('task::taskaction.task_proof_form')
        @elseif(empty($taskStatus) || $taskStatus->code == 'proof_given')
            @include('task::taskaction.task_proof_images')
        @endif
    @endif


    @include('task::taskrequisitionbill.requistion_data')
@endsection

@section('column_right')
    <div class="card tile is-child">
        <header class="card-header">
            <p class="card-header-title">
                <span class="icon"><i class="fas fa-tasks default"></i></span>
                Status
            </p>
        </header>

        <div class="card-content">
            @php $taskStatus = \Tritiyo\Task\Models\TaskStatus::where('task_id', $task->id)->orderBy('id', 'DESC')->get() @endphp
            <?php //dump($taskStatus);?>
            @foreach($taskStatus as $task_status)
                @php
                    if($task_status->code == 'head_declined' || $task_status->code == 'approver_declined' || $task_status->code == 'cfo_declined' || $task_status->code == 'accountant_declined'){
                            $msgClass = 'danger';
                    } else {
                            $msgClass = 'success';
                    }
                @endphp
                <div class="task_status {{$msgClass}}">{{$task_status->message}}</div>
            @endforeach
        </div>
    </div>
@endsection
@section('cusjs')
    <style type="text/css">
        .table.is-fullwidth {
            width: 100%;
            font-size: 15px;
        }

        .task_status {
            padding: .30rem .50rem;
            margin-bottom: .30rem;
            border: 1px solid transparent;
            border-radius: .25rem;
            font-size: 11px;
        }

        .task_status.success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .task_status.danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
@endsection
