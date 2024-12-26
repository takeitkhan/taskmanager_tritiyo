@extends('layouts.app')

@section('title')
    Range Project
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
            <a href="{{ route('projects.show', $project->id) }}">
                <i class="fas fa-list"></i>&nbsp; Project Data All Time
            </a>
            <a href="{{ route('projects.current.range', $project->id) }}">
                <i class="fas fa-list"></i>&nbsp; Current Range Project Data
            </a>

            <a href="{{ route('projects.range', $project->id) }}" class="is-active">
                <i class="fas fa-list"></i>&nbsp; Range based tasks
            </a>

            <a href="{{ route('projects.site', $project->id) }}">
                <i class="fas fa-list"></i>&nbsp; Site of project
            </a>
            <a href="{{route('projects.sites.info', $project->id)}}">
                <i class="fas fa-list"></i>&nbsp; Range Based Site Information of Project
            </a>
        </p>

        @php
            function status_based_count($project_id, $status) {
                $total_sites = \Tritiyo\Site\Models\Site::where('project_id', $project_id)->where('completion_status', $status)->get();
                //dd($total_sites);
                return count($total_sites);
                #SELECT * FROM sites WHERE project_id = 8 AND completion_status = 'Running'
            }
        @endphp
        <section id="rangeAccordion" class="accordions">
            <?php
            $ranges = \Tritiyo\Project\Helpers\ProjectHelper::all_ranges($project->id);
            foreach ($ranges as $key => $range) {
            $exploded = explode(',', $range->status_string);
            //dump($exploded[0]);
            $range_datas0 = explode('|', $exploded[0]);
                if (count($exploded) > 1) {
                    $range_datas1 = explode('|', $exploded[1]);
                } else {
                    $today = explode('|', $exploded[0]);
                    $range_datas1 = [
                        '0' => $today[0],
                        '1' => $today[1],
                        '2' => date('Y-m-d'),
                        '3' => $today[3],
                        '4' => $today[4]
                    ];
                }
                $rangeKey = str_replace(' ', '',$range_datas0[4]);
            ?>


            <div class="card tile is-child has-background-info-light my-2 accordion {{request()->get('range_key') == $rangeKey ? 'is-active' : ''}}">
                <header class="card-header  accordion-header stoggle">
                    <p class="card-header-title">
                        <span class="icon"><i class="fas fa-tasks default"></i></span>
                        {{$range_datas0[2]}} - {{$range_datas1[2]}}
                        <a class="ml-3 is-size-7 has-text-link-dark"
                           href="{{route('projects.range', $project->id)}}?range_key={{$rangeKey}}">Click Here</a>
                    </p>
                </header>

                <div class="accordion-body">
                    @if(request()->get('range_key') == $rangeKey)
                    <div class="card-content">
                        <div class="card-data accordion-content">
                            <div class="level">
                                <div class="level-left">
                                    <strong>Range based tasks </strong>
                                </div>
                                <div class="level-right">
                                    <div class="level-item tag is-warning is-light">
                                        {{$range_datas0[2]}} - {{$range_datas1[2]}}
                                    </div>
                                </div>
                            </div>

                            <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth"
                                   style="text-align: left;">
                                <tr>
                                    <td colspan="2" width="50%">
                                        <div class="notification is-warning has-text-centered">
                                            Budget <br/>
                                            <h1 class="title">
                                                BDT.

                                                {{\Tritiyo\Project\Helpers\ProjectHelper::current_range_budgets($range_datas0[1], $range_datas0[0])}}

                                                <?php //dump($range); ?>
                                            </h1>

                                            <p> &nbsp; </p>
                                        </div>
                                    </td>
                                    <td colspan="2">
                                        <div class="notification is-link has-text-centered">
                                            Total Budget Used
                                            <h1 class="title">
                                                @php

                                                      $total_requisition = \Tritiyo\Project\Helpers\ProjectHelper::ttrbGetTotalByProject('reba_amount', $project->id, $range_datas0[0]);
                                                @endphp

                                                <?php
                                                $mobileBill = round(Tritiyo\Project\Models\MobileBill::where('project_id', $project->id)->where('range_id', $range_datas0[0])->get()->sum('received_amount'), 2);
                                                $budgetuse = $total_requisition;
                                                //dump($range_datas0[0]);
                                                ?>
                                                
                                                BDT. {{ $budgetuse + $mobileBill }}

                                            </h1>
                                            <small>
                                                Used Budget BDT. {{$budgetuse}} + Mobile Bill BDT. {{$mobileBill}}
                                            </small>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">&nbsp;</td>
                                </tr>



                            </table>

                        </div>

                        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                            <tr>
                                <th>Task Name</th>
                                <th>Task For</th>
                                <th>Project Name</th>
                                <th>Project Manager</th>
                                <th>Site Code</th>
                                <th>Site Head</th>
                                <th>Requisition Approved</th>
                                <th>Submit Bill</th>
                                <th>Bill Approved</th>
                            </tr>
                            <?php //echo request()->get('daterange');?>
                            @php
                                $start = $range_datas0[2];
                                $end = $range_datas1[2];
                                $tasks = \Tritiyo\Task\Models\Task::where('project_id', $project->id)->whereBetween('task_for', [$start, $end])->paginate(50);
                            @endphp
                            <?php
                                $requisitionApproveTotal = [];
                                $submitBill = [];
                                $billApproveTotal = [];
                            ?>

                            @foreach($tasks as $task)
                                @php
                                    $project = Tritiyo\Project\Models\Project::where('id', $task->project_id)->first();
                                    $sites = Tritiyo\Task\Models\TaskSite::leftjoin('sites', 'sites.id', 'tasks_site.site_id')->select('sites.site_code')->where('tasks_site.task_id', $task->id)->first();
                                    $site_id = Tritiyo\Task\Models\TaskSite::leftjoin('sites', 'sites.id', 'tasks_site.site_id')->select('sites.id')->where('tasks_site.task_id', $task->id)->first() ?? NULL;
                                    $task_name = $task->task_name ?? NULL;
                                    $task_for = $task->task_for ?? NULL;
                                    $project_name = $project->name ?? NULL;
                                    $manager_name = App\Models\User::where('id', $task->user_id)->first()->name ?? NULL;
                                    $site_code = $sites->site_code ?? NULL;
                                    $site_head = $task->site_head ?? NULL;
                                    $site_head_name = App\Models\User::where('id', $task->site_head)->first()->name ?? NULL;
                                    /*
                                    $rm = new \Tritiyo\Task\Helpers\SiteHeadTotal('requisition_edited_by_accountant', $task->id, false);
                                    $requisition_approved_total = $rm->getTotal();

                                    $rm = new \Tritiyo\Task\Helpers\SiteHeadTotal('bill_edited_by_accountant', $task->id, false);
                                    $bill_approved_total = $rm->getTotal();
                                    */

                                    $rm = DB::SELECT("SELECT reba_amount FROM ttrb WHERE id = " . $task->id);
                                    $requisition_approved_total = $rm[0]->reba_amount;

                                    $sb = DB::SELECT("SELECT bpbr_amount FROM ttrb WHERE id = " . $task->id);
                                    $submit_bill_total = $sb[0]->bpbr_amount;

                                    $bill = DB::SELECT("SELECT beba_amount FROM ttrb WHERE id = " . $task->id);
                                    $bill_approved_total = $bill[0]->beba_amount;
                                @endphp


                                <tr>
                                    <td>
                                        <a href="{{route('tasks.show', $task->id)}}" target="__blank">
                                            {{ $task_name }}
                                        </a>
                                    </td>
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
                                            <a target="__blank"
                                               href="{{ route('sites.show', $site_id) }}">
                                                {{ $site_code ?? NULL }}
                                            </a>
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
                                            BDT.  {{ $requisitionApproveTotal[] = $requisition_approved_total }}
                                    </td>
                                    <td>
                                            BDT.  {{ $submitBill[] = $submit_bill_total }}
                                    </td>
                                    <td>
                                            BDT.  {{ $billApproveTotal[] =  $bill_approved_total }}
                                    </td>

                                </tr>
                            @endforeach

                            <tr>
                                <td colspan="6"></td>
                                <td> Total:  {{ array_sum($requisitionApproveTotal) }} </td>
                                <td>Total: {{array_sum($submitBill) }}</td>
                                <td>  Total:   {{ array_sum($billApproveTotal) }}</td>
                            </tr>
                        </table>
                        <div class="pagination_wrap pagination is-centered">
                            {{ $tasks->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                    @endif
                </div>

            </div>


            <?php
            //dump($range_datas0);
            //dump($range_datas1);
            } // End Range Foreach
            ?>
        </section>
    </article>

@endsection



@section('cusjs')
    <style type="text/css">
        .table.is-fullwidth {
            width: 100%;
            font-size: 15px;
            text-align: center;
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

    <style>
        /* Accordion */
        section#rangeAccordion.accordions .accordion .accordion-header {
            align-items: center;
            background-color: #c4c2fd !important;
            border-radius: 4px 4px 0 0;
            color: #fff;
            display: flex;
            line-height: 0em;
            padding: 0em .0em !important;
            position: relative;
            border: 0px;
        }
        section#rangeAccordion.accordions .accordion.is-active .accordion-header {
            background-color: #a189d4 !important;
        }
        section#rangeAccordion.accordions .accordion {
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
            border-radius: 4px;
            font-size: 13px;
            border: 0px;
        }

        section#rangeAccordion.accordions .accordion .accordion-header + .accordion-body .accordion-content {
            padding: 0em 0em;
        }

        section#rangeAccordion.accordions .accordion a:not(.button):not(.tag) {
            text-decoration: none;
        }
    </style>

    <script type="text/javascript"
            src="https://cdn.jsdelivr.net/npm/bulma-accordion@2.0.1/dist/js/bulma-accordion.min.js"></script>

    <script>

        var accordions = bulmaAccordion.attach();

    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma-accordion@2.0.1/dist/css/bulma-accordion.min.css">

@endsection

