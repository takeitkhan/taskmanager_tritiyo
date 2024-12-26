@extends('layouts.app')

@section('title')
    Tasks
@endsection

@php
    if (auth()->user()->isManager(auth()->user()->id)) {
        $addbtn = route('tasks.create');
    } else {
        $addbtn = '#';
        $alldatas = '#';
    }
@endphp

<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Tasks',
            'spSubTitle' => 'all tasks here',
            'spShowTitleSet' => true
        ])

        @include('component.button_set', [
            'spShowButtonSet' => true,
            'spAddUrl' => null,
            'spAddUrl' => $addbtn,
            'spAllData' => route('tasks.index'),
            'spSearchData' => route('tasks.search'),
            'spTitle' => 'All Task',
        ])
         @if(auth()->user()->isManager(auth()->user()->id) || auth()->user()->isCFO(auth()->user()->id) || auth()->user()->isAccountant(auth()->user()->id) )
            @include('component.any_link', [
                'spShowButtonSet' => true,
                'spAddUrl' => null,
                'spAddUrl' => '#',
                'spCss' => 'is-warning',
                'spAllData' => route('tasks.index').'?bill=pending',
                'spTitle' => 'Pending Bills',
            ])
        @endif

        @include('component.filter_set', [
            'spShowFilterSet' => true,
            'spAddUrl' => route('tasks.create'),
            'spAllData' => route('tasks.index'),
            'spSearchData' => route('tasks.search'),
            'spPlaceholder' => 'Search tasks...',
            'spMessage' => $message = $message ?? NULl,
            'spStatus' => $status = $status ?? NULL
        ])
    </nav>
</section>
{{-- ||--}}

<?php
function userAccess($arg)
{
    return auth()->user()->$arg(auth()->user()->id);
}
?>
@section('column_left')
    @if(!empty($tasks))
        <div class="columns is-multiline">

            @if(auth()->user()->isResource(auth()->user()->id))
                @foreach($tasks->where('task_assigned_to_head', 'Yes') as $task)
                    @if($task->user_id == auth()->user()->id || $task->site_head == auth()->user()->id)
                        @include('task::tasklist.index_template')
                    @endif
                @endforeach

            @elseif(auth()->user()->isApprover(auth()->user()->id))

                @foreach($tasks->where('task_assigned_to_head', 'Yes') as $task)
                    @php
                        $proof_check = \Tritiyo\Task\Models\TaskStatus::where('code', 'proof_given')->where('task_id', $task->id)->first();
                        $checkRequisitionApprovedByCFO = \Tritiyo\Task\Models\TaskRequisitionBill::where('task_id', $task->id)->first();
                    @endphp
                    @if($proof_check != null && $proof_check->code)
                        @if(empty($checkRequisitionApprovedByCFO) || (!empty($checkRequisitionApprovedByCFO) && $checkRequisitionApprovedByCFO->requisition_approved_by_cfo == NULL))
                            @include('task::tasklist.index_template')
                        @endif
                    @endif
                @endforeach

            @elseif(auth()->user()->isManager(auth()->user()->id))
                @php
                     if(request()->get('bill') == 'pending'){
                        $tasks =  Tritiyo\Task\Models\TaskRequisitionBill::leftJoin('tasks', 'tasks.id', '=', 'tasks_requisition_bill.task_id')
                                    ->where('tasks_requisition_bill.requisition_approved_by_accountant', 'Yes')
                                    ->where('tasks_requisition_bill.bill_submitted_by_resource', 'Yes')
                                    ->where('tasks_requisition_bill.bill_approved_by_manager', Null)
                                    ->orderBy('tasks.id', 'Desc')
                                    ->paginate('48');
                    }
                @endphp
                @foreach($tasks->where('user_id', auth()->user()->id) as $task)
                    @include('task::tasklist.index_template')
                @endforeach

                {{--   Cfo--}}
            @elseif(auth()->user()->isCFO(auth()->user()->id))

                @php
                    if(request()->get('bill') == 'pending'){
                        $tasks =  Tritiyo\Task\Models\TaskRequisitionBill::leftJoin('tasks', 'tasks.id', '=', 'tasks_requisition_bill.task_id')
                                    ->where('tasks_requisition_bill.requisition_approved_by_accountant', 'Yes')
                                    ->where('tasks_requisition_bill.bill_submitted_by_resource', 'Yes')
                                    ->where('tasks_requisition_bill.bill_approved_by_manager', 'Yes')
                                    ->where('tasks_requisition_bill.bill_approved_by_CFO', Null)
                                    ->orderBy('tasks.id', 'Desc')
                                    ->paginate('48');
                    } else {
                        $tasks =  Tritiyo\Task\Models\TaskRequisitionBill::leftJoin('tasks', 'tasks.id', '=', 'tasks_requisition_bill.task_id')
                                    ->where('tasks_requisition_bill.requisition_submitted_by_manager', 'Yes')
                                    ->orderBy('tasks.id', 'Desc')
                                    ->paginate('48');
                    }

                @endphp

                @foreach($tasks as $task)
                    @include('task::tasklist.index_template')
                @endforeach

                <div class="pagination_wrap pagination is-centered">
                    {{$tasks->links('pagination::bootstrap-4')}}
                </div>

                {{--  Accountant          --}}
            @elseif(auth()->user()->isAccountant(auth()->user()->id))
                @php
                     if(request()->get('bill') == 'pending'){
                        $tasks =  Tritiyo\Task\Models\TaskRequisitionBill::leftJoin('tasks', 'tasks.id', '=', 'tasks_requisition_bill.task_id')
                                    ->where('tasks_requisition_bill.requisition_approved_by_accountant', 'Yes')
                                    ->where('tasks_requisition_bill.bill_submitted_by_resource', 'Yes')
                                    ->where('tasks_requisition_bill.bill_approved_by_manager', 'Yes')
                                    ->where('tasks_requisition_bill.bill_approved_by_cfo', 'Yes')
                                    ->where('tasks_requisition_bill.bill_approved_by_accountant', Null)
                                    ->orderBy('tasks.id', 'Desc')
                                    ->paginate('48');
                    } else {
                        $tasks =  Tritiyo\Task\Models\TaskRequisitionBill::leftJoin('tasks', 'tasks.id', '=', 'tasks_requisition_bill.task_id')
                                    
                                    ->where(function($query) {
                                    	$query->where('tasks_requisition_bill.requisition_approved_by_cfo', 'Yes');
                                        $query->where('tasks_requisition_bill.bill_submitted_by_resource', NULL);
                                        $query->where('tasks_requisition_bill.bill_approved_by_manager', NULL);
                                        $query->where('tasks_requisition_bill.bill_approved_by_cfo', NULL);
                                        $query->where('tasks_requisition_bill.bill_approved_by_accountant', NULL);
                                    })
                                    ->orWhere(function($query) {
                                    	$query->where('tasks_requisition_bill.bill_approved_by_cfo', 'Yes');
                                    })
                                    ->orderBy('tasks.id', 'desc')
                                    //->toSql();
                                    ->paginate('48');
                                    //dd($tasks);
                    }

                @endphp

                @foreach($tasks as $task)
                    @include('task::tasklist.index_template')
                @endforeach

                <div class="pagination_wrap pagination is-centered">
                    @if(Request::get('key'))
                        {{ $tasks->appends(['key' => Request::get('key')])->links('pagination::bootstrap-4') }}
                    @else
                        {{$tasks->links('pagination::bootstrap-4')}}
                    @endif
                </div>
                {{--   End         --}}

            @elseif(auth()->user()->isAdmin(auth()->user()->id))
                @foreach($tasks as $task)
                    @include('task::tasklist.index_template')
                @endforeach
            @endif


        </div>
    @endif
@endsection
