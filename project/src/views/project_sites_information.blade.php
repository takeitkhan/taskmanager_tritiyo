@extends('layouts.app')


@section('title')
Information of Range Based Total Sites Of Project 
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
        'spTitle' => 'Range Based Sites Of Project',
        'spSubTitle' => 'view all sites of current project',
        'spShowTitleSet' => true
        ])

        @include('component.button_set', [
        'spShowButtonSet' => true,
        'spAddUrl' => null,
        'spAddUrl' => $addUrl,
        'spAllData' => route('projects.index'),
        'spSearchData' => '#',
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
    <?php
    $projectId = $project->id;
    $rangeId = \Tritiyo\Project\Helpers\ProjectHelper::current_range_id($projectId);
    $allranges = \Tritiyo\Project\Helpers\ProjectHelper::all_ranges($project->id);
    //dump($allranges);
    ?>
    <article class="panel is-primary">
        <p class="panel-tabs">
            <a href="{{ route('projects.show', $project->id) }}">
                <i class="fas fa-list"></i>&nbsp;  Project Data All Time
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
            <a href="{{route('projects.sites.info', $project->id)}}" class="is-active">
                <i class="fas fa-list"></i>&nbsp; Range Based Site Information of Project
            </a>
        </p>
        <br/>

        <?php
            //$sites = \Tritiyo\Site\Models\Site::where('sites.project_id', $projectId)->paginate('30');
        ?>
        <section id="rangeAccordion" class="accordions">
            @foreach($allranges as $cRange)

            @php
                $rangeId = explode(' | ', $cRange->status_string);

                $exploded = explode(',', $cRange->status_string);
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
                 $sites = \Tritiyo\Task\Models\TaskSite::leftjoin('tasks', 'tasks.id', 'tasks_site.task_id')
                             ->leftjoin('sites', 'tasks_site.site_id', 'sites.id')
                             ->where('sites.project_id', $projectId)
                             ->where('tasks.current_range_id', $rangeId[0])
                             ->groupBy('sites.id')
                             ->paginate('30');
                 //$sites = \Tritiyo\Site\Models\Site::where('sites.project_id', $projectId)->paginate('30');
                	//dd($sites);
                 //$range_datas0 = explode('|', $exploded[0]);

            @endphp
          
                <div class="card tile is-child has-background-info-light my-2 accordion {{request()->get('range_key') == $rangeKey ? 'is-active' : ''}}">
                    <header class="card-header  accordion-header stoggle">
                        <p class="card-header-title">
                            <span class="icon"><i class="fas fa-tasks default"></i></span>
                            {{$range_datas0[2]}} - {{$range_datas1[2]}}
                            <a class="ml-3 is-size-7 has-text-link-dark"
                               href="{{route('projects.sites.info', $project->id)}}?range_key={{$rangeKey}}">Click Here</a>
                        </p>
                    </header>
                @if(request()->get('range_key') )
                    <div class="accordion-body">
                        <div class="level">
                            <div class="level-left"></div>
                            <div class="level-right">
                                <div class="level-item">
                                    <a href="{{ route('projects.sites.info.export', $project->id) }}?range_id={{$rangeId[0]}}&&range_date={{$range_datas0[2].'-'.$range_datas1[2]}}"
                                       class="button is-primary is-small">
                                        Download as excel
                                    </a>
                                </div>
                            </div>
                        </div>
                        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                            <tr>
                                <th width="3%">SL</th>
                                <th width="20%">Site Code</th>
                                <th>Completion Status</th>
                                <th> Task Count</th>
                              <?php /*
                                <th>Requisition Approved</th>
                                <th>Bill Submitted</th>
                                <th>Bill Approved</th>
                                */ ?>
                            </tr>

                            @foreach($sites as $key => $site)
                                <tr>
                                    <td>{{$key + $sites->firstitem()}}</td>
                                    <td>
                                        <a target="__blank" href="{{ route('sites.show', $site->id) }}" class="has-text-link">
                                        {{$site->site_code}}
                                        </a>
                                    </td>
                                    <td> {{$site->completion_status}} </td>
                                 
                                    <td>
                                        @php
                                            $getTask = DB::table('tasks_site_datas')->where('site_id', $site->id)->groupBy('task_id')->get();
                                        @endphp

                                        <?php 
                                       
                                         ///Requisition / Biil Amount
                                      
                                        $reba = [];
                                        $bpbr = [];
                                        $beba = [];

                                        $sharedreba = [];
                                        $sharedbpbr = [];
                                        $sharedbeba = [];

                                        $sharedTask = 0;
                                        foreach($getTask as $data){
                                         
                                            $amount = \Tritiyo\Task\Models\TaskRequisitionBill::select('reba_amount', 'bpbr_amount', 'beba_amount')->where('task_id', $data->task_id)->first();
                                          //dd($amount);
                                            $reba []= $amount['reba_amount'] ?? 0;
                                            $bpbr []= $amount['bpbr_amount'] ?? 0;
                                            $beba []= $amount['beba_amount'] ?? 0;
										
                                            $sharedTask += DB::table('tasks_site')->where('task_id', $data->task_id)
                                                            ->whereNotIn('site_id', [$site->id])->groupBy('task_id')->get()->count();
                                                         
                                            //$reba [] = $amount->sum('reba_amount');
                                            //$bpbr [] = $amount->sum('bpbr_amount');
                                            //$beba [] = $amount->sum('beba_amount');
											/*
                                            $sharedAmount = DB::table('ttrb')
                                                            ->leftjoin('tasks_site', 'tasks_site.task_id', 'ttrb.task_id')
                                                            ->select('ttrb.reba_amount', 'ttrb.bpbr_amount', 'ttrb.beba_amount')
                                                            ->where('ttrb.task_id', $data->task_id)
                                                            ->whereNotIn('tasks_site.site_id', [$site->id])
                                                            ->groupBy('tasks_site.task_id')
                                                            ->get();

                                            $sharedreba []= $sharedAmount->sum('reba_amount');
                                            $sharedbpbr []= $sharedAmount->sum('bpbr_amount');
                                            $sharedbeba []= $sharedAmount->sum('beba_amount');
                                            */
                                        }
                                        ?>
										
                                        <strong title="Total Task">TT:</strong> {{$totalTask = $getTask->count()}}
                                        <br>
                                        <strong title="Shared Task">ST:</strong> {{ $sharedTask }}
                                        <strong title="Non Shared Task">NST:</strong> {{ $totalTask - $sharedTask }}
                                     
                                    </td>
                                  <?php /*
                                    <td>
                                        <strong title="Total">T:</strong> {{ array_sum($reba) }}
                                        <br>
                                        <strong title="Shared">S:</strong> {{ array_sum($sharedreba) }}
                                        <strong title="Non Shared">NS:</strong> {{  array_sum($reba) - array_sum($sharedreba) }}
                                        
                                    </td>
                                  
                                    <td>
                                       {{array_sum($bpbr)}}
                                       <br>
                                       <strong title="Shared">S:</strong> {{ array_sum($sharedbpbr) }}
                                       <strong title="Non Shared">NS:</strong> {{  array_sum($bpbr) - array_sum($sharedbpbr) }}
                                    </td>

                                    <td>
                                        {{array_sum($beba)}}
                                        <br>
                                        <strong title="Shared">S:</strong> {{ array_sum($sharedbeba) }}
                                        <strong title="Non Shared">NS:</strong> {{  array_sum($beba) - array_sum($sharedbeba) }}
                                    </td>
                                    */ ?>
                                  
                     				
                                </tr>
                            @endforeach
                        </table>
                        <div class="pagination_wrap pagination is-centered">
                            @if(request()->get('range_key') )
                                {{ $sites->appends(['range_key'=> request()->get('range_key')])->links('pagination::bootstrap-4') }}
                            @else
                            {{ $sites->links('pagination::bootstrap-4') }}
                            @endif
                        </div>
                    </div>
                @endif
                </div>
            @endforeach
        </section>
    </article>


@endsection


@section('cusjs')


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

        .accordions .accordion.is-active .accordion-body {
            max-height: 100em;
            overflow: hidden;
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
