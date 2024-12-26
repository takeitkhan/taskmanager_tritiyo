@extends('layouts.app')
@section('title')
    User History
@endsection

@section('column_left')

    @php
        function getTotalForUser($userid, $getColumn, $checkColumn) {
            return $total_tasks = \Tritiyo\Task\Models\Task::
                        leftJoin('tasks_requisition_bill', 'tasks_requisition_bill.task_id', 'tasks.id')
                        ->select('tasks.*', 'tasks_requisition_bill.'. $getColumn .'')
                        ->where('tasks.site_head', $userid)
                        ->orWhere('tasks.user_id', $userid)
                        ->where('tasks_requisition_bill.'.$checkColumn.'', 'Yes')
                        ->orderBy('tasks.id', 'desc')
                        ->get();
        }
    @endphp
    <div class="columns is-vcentered pt-2">
        <div class="column is-10 mx-auto">
            <div class="card tile is-child xquick_view">
                <header class="card-header">
                    <p class="card-header-title">
                        <a href="{{route('hidtory.user', $user_id)}}" class="has-text-dark">
                            <span>
                                <span class="icon">
                                    <i class="fas fa-tasks default"></i>
                                </span>
                                User History
                            </span>
                        </a>
                        <a href="{{route('kpi.user', $user_id)}}" style="display: none">
                            <span class="ml-3">
                                 <span class="icon">
                                    <i class="far fa-chart-bar"></i>
                                </span>
                                Kpi
                            </span>
                        </a>
                    </p>
                </header>
              
                <div class="card-content">
                    <div class="card-data">
                        @php
                            $user = \App\Models\User::where('id', $user_id)->first();
                        @endphp
                        <div class="columns">
                            <div class="column is-8">
                                <br/>
                                <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                    <tr>
                                        <td colspan="4">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <strong>Personal Information</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Name</strong></td>
                                        <td>{{ $user->name }}</td>
                                        <td><strong>Email</strong></td>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Employee No</strong></td>
                                        <td>{{ $user->employee_no }}</td>
                                        <td><strong>Username</strong></td>
                                        <td>{{ $user->username }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Role</strong></td>
                                        <td>{{ \App\Models\Role::where('id', $user->role)->first()->name }}</td>
                                        <td><strong>Birthday</strong></td>
                                        <td>{{ $user->birthday }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Gender</strong></td>
                                        <td>{{ $user->gender }}</td>
                                        <td><strong>Marital Status</strong></td>
                                        <td>{{ $user->marital_status }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone No</strong></td>
                                        <td>{{ $user->phone }}</td>
                                        <td><strong>Phone No (Alternative)</strong></td>
                                        <td>{{ $user->emergency_phone }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Employee Status</strong></td>
                                        <td>{{ $user->employee_status ?? NULL }}</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Employee Status Note</strong></td>
                                        <td>{{ $user->employee_status_reason ?? NULL }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="column is-4">
                                <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                    <tr>
                                        <td>
                                            @php
                                                $requisition_total = [];
                                            @endphp
                                            @foreach(getTotalForUser($user_id, 'requisition_edited_by_accountant', 'requisition_approved_by_accountant') as $task)
                                                @php
                                                    //dd($task);
                                                    // $rm = new \Tritiyo\Task\Helpers\SiteHeadTotal('requisition_edited_by_accountant', $task->id);
                                                    // $requisition_total[] = $rm->getTotal();
                                                    $requisition_total []= \Tritiyo\Task\Helpers\SiteHeadTotal::totalAmountRequisionBill('reba_amount', $task->id)
                                                @endphp
                                                
                                            @endforeach

                                            <div class="notification is-warning has-text-centered">
                                                Requisition Approved <br/>
                                                <h1 class="title">
                                                    BDT. {{ array_sum($requisition_total) }}
                                                </h1>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            @php
                                                $bill_submitted = [];
                                                
                                            @endphp
                                            @foreach(getTotalForUser($user_id, 'bill_prepared_by_resource', 'bill_submitted_by_resource') as $task)
                                                @php
                                                    //dd($task);
                                                    $rm = new \Tritiyo\Task\Helpers\SiteHeadTotal('bill_prepared_by_resource', $task->id);
                                                    //$bill_submitted[] = $rm->getTotal();
                                                    $bill_submitted[] = \Tritiyo\Task\Helpers\SiteHeadTotal::totalAmountRequisionBill('bpbr_amount', $task->id)
                                                @endphp
                                                
                                            @endforeach
                                            <div class="notification is-link has-text-centered">
                                                Bill Submitted By Resource
                                                <h1 class="title">
                                                    BDT. {{ array_sum($bill_submitted) }}
                                                </h1>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            @php
                                                $bill_approved = [];
                                            @endphp
                                            @foreach(getTotalForUser($user_id, 'bill_edited_by_accountant', 'bill_approved_by_accountant') as $task)
                                                @php
                                                    //dd($task);
                                                    $rm = new \Tritiyo\Task\Helpers\SiteHeadTotal('bill_edited_by_accountant', $task->id);
                                                   // $bill_approved[] = $rm->getTotal();
                                                    $bill_approved[] = \Tritiyo\Task\Helpers\SiteHeadTotal::totalAmountRequisionBill('beba_amount', $task->id);
                                                @endphp
                                            @endforeach
                                            <div class="notification is-success has-text-centered">
                                                Bill Approved
                                                <h1 class="title">
                                                    BDT. {{ array_sum($bill_approved) }}
                                                </h1>
                                            </div>
                                        </td>
                                    </tr>

                                </table>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <div class="columns">
                            <div class="column is-12">

                                @if( auth()->user()->isCFO(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id) || auth()->user()->isAdmin(auth()->user()->id)  || auth()->user()->isAccountant(auth()->user()->id) )

                                    <form method="post" action="{{ route('get_user_history')}}">
                                        <div class="columns mb-0">
                                            <div class="column is-2">

                                            </div>
                                            <div class="column is-8 is-right">
                                                <div class="columns">
                                                  	<div class="column is-3">
                                                      <a href="{{route('tasks.index')}}?bill=pending&user={{$user_id}}" target="_blank" class="button is-info is-small">Bill List </a>
                                                  	</div>
                                                    <div class="column is-3">
                                                        <a href="{{ route('download_excel_user') }}?id={{ $user_id }}&daterange={{ !empty($task_for_date) ? $task_for_date :  date('Y-m-d', strtotime(date('Y-m-d'). ' - 30 days')) . ' - ' . date('Y-m-d') }}"
                                                           class="button is-primary is-small">
                                                            Download as excel
                                                        </a>
                                                    </div>
                                                    @csrf
                                                    <?php
                                                    $resources = \App\Models\User::where('role', 2)->pluck('name', 'id')->prepend('Select a resource', '');
                                                    //dd($resources);
                                                    ?>

                                                    <div class="column is-3">
                                                        <input type="hidden" name="current_user_id" value="{{$user_id}}">
                                                        <select name="user_id" id="sitehead_select" >
                                                            @foreach($resources as $id => $name)
                                                                <?php
                                                                if ($user_id == $id) {
                                                                    $selected = 'selected="selected"';
                                                                } else {
                                                                    $selected = '';
                                                                }
                                                                ?>
                                                                <option></option>
                                                                <option value="{{ $id }}" {{ $selected }}>{{ $name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="column is-3">
                                                        <input class="input is-small" type="text" name="daterange"
                                                               id="textboxID" value="{{$task_for_date ?? null}}"/>
                                                    </div>
                                                    <div class="column is-2">
                                                        <input name="search" type="submit"
                                                               class="button is-small is-primary has-background-primary-dark"
                                                               value="Search"/>
                                                    </div>
                                                </div>
                                            </div>

                                            <style>
                                                .button.is-small {
                                                    width: 100% !important;
                                                }
                                            </style>

                                        </div>
                                    </form>

                                @else
                                    <?php //dd($user_id);?>
                                    <form method="post" action="{{route('hidtory.user', $user_id)}}">
                                        <div class="columns mb-0">
                                            <div class="column is-2">
												
                                            </div>
                                            <div class="column is-8 is-right">
                                                <div class="columns">
                                                  	<div class="column is-3">
                                                      <a href="{{route('tasks.index')}}?bill=pending&user={{$user_id}}" target="_blank" class="button is-info is-small">Bill List </a>
                                                  	</div>
                                                    <div class="column is-3">
                                                        <a href="{{ route('download_excel_user') }}?id={{ $user_id }}&daterange={{ !empty($task_for_date) ? $task_for_date :  date('Y-m-d', strtotime(date('Y-m-d'). ' - 30 days')) . ' - ' . date('Y-m-d') }}"
                                                           class="button is-primary is-small">
                                                            Download as excel
                                                        </a>
                                                    </div>
                                                    @csrf
                                                    <div class="column is-5">
                                                        <input class="input is-small" type="text" name="daterange"
                                                               id="textboxID" value="{{$task_for_date ?? null}}"/>
                                                    </div>
                                                    <div class="column is-2">
                                                        <input name="search" type="submit"
                                                               class="button is-small is-primary has-background-primary-dark"
                                                               value="Search"/>
                                                    </div>
                                                </div>
                                            </div>

                                            <style>
                                                .button.is-small {
                                                    width: 100% !important;
                                                }
                                            </style>

                                        </div>
                                    </form>
                                @endif
                                <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth"
                                       style="margin-top: 10px;">

                                    @php
                                        //dump(request()->all());
                                        if(!empty($task_for_date)){
                                            //dump($task_for_date);
                                            $date = explode(' - ', $task_for_date);
                                            //dump($date);
                                            //$tasks = Tritiyo\Task\Models\Task::where('site_head', $user_id)->whereBetween('task_for', [$date[0], $date[1]])->paginate('50');
                                            $tasks = Tritiyo\Task\Models\Task::leftjoin('tasks_site', 'tasks_site.task_id', 'tasks.id')
                                                                                ->select('tasks.*', 'tasks_site.site_id', 'tasks_site.resource_id')
                                                                                 ->where(function($q) use ($user_id){
                                                                                    $q->where('tasks.user_id', $user_id)
                                                                                    ->orWhere('tasks.site_head', $user_id)
                                                                                    ->orWhere('tasks_site.resource_id', $user_id);
                                                                                })
                                                                                ->whereBetween('tasks.task_for', [$date[0], $date[1]])
                                                                                ->orderBy('tasks.id', 'desc')
                                                                                ->groupBy('tasks.id')
                                                                                ->paginate('30');
                                            //dump($tasks);
                                        } else {
                                             $tasks = Tritiyo\Task\Models\Task::leftjoin('tasks_site', 'tasks_site.task_id', 'tasks.id')
                                                                                ->select('tasks.*', 'tasks_site.site_id', 'tasks_site.resource_id')
                                                                                ->where(function($q) use ($user_id){
                                                                                    $q->where('tasks.user_id', $user_id)
                                                                                    ->orWhere('tasks.site_head', $user_id)
                                                                                    ->orWhere('tasks_site.resource_id', $user_id);
                                                                                })
                                                                                ->orderBy('tasks.id', 'desc')
                                                                                ->groupBy('tasks.id')
                                                                                //->groupBy('tasks_site.resource_id')
                                                                                ->paginate('30');

                                        }
                                        //dd(count($tasks));
                                        $checkManager = $tasks->contains('user_id', $user_id);
                                        $checkSitehead = $tasks->contains('site_head', $user_id);
                                        $checktaskresource = $tasks->contains('resource_id', $user_id);
                                        //dump($checktaskresource);
                                    @endphp
                                    <tr>
                                        <th>Task Name</th>
                                        <th>Task Status</th>
                                        <th>Task For</th>
                                        <th>Task Type</th>
                                        <th>Play Role</th>
                                        <th>Site Code</th>
                                        <th>Site Head</th>
                                        <th>Project Name</th>
                                        <th class="project_manager">Project Manager</th>
                                        <th>Vehicle Rent</th>
                                        @if(auth()->user()->isResource(auth()->user()->id))
                                        @else
                                            <th>Requsition Approved</th>
                                        @endif
                                        <th>Bill Submit</th>
                                        <th>Bill Approved</th>
                                    </tr>
                                    @php
                                        $totalRequisitionApproved = [];
                                        $totalRequisitionSubmit = [];
                                        $totalBillSubmit = [];
                                        $totalBillApproved = [];
                                    @endphp
                                    @foreach($tasks as $task)
                                        @php
                                            $statusCheck = \Tritiyo\Task\Models\TaskStatus::select('code', 'message')->where('task_id', $task->id)->latest()->first();
                                            if(strpos($statusCheck->code, 'decline') == true){
                                                $decline = '#ffd8e5';
                                            } else {
                                                $decline = '';
                                            }
                                        @endphp
                                        <tr style="background: {{$decline}}">
                                            <td>
                                                <a href="{{route('tasks.show', $task->id)}}" target="__blank">
                                                    {{$task->task_name}}
                                                </a>
                                            </td>
                                            <td>{{$statusCheck->message}}</td>
                                            <td>{{$task->task_for}}</td>
                                            <td>{{$task->task_type}}</td>
                                            <td title="Play role">
                                                <?php
                                                $resources = \Tritiyo\Task\Models\TaskSite::where('task_id', $task->id)
                                                    ->select('resource_id')
                                                    ->groupBy('resource_id')
                                                    ->get();
                                                //dump($resources);
                                                //dump(in_array($user_id, $resources));
                                                $issetResource = $resources->contains('resource_id', $user_id);
                                                ?>
                                                @if($user_id == $task->user_id)
                                                    Project Manager
                                                @elseif($user_id == $task->site_head)
                                                    Site Head
                                                @elseif($issetResource == true)
                                                    As a Resource
                                                @endif
                                            </td>
                                            <td title="Site Code">
                                                @php
                                                    $sites = \Tritiyo\Task\Models\TaskSite::leftjoin('sites', 'sites.id', 'tasks_site.site_id')->select('sites.id', 'sites.site_code')
                                                            ->where('task_id', $task->id)
                                                            ->groupBy('sites.site_code')
                                                            ->get()->toArray();

                                                    foreach($sites as $site) {
                                                        echo '<a href="'. route('sites.show', $site['id']) .'" target="_blank">'. $site['site_code'] .'</a><br/>';
                                                    }
                                                @endphp
                                            </td>
                                            <td title="Site head">
                                                <?php //dd($task->site_head);?>
                                                <a href="{{route('hidtory.user', $task->site_head ?? '')}}"
                                                   target="_blank">
                                                    {{App\Models\User::where('id', $task->site_head)->first()->name ?? NULL}}
                                                </a>
                                            </td>
                                            <td>
                                                @php $project = Tritiyo\Project\Models\Project::where('id', $task->project_id)->first(); @endphp
                                                <a target="__blank"
                                                   href="{{route('projects.show', $project->id)}}">{{$project->name}}</a>
                                            </td>
                                            @if($user_id == $task->user_id)
                                                <td title="Project Manager">
                                                    <a href="{{route('hidtory.user', $task->user_id)}}" target="_blank">
                                                        {{App\Models\User::where('id', $task->user_id)->first()->name}}
                                                    </a>
                                                </td>
                                            @else
                                                <td title="Project Manager">
                                                    <a href="{{route('hidtory.user', $task->user_id)}}" target="_blank">
                                                        {{App\Models\User::where('id', $task->user_id)->first()->name}}
                                                    </a>
                                                </td>
                                            @endif
                                            @if($user_id == $task->user_id)
                                                <td>
                                                    <?php
                                                    $vehicle = \Tritiyo\Task\Models\TaskVehicle::where('task_id', $task->id)
                                                        ->groupBy('vehicle_id')
                                                        ->get()->toArray();
                                                    echo implode('<br> ', array_column($vehicle, 'vehicle_rent'));
                                                    ?>
                                                </td>
                                            @else
                                                <td></td>
                                            @endif
                                            @if(auth()->user()->isResource(auth()->user()->id))

                                            @else
                                                <td>
                                                    {{-- @php $totalRequisitionApproved []= (new \Tritiyo\Task\Helpers\SiteHeadTotal('requisition_edited_by_accountant', $task->id, false))->getTotal()  @endphp --}}
                                                    {{-- {{ (new \Tritiyo\Task\Helpers\SiteHeadTotal('requisition_edited_by_accountant', $task->id, true))->getTotal() }} --}}
                                                   BDT {{$totalRequisitionApproved [] = \Tritiyo\Task\Helpers\SiteHeadTotal::totalAmountRequisionBill('reba_amount', $task->id,'requisition_approved_by_accountant')}}
                                                </td>
                                            @endif
                                                <td>
                                                    <?php
                                                    $resources = \Tritiyo\Task\Models\TaskSite::where('task_id', $task->id)
                                                        ->select('resource_id')
                                                        ->groupBy('resource_id')
                                                        ->get();
                                                    //dump($resources);
                                                    //dump(in_array($user_id, $resources));
                                                    $issetResource = $resources->contains('resource_id', $user_id);
                                                    ?>
                                                    @if($user_id == $task->user_id || $user_id == $task->site_head)

                                                        {{-- @php $totalBillSubmit []= (new \Tritiyo\Task\Helpers\SiteHeadTotal('bill_prepared_by_resource', $task->id, false))->getTotal(); @endphp
                                                        {{ (new \Tritiyo\Task\Helpers\SiteHeadTotal('bill_prepared_by_resource', $task->id, true))->getTotal() }} --}}

                                                        BDT {{$totalBillSubmit [] = \Tritiyo\Task\Helpers\SiteHeadTotal::totalAmountRequisionBill('bpbr_amount', $task->id,'bill_submitted_by_resource')}}
                                                    {{-- @elseif($user_id == $task->site_head)
                                                        @php $totalBillSubmit []= (new \Tritiyo\Task\Helpers\SiteHeadTotal('bill_prepared_by_resource', $task->id, false))->getTotal(); @endphp
                                                        {{ (new \Tritiyo\Task\Helpers\SiteHeadTotal('bill_prepared_by_resource', $task->id, true))->getTotal() }} --}}
                                                    @elseif($issetResource == true)

                                                    @endif
                                                </td>
                                                <td>

                                                    {{-- @php $totalBillApproved []=  (new \Tritiyo\Task\Helpers\SiteHeadTotal('bill_edited_by_accountant', $task->id, false))->getTotal() @endphp
                                                    {{ (new \Tritiyo\Task\Helpers\SiteHeadTotal('bill_edited_by_accountant', $task->id, true))->getTotal() }} --}}
                                                    BDT {{$totalBillApproved [] = \Tritiyo\Task\Helpers\SiteHeadTotal::totalAmountRequisionBill('beba_amount', $task->id, 'bill_approved_by_accountant')}}
                                                </td>



                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="9"></td>
                                        <td></td>
                                        @if(auth()->user()->isResource(auth()->user()->id))
                                        @else
                                        <td>Total: {{array_sum($totalRequisitionApproved)}}</td>
                                        @endif
                                        <td>Total: {{array_sum($totalBillSubmit)}}</td>
                                        <td>Total: {{array_sum($totalBillApproved)}}</td>
                                    </tr>

                                </table>
                                <div class="pagination_wrap pagination is-centered">
                                    @if(!empty($task_for_date))
                                        {{$tasks->appends(['user_id' => $user_id, 'daterange' => $task_for_date])->links('pagination::bootstrap-4')}}
                                    @else
                                        {{$tasks->links('pagination::bootstrap-4')}}
                                    @endif
                                </div>
                                {{--                                @endif--}}


                            </div>
                        </div>

                    </div>
                </div>
                

            </div>
        </div>
    </div>


@endsection



@section('cusjs')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

    <script type="text/javascript">
        document.getElementById('textboxID').select();
    </script>

    <script>
        $(function () {
            $('input[name="daterange"]').daterangepicker({
                opens: 'left',
                locale: {
                    format: 'YYYY-MM-DD'
                }
            }, function (start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
        });
    </script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script>
        $('#sitehead_select').select2({
            placeholder: "Select Resource",
            allowClear: true
        });
    </script>

@endsection
