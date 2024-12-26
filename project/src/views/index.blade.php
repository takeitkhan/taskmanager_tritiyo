@extends('layouts.app')

@section('title')
    Projects
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
            'spTitle' => 'Projects',
            'spSubTitle' => 'all projects here',
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
            'spPlaceholder' => 'Search projects...',
            'spAddUrl' => route('projects.create'),
            'spAllData' => route('projects.index'),
            'spSearchData' => route('projects.search'),
            'spMessage' => $message = $message ?? NULl,
            'spStatus' => $status = $status ?? NULL
        ])
    </nav>
</section>

@section('column_left')
    <div class="columns is-multiline">
        @php
            if(auth()->user()->isManager(auth()->user()->id)) {
                $manager_id = auth()->user()->id;
                $projectsss = \DB::table('projects')
                                ->where('projects.manager', $manager_id)
                                ->paginate(30);
            } else {
                $projectsss = $projects;
            }
        @endphp
        @if(!empty($projectsss))
            @foreach($projectsss as $project)
      			@php
      				$project_status = \Tritiyo\Project\Helpers\ProjectHelper::project_status($project->id);      				
      			@endphp 
                <div class="column is-3">                  
                    <div class="borderedCol {{ ($project_status != 'Active') ? 'has-background-danger-light' : '' }}">
                        <article class="media">
                            <div class="media-content">
                                <div class="content">
                                    <p>
                                        <strong>
                                            <a href="{{ route('projects.show', $project->id) }}"
                                               title="View project">
                                                {{ $project->name }}                                              	
                                            </a>
                                        </strong>
                                        <br/>
                                        <small> 
                                          	<strong>ID: </strong> {{ $project->id }}
                                            <br/>
                                            <strong>Type: </strong> {{ $project->type }}
                                            <br/>
                                            <strong>Project Manager:</strong>
                                            @php
                                                $pm = \Tritiyo\Project\Models\Project::where('id', $project->id)->first()->manager ?? 0;
                                                $pm_name = \App\Models\User::where('id', $pm)->first()->name ?? null;
                                            @endphp
                                            <a href="{{ route('hidtory.user', $pm) }}"
                                               target="_blank"
                                               title="View project manager">
                                                {{ \App\Models\User::where('id', $project->manager)->first()->name ?? null }}
                                            </a>
                                            {{--                                        <!-- <strong>Code: </strong> {{ $project->code }}, -->--}}
                                        </small>
                                        <br/>
                                        <small>

                                            <strong>Customer:</strong> {{ $project->customer }}
                                        <!-- <strong>Vendor:</strong> {{ $project->vendor }},
                                            <strong>Supplier:</strong> {{ $project->supplier }} -->
                                        </small>
                                      	<br/>
                                      	<small>
                                          <strong>Status:</strong> {{$project_status ?? 'Inactive'}}
                                      	</small>
                                        <br/>

                                        @php
                                            $exists = \Tritiyo\Project\Models\ProjectRange::where('project_id', $project->id)->orderBy('id', 'desc')->get();
                                            //dd($exists);
                                        @endphp
                                        
                                    <strong>Current Range Budget Information Till Today</strong>
                                  	<br/>
                                    <small>
                                      <table class="table is-striped is-bordered is-narrow is-size-7">
                                        <thead>
                                          <tr class="is-selected has-background-link-light has-text-link-dark">
                                            <td>Total Budget</td>
                                            <td>Used Budget</td>
                                            <td>Mobile Bill</td>
                                            <td>Total Used</td>
                                          </tr>
                                        </thead>
                                        
                                        <tbody>
                                          <tr>
                                            <td>
                                                    @php
                                                        $allCurrentBudgets = \Tritiyo\Project\Helpers\ProjectHelper::current_range_budgets($project->id);
                                                    @endphp
                                            		BDT. {{ $allCurrentBudgets ?? NULL }}
                                          	</td>
                                          
                                          	<?php
                                                 $current_range_id = \Tritiyo\Project\Helpers\ProjectHelper::current_range_id($project->id);
                                           		 $mobileBill = \Tritiyo\Project\Models\MobileBill::where('project_id', $project->id)->where('range_id', $current_range_id)->get()->sum('received_amount');
                                          		 $budgetuse = \Tritiyo\Project\Helpers\ProjectHelper::ttrbGetTotalByProject('reba_amount', $project->id, $current_range_id);
                                            ?>
                                           <td> BDT. {{number_format($budgetuse)}} </td>
                                           <td>  BDT . {{number_format($mobileBill)}} </td>
                                           <td> BDT. {{number_format($budgetuse + $mobileBill  )}} </td>
                                        </tr>
                                        </tbody>

                                      </table>
                                    </small>
                                  </p>
                                </div>
                                <nav class="level is-mobile">
                                    <div class="level-left">
                                        <a href="{{ route('projects.show', $project->id) }}"
                                           class="level-item"
                                           title="View project">
                                            <span class="icon is-small"><i class="fas fa-eye"></i></span>
                                        </a>
                                        @if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
                                            <a href="{{ route('projects.edit', $project->id) }}"
                                               class="level-item"
                                               title="Edit Project Information">
                                                <span class="icon is-info is-small">
                                                  <i class="fas fa-edit"></i>
                                              	</span>
                                            </a>
                                            @php
                                                $project_status = \Tritiyo\Project\Helpers\ProjectHelper::project_status($project->id);
                                            @endphp
                                            @if($project_status == 'Active')
                                                <a href="{{ route('project_budgets.create') }}?project_id={{ $project->id }}"
                                                   class="level-item"
                                                   title="Add Project Budget">
                                                <span class="icon is-small" style="color: green; font-size: 15px;">
                                                  <i class="fas fa-plus"></i>
                                              	</span>
                                                </a>
                                            @endif

                                            <a href="{{ route('project_ranges.create') }}?project_id={{ $project->id }}"
                                               class="level-item"
                                               title="Edit Project Status">
                                                <span class="icon is-small" style="color: red; font-size: 17px;">
                                                  <i class="fas fa-pen-square"></i>
                                              	</span>
                                            </a>

                                            <a href="{{ route('target.projects.kpi.create')}}?project_id={{ $project->id }}"
                                               class="level-item"
                                               style="display: none"
                                               title="Key Performance indicators">
                                                <span class="icon is-small" style="color: #ff5400; font-size: 17px;">
                                                   <i class="far fa-chart-bar"></i>
                                                </span>
                                            </a>

                                            {{-- {!! delete_data('projects.destroy',  $project->id) !!}--}}
                                        @endif
                                        <!-- For Only Accountant -->
                                        @if(auth()->user()->isAccountant(auth()->user()->id))
                                            @php
                                                $project_status = \Tritiyo\Project\Helpers\ProjectHelper::project_status($project->id);
                                            @endphp
                                            @if($project_status == 'Active')
                                                <a href="{{ route('project_budgets.create') }}?project_id={{ $project->id }}"
                                                   class="level-item"
                                                   title="Add Project Budget">
                                                    <span class="icon is-small" style="color: green; font-size: 15px;">
                                                      <i class="fas fa-plus"></i>
                                                    </span>
                                                </a>
                                            @endif
                                        @endif

                                    </div>
                                </nav>
                            </div>
                        </article>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <div class="pagination_wrap pagination is-centered">

        @if(Request::get('key'))
            {{ $projects->appends(['key' => Request::get('key')])->links('pagination::bootstrap-4') }}
        @else
            {{$projects->links('pagination::bootstrap-4')}}
        @endif
    </div>
@endsection
