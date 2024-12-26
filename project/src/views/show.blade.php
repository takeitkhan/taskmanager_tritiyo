@extends('layouts.app')

@section('title')
    Single Project
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
            'spTitle' => 'Single Project',
            'spSubTitle' => 'view a Project',
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
            <a href="javascript:void(0)" class="is-active">
                <i class="fas fa-list"></i>&nbsp; Project Data All Time
            </a>

            <a href="{{ route('projects.current.range', $project->id) }}">
                <i class="fas fa-list"></i>&nbsp; Current Range Project Data
            </a>

            <a href="{{ route('projects.range', $project->id) }}">
                <i class="fas fa-list"></i>&nbsp; Range based tasks
            </a>

            <a href="{{ route('projects.site', $project->id) }}">
                <i class="fas fa-list"></i>&nbsp; Site of project
            </a>
            <a href="{{route('projects.sites.info', $project->id)}}">
                <i class="fas fa-list"></i>&nbsp; Range Based Site Information of Project
            </a>


        </p>


        <div class="card tile is-child">
            <div class="card-content">
                <div class="card-data">
                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth"
                           style="text-align: left;">
                        <tr>
                            <td colspan="4">
                                <strong>Project Information</strong>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="4">
                                <table class="table is-bordered is-striped is-narrow is-fullwidth">
                                    <tr>
                                        <td width="25%">
                                            <a href="{{ route('projects.site', $project->id) }}" target="_blank">
                                                <div class="tag is-dark has-text-white"
                                                     style="font-size: 16px; width: 100%;">
                                                    Total Sites:
                                                    {{ \Tritiyo\Site\Models\Site::where('project_id', $project->id)->count() }}
                                                </div>
                                            </a>
                                        </td>
                                        <td width="25%">
                                            <a href="{{ route('projects.site', $project->id) }}?key=Running" target="_blank">
                                                <div class="tag is-success has-text-white"
                                                     style="font-size: 16px; width: 100%;">
                                                    Total Running Site: {{ status_based_count($project->id, 'Running') }}
                                                </div>
                                            </a>
                                        </td>
                                        <td width="25%">
                                            <a href="{{ route('projects.site', $project->id) }}?key=Completed" target="_blank">
                                                <div class="tag is-link has-text-white"
                                                     style="font-size: 16px; width: 100%;">
                                                    Total Completed
                                                    Site: {{ status_based_count($project->id, 'Completed') }}
                                                </div>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('projects.site', $project->id) }}?key=Rejected" target="_blank">
                                                <div class="tag is-danger has-text-white"
                                                     style="font-size: 16px; width: 100%;">
                                                    Total Rejected Site: {{ status_based_count($project->id, 'Rejected') }}
                                                </div>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                 
                        <tr>
                          	<td colspan="4">
                              <table class="table is-fullwidth">
                                <tr>
                                                  <td>
                                                      <div class="notification is-warning has-text-centered">
                                                          Budget <br/>
                                                          <h1 class="title">
                                                              BDT.
                                                              {{\Tritiyo\Project\Helpers\ProjectHelper::all_range_budgets($project->id)}}

                                                          </h1>
                                                          <p> &nbsp; </p>

                                                      </div>
                                                  </td>  
                                  
                                                  <td>
                                                      <div class="notification is-link has-text-centered">
                                                          Total Budget Used
                                                          <h1 class="title">


                                                              <?php
                                                              $current_range_id = \Tritiyo\Project\Helpers\ProjectHelper::current_range_id($project->id);
                                                              $mobileBill = round(\Tritiyo\Project\Models\MobileBill::where('project_id', $project->id)->get()->sum('received_amount'), 2);
                                                              //$budgetuse = \Tritiyo\Project\Helpers\ProjectHelper::used_range_budgets($project->id);
                                                             $budgetuse = \Tritiyo\Project\Helpers\ProjectHelper::ttrbGetTotalByProject('reba_amount', $project->id, $current_range_id);
                                                           
                                                              //dump($mobileBill);
                                                              ?>
                                                         
                                                              BDT. {{$budgetuse + $mobileBill}}
                                                             

                                                          </h1>
                                                          <small>
                                                              Used Budget BDT. {{$budgetuse}} + Mobile Bill BDT. {{$mobileBill}}
                                                          </small>
                                                      </div>
                                                  </td>
                                   
                                                  <td>
                                                      <div class="notification is-info has-text-centered">
                                                          Total Invoice Submitted
                                                          <h1 class="title">
                                                              <?php
                                                              $invoiceSiteAmount = round(\Tritiyo\Site\Models\SiteInvoice::where('project_id', $project->id)->get()->sum('invoice_amount'));                                        
                                                              //dump($mobileBill);
                                                              ?>
                                                              BDT. {{ $invoiceSiteAmount }}

                                                          </h1>
                                                        <small>
                                                          &nbsp;
                                                        </small>
                                                      </div>
                                                  </td>
                                               
                                </tr>
                               
                              </table>
                              </td>
                        </tr>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>

                        @php
                            function status_based_count($project_id, $status) {
                                $total_sites = \Tritiyo\Site\Models\Site::where('project_id', $project_id)->where('completion_status', $status)->get();
                                //dd($total_sites);
                                return count($total_sites);
                                #SELECT * FROM sites WHERE project_id = 8 AND completion_status = 'Running'
                            }
                        @endphp


                        <tr>
                            <td width="15%"><strong>Project Name:</strong></td>
                            <td>{{$project->name}}</td>
                            <td width="15%"><strong>Project Code:</strong></td>
                            <td>{{ $project->code }}</td>
                        </tr>


                        <tr>
                            <td><strong>Project Type:</strong></td>
                            <td>{{ $project->type }}</td>
                            <td><strong>Project Manager:</strong></td>
                            <td>
                                @php $projectManager = \App\Models\User::where('id', $project->manager)->first() @endphp
                                {{ !empty($projectManager) ? $projectManager->name : '' }}
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Project customer:</strong></td>
                            <td>{{ $project->customer }}</td>
                            <td><strong>Project vendor:</strong></td>
                            <td>{{ $project->vendor }}</td>
                        </tr>

                        <tr>
                            <td><strong>Project supplier:</strong></td>
                            <td>{{ $project->supplier }}</td>
                            <td><strong></strong></td>
                            <td></td>
                        </tr>

                        <tr>
                            <td><strong>Project address:</strong></td>
                            <td>{{ $project->address }}</td>
                            <td><strong>Project location:</strong></td>
                            <td>{{ $project->location }}</td>
                        </tr>

                        <tr>
                            <td><strong>Head Office:</strong></td>
                            <td>{{ $project->office }}</td>
                            <td></td>
                            <td></td>
                        </tr>

                        <tr>
                            <td><strong>Project start:</strong></td>
                            <td>{{ $project->start }}</td>
                            <td><strong>Approximate project end:</strong></td>
                            <td>{{ $project->end }}</td>
                        </tr>

                        <tr>
                            <td colspan="4">
                                {{ $project->summary }}
                            </td>
                        </tr>

                    </table>
                    <div class="level">
                        <div class="level-left">
                            <strong>Project based tasks</strong>
                        </div>
                        <div class="level-right">
                            <div class="level-item ">
                                <form method="get" action="{{ route('projects.show', $project->id) }}">
                                    @csrf

                                    <div class="field has-addons">
                                        <a href="{{ route('download_excel_project') }}?id={{ $project->id }}&daterange={{ request()->get('daterange') ?? date('Y-m-d', strtotime(date('Y-m-d'). ' - 30 days')) . ' - ' . date('Y-m-d') }}"
                                           class="button is-primary is-small">
                                            Download as excel
                                        </a>
                                        <div class="control">
                                            <input class="input is-small" type="text" name="daterange" id="textboxID"
                                                   value="{{ request()->get('daterange') ?? null }}">
                                        </div>
                                        <div class="control">
                                            <input name="search" type="submit"
                                                   class="button is-small is-primary has-background-primary-dark"
                                                   value="Search"/>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                    <tr>
                        <th>Task Name</th>
                        <th>Task Status</th>
                        <th>Task For</th>
                        <th>Project Name</th>
                        <th>Project Manager</th>
                        <th>Site Code</th>
                        <th>Site Head</th>
                        <th>Requisition Approved</th>
                        <th>Submit  Bill</th>
                        <th>Bill Approved</th>
                    </tr>
                    <?php //echo request()->get('daterange');?>
                    @php
                        if (request()->get('daterange')) {
                                $dates = explode(' - ', request()->get('daterange'));
                                $start = $dates[0];
                                $end = $dates[1];

                            $tasks = \Tritiyo\Task\Models\Task::where('project_id', $project->id)->whereBetween('task_for', [$start, $end])->paginate(30);


                        } else {
                           $start = date('Y-m-d', strtotime(date('Y-m-d'). ' - 30 days'));
                           $end = date('Y-m-d');
                           $tasks = \Tritiyo\Task\Models\Task::where('project_id', $project->id)->whereBetween('task_for', [$start, $end])->paginate(30);
                        }
                    @endphp
                    @php
                        $sumOf = [];
                        $submitBill = [];
                        $billApproveSum = [];
                    @endphp
                    @foreach($tasks as $task)
                        @php
                            $project = \Tritiyo\Project\Models\Project::where('id', $task->project_id)->first();
                            $sites = \Tritiyo\Task\Models\TaskSite::leftjoin('sites', 'sites.id', 'tasks_site.site_id')
                  						->select('sites.site_code', 'sites.id')->where('tasks_site.task_id', $task->id)->groupBy('tasks_site.site_id')->get();
                  			$site_id = \Tritiyo\Task\Models\TaskSite::leftjoin('sites', 'sites.id', 'tasks_site.site_id')->select('sites.id')->where('tasks_site.task_id', $task->id)->first() ?? NULL;
                            $task_name = $task->task_name ?? NULL;
                            $task_for = $task->task_for ?? NULL;
                            $project_name = $project->name ?? NULL;
                            $manager_name = App\Models\User::where('id', $task->user_id)->first()->name ?? NULL;
                            //$site_code = $sites->site_code ?? NULL;
                            $site_head = $task->site_head ?? NULL;
                            $site_head_name = App\Models\User::where('id', $task->site_head)->first()->name ?? NULL;
                            /*
                            $rm = new \Tritiyo\Task\Helpers\SiteHeadTotal('requisition_edited_by_accountant', $task->id, true);
                            $rmf = new \Tritiyo\Task\Helpers\SiteHeadTotal('requisition_edited_by_accountant', $task->id, false);
                            $requisition_approved_total = $rm->getTotal();
                            $requisition_approved_totalf = $rmf->getTotal();

                            $rm = new \Tritiyo\Task\Helpers\SiteHeadTotal('bill_edited_by_accountant', $task->id, true);
                            $bill_approved_total = $rm->getTotal();
                            */
                            $calculate = \Tritiyo\Task\Models\TaskRequisitionBill::select('bpbr_amount', 'rpbm_amount', 'bebm_amount', 'rebc_amount', 'bebc_amount', 'reba_amount', 'beba_amount', )
                                    ->where('task_id', $task->id)->first();
                        @endphp

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
                                    {{ $task_name }}
                                </a>
                            </td>
                            <td>{{$statusCheck->message}}</td>
                            <td>{{ $task_for }}</td>
                            <td>
                                <a target="__blank"
                                   href="{{ route('projects.show', $project->id)}}">
                                    {{ $project_name }}
                                </a>
                            </td>
                            <td>{{ $manager_name ?? NULL  }}</td>
                            <td>
                                @if(!empty($site_id))
                              		@foreach($sites as $site)
                              		@php
                              		//dump($site);
                              		$site_code =  $site->site_code; @endphp
                                    <a target="__blank"
                                       href="{{ route('sites.show', $site->id) }}">
                                        {{ $site_code ?? NULL }} <br/>
                                    </a>
                              		@endforeach
                                @endif
                            </td>
                            <td>
                                @if(!empty($site_head))
                                    <a href="{{ route('hidtory.user', $site_head) }}">
                                        {{ $site_head_name ?? NULL }}
                                    </a>
                                @endif
                            </td>
                            <td>

                                BDT.   {{$sumOf[] = $calculate->reba_amount ?? 0}}

                            </td>
                            <td>
                                BDT. {{ $submitBill []= $calculate->bpbr_amount ?? 0  }}
                            </td>
                            <td>
                                BDT. {{ $billApproveSum []= $calculate->beba_amount ?? 0 }}
                            </td>

                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="7" style="text-align: right;">
                            Total
                        </td>
                        <td>
                            BDT. {{ array_sum($sumOf) }}
                        </td>
                        <td>
                            BDT.	{{array_sum($submitBill)}}</td>
                        <td>
                            BDT.   {{array_sum($billApproveSum)}}
                        </td>
                    </tr>
                </table>
                <div class="pagination_wrap pagination is-centered">
                    @if(Request::get('daterange') )
                        {{ $tasks->appends(['daterange' => Request::get('daterange')])->links('pagination::bootstrap-4') }}
                    @else
                        {{ $tasks->links('pagination::bootstrap-4') }}
                    @endif
                </div>
            </div>
        </div>



    </article>

@endsection

@section('cusjs')
    <style type="text/css">
        .table.is-fullwidth {
            width: 100%;
            font-size: 15px;
        }
    </style>
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

@endsection

