@extends('layouts.app')
@section('title')
    User History
@endsection

@section('column_left')

    @php
        function getTotalForUser($userid, $getColumn, $checkColumn) {
            return $total_tasks = \Tritiyo\Task\Models\Task::where('site_head', $userid)
                ->leftJoin('tasks_requisition_bill', 'tasks_requisition_bill.task_id', 'tasks.id')
                ->select('tasks.*', 'tasks_requisition_bill.'. $getColumn .'')
                ->where('tasks_requisition_bill.'.$checkColumn.'', 'Yes')
                ->get();
        }
    @endphp

    <div class="columns is-vcentered  pt-2">
        <div class="column is-10 mx-auto">
            <div class="card tile is-child xquick_view">
                <header class="card-header">
                    <p class="card-header-title">
                    <span class="icon">
                        <i class="fas fa-tasks default"></i>
                    </span>
                        User History
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
                                        <td><strong>Basic Salary</strong></td>
                                        <td>{{ $user->basic_salary }}</td>
                                        <td><strong>Employee Status</strong></td>
                                        <td>{{ $user->employee_status ?? NULL }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Employee Status Note</strong></td>
                                        <td>{{ $user->employee_status_reason ?? NULL }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </table>
{{--                                @if(auth()->user()->isCFO(auth()->user()->id) || auth()->user()->isAdmin(auth()->user()->id) )--}}
                                    <form method="post" action="{{route('hidtory.user', $user_id)}}">
                                        <div class="columns mb-0">
                                            <div class="column is-6">

                                            </div>
                                            <div class="column is-2">
                                                <a href="{{ route('download_excel_user') }}?id={{ $user_id }}&daterange={{ !empty($task_for_date) ? $task_for_date :  date('Y-m-d', strtotime(date('Y-m-d'). ' - 30 days')) . ' - ' . date('Y-m-d') }}"
                                                   class="button is-primary is-small">
                                                    Download as excel
                                                </a>
                                            </div>

                                            @csrf
                                            <div class="column is-3">
                                                <input class="input is-small" type="text" name="daterange" id="textboxID"  value="{{$task_for_date ?? null}}" />
                                            </div>
                                            <div class="column is-1">
                                                <input name="search" type="submit" class="button is-small is-primary has-background-primary-dark" value="Search"/>
                                            </div>
                                        </div>
                                    </form>
                                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">

                                        @php
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
                                                                                    ->groupBy('tasks.id')
                                                                                    ->paginate('50');
                                                //dump($tasks);
                                            } else {
                                                 $tasks = Tritiyo\Task\Models\Task::leftjoin('tasks_site', 'tasks_site.task_id', 'tasks.id')
                                                                                    ->select('tasks.*', 'tasks_site.site_id', 'tasks_site.resource_id')
                                                                                    ->where(function($q) use ($user_id){
                                                                                        $q->where('tasks.user_id', $user_id)
                                                                                        ->orWhere('tasks.site_head', $user_id)
                                                                                        ->orWhere('tasks_site.resource_id', $user_id);
                                                                                    })
                                                                                    ->groupBy('tasks.id')
                                                                                    //->groupBy('tasks_site.resource_id')
                                                                                    ->paginate('50');

                                            }
                                            //dd($tasks);
                                            $checkManager = $tasks->contains('user_id', $user_id);
                                            $checkSitehead = $tasks->contains('site_head', $user_id);
                                            $checktaskresource = $tasks->contains('resource_id', $user_id);
                                            //dump($checktaskresource);
                                        @endphp
                                        <tr>
                                            <th>Task Name</th>
                                            <th>Task For</th>
                                            <th>Task Type</th>
                                            <th>Play Role</th>
                                            <th>Site Code</th>

                                            <th>
{{--                                                @if($checkSitehead == true)--}}
{{--                                                @else--}}
                                                Site Head
{{--                                                @endif--}}
                                            </th>


                                            <th>Project Name</th>
                                            <th class="project_manager">
{{--                                                @if($checkManager == true)--}}
{{--                                                @else--}}
                                                    Project Manager
{{--                                                @endif--}}
                                            </th>
{{--                                            @if($checkManager == true)--}}
                                                <th>Vehicle Rent</th>
{{--                                            @endif--}}

                                            <th>
{{--                                                @if($checkSitehead == true)--}}
                                                Requsition Approved
{{--                                                @endif--}}
                                            </th>
                                            <th>
{{--                                                @if($checkSitehead == true)--}}
                                                Bill Submit
{{--                                                @endif--}}
                                            </th>
                                            <th>
{{--                                                @if($checkSitehead == true)--}}
                                                    Bill Approved
{{--                                                @endif--}}
                                            </th>

                                        </tr>

                                        @foreach($tasks as $task)
                                            <tr>
                                                <td>
                                                    <a href="{{route('tasks.show', $task->id)}}" target="__blank">
                                                        {{$task->task_name}}
                                                    </a>
                                                </td>
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
                                                        $sites = \Tritiyo\Task\Models\TaskSite::leftjoin('sites', 'sites.id', 'tasks_site.site_id')
                                                                                                ->select('sites.site_code')
                                                                                                ->where('task_id', $task->id)
                                                                                                ->groupBy('sites.site_code')
                                                                                                ->get()->toArray();
                                                        echo implode('<br>', array_column($sites, 'site_code'));
                                                    @endphp
                                                </td>
                                                @if($user_id == $task->site_head)
                                                    <td></td>
                                                @else
                                                    <td title="Site head">
                                                        <a href="{{route('hidtory.user', $task->site_head)}}" target="_blank">
                                                            {{App\Models\User::where('id', $task->site_head)->first()->name}}
                                                        </a>
                                                    </td>
                                                @endif
                                                <td>
                                                    @php $project = Tritiyo\Project\Models\Project::where('id', $task->project_id)->first(); @endphp
                                                    <a target="__blank" href="{{route('projects.show', $project->id)}}">{{$project->name}}</a>
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
                                                    @php
                                                        $vehicle = \Tritiyo\Task\Models\TaskVehicle::where('task_id', $task->id)
                                                                                        ->groupBy('vehicle_id')
                                                                                        ->get()->toArray();
                                                        //dump($vehicle);
                                                        echo implode('<br> ', array_column($vehicle, 'vehicle_rent'));
                                                    @endphp
                                                </td>
                                                @else
                                                    <td>
                                                        
                                                    </td>
                                                @endif

                                                @if($user_id == $task->site_head)
                                                    <td>
                                                         {{ (new \Tritiyo\Task\Helpers\SiteHeadTotal('requisition_edited_by_accountant', $task->id, true))->getTotal() }}
                                                    </td>
                                                    <td>
                                                        {{ (new \Tritiyo\Task\Helpers\SiteHeadTotal('bill_prepared_by_resource', $task->id, true))->getTotal() }}
                                                    </td>
                                                    <td>
                                                        {{ (new \Tritiyo\Task\Helpers\SiteHeadTotal('bill_edited_by_accountant', $task->id, true))->getTotal() }}
                                                    </td>
                                                
                                                @else
                                                    <td>
                                                         {{ (new \Tritiyo\Task\Helpers\SiteHeadTotal('requisition_edited_by_accountant', $task->id, true))->getTotal() }}
                                                    </td>
                                                    <td>
                                                        {{ (new \Tritiyo\Task\Helpers\SiteHeadTotal('bill_prepared_by_resource', $task->id, true))->getTotal() }}
                                                    </td>
                                                    <td>
                                                        {{ (new \Tritiyo\Task\Helpers\SiteHeadTotal('bill_edited_by_accountant', $task->id, true))->getTotal() }}
                                                    </td>
                                                @endif


                                            </tr>
                                        @endforeach
                                    </table>
                                    <div class="pagination_wrap pagination is-centered">
                                        {{$tasks->links('pagination::bootstrap-4')}}
                                    </div>
{{--                                @endif--}}
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
                                                    $rm = new \Tritiyo\Task\Helpers\SiteHeadTotal('requisition_edited_by_accountant', $task->id);
                                                    $requisition_total[] = $rm->getTotal();
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
                                                    $bill_submitted[] = $rm->getTotal();
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
                                                    $bill_approved[] = $rm->getTotal();
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
                    </div>
                </div>


            </div>
        </div>
    </div>


@endsection

@section('cusjs')

@section('cusjs')
    <script type="text/javascript"  src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <script type="text/javascript">
        document.getElementById('textboxID').select();
    </script>

    <script>
        $(function() {
            $('input[name="daterange"]').daterangepicker({
                opens: 'left',
                locale: {
                    format: 'YYYY-MM-DD'
                }
            }, function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
        });
    </script>

@endsection
