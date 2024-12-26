@extends('layouts.app')
@section('title')
    User KPI
@endsection

@section('column_left')

    @php
        function getTotalForUser($userid, $getColumn, $checkColumn) {
            return $total_tasks = \Tritiyo\Task\Models\Task::where('site_head', $userid)->orWhere('user_id', $userid)
                ->leftJoin('tasks_requisition_bill', 'tasks_requisition_bill.task_id', 'tasks.id')
                ->select('tasks.*', 'tasks_requisition_bill.'. $getColumn .'')
                ->where('tasks_requisition_bill.'.$checkColumn.'', 'Yes')
                ->get();
        }
    @endphp

    <div class="columns is-vcentered pt-2">
        <div class="column is-10 mx-auto">
            <div class="card tile is-child xquick_view">
                <header class="card-header">
                    <p class="card-header-title">
                        <a href="{{route('hidtory.user', $user_id)}}">
                            <span>
                                <span class="icon">
                                    <i class="fas fa-tasks default"></i>
                                </span>
                                User History
                            </span>
                        </a>
                        <a href="{{route('kpi.user', $user_id)}}" class="has-text-dark">
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
                            <div class="column is-7">
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
                            <div class="column is-5">
                                <canvas id="myChart" style="width:100%;max-width:600px"></canvas>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <form action="{{route('kpi.user', $user_id)}}" method="GET">
                            <div class="columns">
                                <div class="column is-2">
                                    <div class="field">
                                        <label for="">Select Range</label>
                                        <select name="target_range" id="" class="input is-small">
                                            @php
                                                $targetRangeOption = [
                                                        'january_to_march' => 'January To March',
                                                        'april_to_june' => 'April To June',
                                                        'july_to_september' => 'July To September',
                                                        'october_to_december' => 'October To December',
                                                ]
                                            @endphp
                                            @foreach($targetRangeOption as $key => $data)
                                                <option value="{{$key}}" {{request()->get('target_range') == $key ? 'selected' : ''}}>{{$data}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="column is-2">
                                    <div class="field">
                                        <label for="">Select Year</label>
                                        <select name="year" id="" class="input is-small">
                                            @php
                                                $targetYearOption = [
                                                        '2021' => '2021',
                                                        '2022' => '2022',
                                                        '2023' => '2023',
                                                ]
                                            @endphp
                                            @foreach($targetYearOption as $key => $data)
                                                <option value="{{$key}}" {{request()->get('year') == $key ? 'selected' : ''}}>{{$data}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="column is-1">
                                    <br>
                                    <button type="submit"
                                            class="button is-small is-primary has-background-primary-dark">Get Data
                                    </button>
                                </div>
                            </div>
                        </form>
                        <br/>
                        <br/>
                        @if(request()->get('target_range') && request()->get('year'))
                        <div class="columns">
                            <div class="column is-12">

                                @php
                                    $requestTargetRange = request()->get('target_range');
                                    $requestYear = request()->get('year');
                                    $getProjects = \Tritiyo\Project\Models\Project::where('manager', $user_id)->get();
                                @endphp

                                <div>
                                    <h3 class="title is-3">KPI Range ({{$requestTargetRange . '-'. $requestYear}})</h3>

                                    @foreach($getProjects as $project)

                                        <table  style="margin-bottom: 30px;" class="table is-bordered is-fullwidth">
                                        <tbody>
                                        <tr>
                                            <th colspan="6"
                                                style="font-weight: 800 !important; font-size: 18px; background: #6c757d;">
                                                {{ $project->name }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th width="10%">&nbsp;</th>
                                            <th>Project Costing</th>
                                            <th>Task Limit</th>
                                            <th>Site Completion</th>
                                            <th>Invoice Submission</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                        @php
                                            $targetProjectCosting = \Tritiyo\Project\Helpers\ProjectKpiHelper::getTargetKey($requestTargetRange, $requestYear, $project->id, 'projcet_costing');
                                            $targetTaskLimit = \Tritiyo\Project\Helpers\ProjectKpiHelper::getTargetKey($requestTargetRange, $requestYear, $project->id, 'task_limit');
                                            $targetSiteCompletion = \Tritiyo\Project\Helpers\ProjectKpiHelper::getTargetKey($requestTargetRange, $requestYear, $project->id, 'site_completion');
                                            $targetInvoiceSubmission = \Tritiyo\Project\Helpers\ProjectKpiHelper::getTargetKey($requestTargetRange, $requestYear, $project->id, 'invoice_submission');
                                        @endphp
                                        <tr>
                                            <th>Target</th>
                                            <td>{{ $targetProjectCosting->meta_value ?? null}}</td>
                                            <td>{{ $targetTaskLimit->meta_value ?? null}}</td>
                                            <td>{{ $targetSiteCompletion->meta_value ?? null}}</td>
                                            <td>{{ $targetInvoiceSubmission->meta_value ?? null}}</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <th>Performance</th>
                                            <td>
                                                @if(!empty($targetProjectCosting->target_range_date))
                                                    @php
                                                        $explodeRangeDate = explode(' | ', $targetProjectCosting->target_range_date);
                                                    @endphp
                                                    <?php
                                                        $dateForm = $explodeRangeDate[0];
                                                        $dateTo = $explodeRangeDate[1];
                                                        $getProjectCost = DB::table('ttrb')->where('project_id', $project->id)->whereBetween('created_at', [$dateForm, $dateTo])->get()->sum('reba_amount');
                                                        echo $getProjectCost;
                                                    ?>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($targetTaskLimit->target_range_date))
                                                <?php
                                                    $getTask = DB::table('ttrb')->where('project_id', $project->id)->whereBetween('created_at', [$dateForm, $dateTo])->get()->count();
                                                    $getTotalSites = \Tritiyo\Site\Models\Site::where('project_id', $project->id)->whereBetween('created_at', [$dateForm, $dateTo])->get()->count();
                                                    $getTaskLimit = $getTask/$getTotalSites;
                                                    echo $getTaskLimit;
                                                ?>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($targetSiteCompletion->target_range_date))
                                                <?php
                                                    $getCompleteSites = DB::table('sites')->where('project_id', $project->id)->where('completion_status', 'Completed')->whereBetween('created_at', [$dateForm, $dateTo])->get()->count();
                                                    echo $getCompleteSites;
                                                ?>
                                                @endif
                                            </td>
                                            <td>&nbsp;
                                                @if(!empty($targetInvoiceSubmission->target_range_date))
                                                    <?php
                                                        $getTotalInvoice = DB::table('site_invoices')->where('project_id', $project->id)->whereBetween('invoice_date', [$dateForm, $dateTo])->get()->sum('invoice_amount');
                                                        echo $getTotalInvoice;
                                                    ?>
                                                @endif
                                            </td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <th>Mark</th>
                                            <td>{{ $targetProjectCosting->mark ?? null}}</td>
                                            <td>{{ $targetTaskLimit->mark ?? null}}</td>
                                            <td>{{ $targetSiteCompletion->mark ?? null}}</td>
                                            <td>{{ $targetInvoiceSubmission->mark ?? null}}</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <th>Achieved Points</th>
                                            <td>{{$getMarkProjectCosting = Tritiyo\Project\Helpers\ProjectKpiHelper::getCalculate($targetProjectCosting->counting_type ?? '', $targetProjectCosting->meta_value ?? 0, $getProjectCost ?? 0, $targetProjectCosting->mark ?? 0 )}}</td>
                                            <td>{{$getMarkTaskLimit = Tritiyo\Project\Helpers\ProjectKpiHelper::getCalculate($targetTaskLimit->counting_type ?? '', $targetTaskLimit->meta_value ?? 0, $getTaskLimit ?? 0, $targetTaskLimit->mark ?? 0 )}}</td>
                                            <td>{{$getMarkSiteCompletion = Tritiyo\Project\Helpers\ProjectKpiHelper::getCalculate($targetSiteCompletion->counting_type ?? '', $targetSiteCompletion->meta_value ?? 0, $getCompleteSites ?? 0, $targetSiteCompletion->mark ?? 0 )}}</td>
                                            <td>{{$getMarkInvoiceSubmission = Tritiyo\Project\Helpers\ProjectKpiHelper::getCalculate($targetInvoiceSubmission->counting_type ?? '', $targetInvoiceSubmission->meta_value ?? 0, $getTotalInvoice ?? 0, $targetInvoiceSubmission->mark ?? 0 )}}</td>
                                            <td>In Total <br>
                                                {{$total = $getMarkProjectCosting + $getMarkTaskLimit + $getMarkSiteCompletion + $getMarkInvoiceSubmission}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Project Bonus</th>
                                            <td colspan="4">&nbsp;</td>
                                            <td>{{ \Tritiyo\Project\Helpers\ProjectKpiHelper::getBonus($requestTargetRange, $requestYear, $project->id, $total)}}</td>
                                        </tr>
                                            @if(!empty($targetTaskLimit->target_range_date))
                                                @if(auth()->user()->isAdmin(auth()->user()->id))
                                                    <tr style="background: #faebd7">
                                                        <th style="background: #faebd7">&nbsp</th>
                                                        <td colspan="2">Total Sites: {{ $getTotalSites ?? Null}} <br></td>
                                                        <td colspan="2">Total Task: {{$getTask ?? Null}}<br></td>
                                                        <td></td>
                                                    </tr>
                                                @endif
                                            @endif
                                        </tbody>
                                    </table>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                        @endif
                    </div>
                </div>


            </div>
        </div>
    </div>


@endsection

@section('cusjs')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

    <script>
        var xValues = ["January-March", "April-June", "July-September", "October-December"];
        var yValues = [69, 84, 78, 97];
        var barColors = ["green", "skyblue", "orange", "brown"];

        new Chart("myChart", {
            type: "bar",
            data: {
                labels: xValues,
                datasets: [{
                    backgroundColor: barColors,
                    data: yValues
                }]
            },
            options: {
                legend: {display: false},
                title: {
                    display: true,
                    text: ""
                }
            }
        });
    </script>



    <style>
        tr th {
            text-align: center !important;
            vertical-align: top;
        }
        tr td {
            text-align: center !important;
            vertical-align: top;
        }
    </style>

@endsection
