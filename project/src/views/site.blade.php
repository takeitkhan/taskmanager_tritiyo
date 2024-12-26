@extends('layouts.app')

@section('title')
    Sites Of Project
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
            'spTitle' => 'Sites Of Project',
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
            'spShowFilterSet' => false,
            'spPlaceholder' => 'Search projects...',
            'spMessage' => $message = $message ?? NULl,
            'spStatus' => $status = $status ?? NULL
        ])
    </nav>
</section>

@section('column_left')
    <article class="panel is-primary">
        <p class="panel-tabs">
            <a href="{{ route('projects.show', $projectId) }}">
                <i class="fas fa-list"></i>&nbsp;  Project Data All Time
            </a>

            <a href="{{ route('projects.current.range', $projectId) }}">
                <i class="fas fa-list"></i>&nbsp; Current Range Project Data
            </a>

            <a href="{{ route('projects.range', $projectId) }}">
                <i class="fas fa-list"></i>&nbsp; Range based tasks
            </a>

            <a href="{{ route('projects.site', $projectId) }}" class="is-active">
                <i class="fas fa-list"></i>&nbsp; Site of project
            </a>
            <a href="{{route('projects.sites.info', $projectId)}}" class="">
                <i class="fas fa-list"></i>&nbsp; Range Based Site Information of Project
            </a>
        </p>
        <br/>

        <div class="column is-3">
          <div style="display: inline-block; xfloat: right; margin-top: 0px;">
            <div class="level-rights">
              <div class="control">

                {{ Form::open(array('url' => $spSearchData ?? NULL,'method' => 'get','value' => 'PATCH','id' => 'search','files' => true,'autocomplete' => 'off')) }}
                  <div class="sb-example-1">
                    <div class="search">
                      <input
                                      id="textboxID"
                                      name="key"
                                      type="text"
                                      class="input"
                                      placeholder="Search sites on for this project..."
                                      value="{{ request()->get('key') ? request()->get('key') : ''  }}">
                      <button
                              type="submit"
                              class="submit">
                        <i class="fa fa-search"></i>
                      </button>
                      @if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id) )
                        <label for="select_all" class="button is-small ml-2">
                            <input type="checkbox" id="select_all" class="mr-1"> Select All
                        </label>
                       @endif
                    </div>
                  </div>
               
                {{ Form::close() }}
                
                  
              </div>
                
              
            </div>
          </div>
            <a href="{{route('project.site.export', $projectId)}}?key={{request()->get('key')}}"
               class="button is-primary is-small">
              Download as excel
          </a>
          <script type="text/javascript">
            document.getElementById('textboxID').select();
          </script>
        </div>
     

      	<?php
      		if(!empty(request()->get('key'))) {
              $key = request()->get('key');
              $pid = request()->route('id');

              $sites = \Tritiyo\Site\Models\Site::where('project_id', $pid ?? NULL)
                ->where(function($group) use ($key)  {
                  	$group->orWhere('location', 'LIKE', '%' . $key . '%');
                    $group->orWhere('site_code', 'LIKE', '%' . $key . '%');
                    $group->orWhere('material', 'LIKE', '%' . $key . '%');
                    $group->orWhere('site_head', 'LIKE', '%' . $key . '%');
                    $group->orWhere('completion_status', 'LIKE', '%' . $key . '%');
                })->paginate(48);
              //dd($sites);
            } else {
              $sites = $sites;
            }
      	?>
        <form action="{{route('projects.site.recurring')}}" method="POST" id="">
            @csrf
            @if(!empty($sites))
            <div class="columns is-multiline">
                @foreach($sites as $key => $site)
                    <?php
                      if ($site->completion_status == 'Rejected') {
                        $siteStatus = 'danger';
                      } elseif ($site->completion_status == 'Completed') {
                        $siteStatus = 'completed';
                      }elseif ($site->completion_status == 'Running') {
                          $siteStatus = 'running';
                      } else {
                        $siteStatus = '';
                      }

                      if ($site->site_type == 'Recurring') {
                          $siteStatus = 'primary';
                      }
                    ?>
                    <div class="column is-3">
                        <div class="borderedCol {{$siteStatus}}">
                            <article class="media">
                                <div class="media-content">
                                    <div class="content">
                                        <p>
                                            <small>
                                                <strong>Code: </strong>
                                                <a href="{{ route('sites.show', $site->id) }}"
                                                    target="_blank"
                                                    title="View route">
                                                    {{ $site->site_code }}
                                                </a>
                                                <input type="checkbox" class="status_update_all" value="{{$site->id}}" name="recurring[{{$key}}][site_id]" style="float:right"/>
                                            </small>
                                            <br/>

                                            <strong>Location: </strong>
                                                    {{ $site->location }}
                                            <br/>
                                            <small>
                                                <strong>Project: </strong>
                                                @php $project = \Tritiyo\Project\Models\Project::where('id', $site->project_id)->first() @endphp
                                                <a href="{{ route('projects.show', $site->project_id) }}"
                                                    title="View route">
                                                    {{  $project->name }}
                                                </a>
                                            </small>
                                            <br/>
                                            <small>
                                                <strong>Site Type: </strong>
                                                {{$site->site_type ?? Null}}
                                            </small>
                                            <br/>
                                            <small>
                                                <strong>Completion Status: </strong>
                                                {{$site->completion_status ?? 'Activity not started'}}
                                            </small>
                                          	<br/>
                                            <small>
                                                <strong>Invoice Creation: </strong>
                                                @php
                                              		$invoice_type = Tritiyo\Site\Models\SiteInvoice::where('site_id', $site->id)->orderBy('id', 'desc')->first()->invoice_type ?? NULL;
                                              	@endphp
                                              	{{ $invoice_type }}
                                            </small>
                                        </p>
                                        <nav class="level is-mobile">
                                            <div class="level-left">
                                                <a href="{{ route('sites.show', $site->id) }}"
                                                    class="level-item"
                                                    title="View user data">
                                                    <span class="icon is-small"><i class="fas fa-eye"></i></span>
                                                </a>

                                                @if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
                                                    <a href="{{ route('sites.edit', $site->id) }}"
                                                    class="level-item"
                                                    title="View all transaction">
                                                        <span class="icon is-info is-small"><i class="fas fa-edit"></i></span>
                                                    </a>
                                                @endif

                                                {{-- {!! delete_data('sites.destroy',  $site->id) !!} --}}
                                            </div>
                                        </nav>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                @endforeach
            </div>
          @if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id) )
          	<div class="p-4">
              <label for="site_status" class="label">Site Status</label>
              <select class="input is-small is-inline"  id="completion_status" name="selected_site_status">
                  <option value=""></option>
                  <option value="Running">Running</option>
                  <option value="Rejected">Rejected</option>
                  <option value="Completed">Completed</option>
                  <option value="Pending">Pending</option>
                  <option value="Discard">Discard</option>
              </select>
            	<input xtype="submit" class="button st submit is-link is-small" name="selected_site_status_submit" value="Submit"/>
          </div>
          
          <div class="p-4">
              <label for="site_status" class="label">Site Type</label>
              <input xtype="submit" class="button sr submit is-link is-small" name="selected_site_recurring" id="selected_site_recurring" value="Submit For Recurring" />
          </div>
          @endif
        </form>
        <div class="pagination_wrap pagination is-centered">
          @if(!empty(request()->get('key')))
          	{{ $sites->appends(['key' => request()->get('key')])->links('pagination::bootstrap-4') }}
          @else
          	{{ $sites->links('pagination::bootstrap-4') }}
          @endif
        </div>
        @endif
    </article>
@endsection


@section('cusjs')
    <script>
        document.getElementById('select_all').onclick = function () {
            var checkboxes = document.getElementsByClassName('status_update_all');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        }
     
    </script>

<script>

  $('input.st').on('click', function(){
    if ($('input.status_update_all').prop('checked')) {

        let title =  $('#completion_status').val();
        if(title){
          $('#selected_site_recurring').val('');
          confirmAlert('You want to update the completion status of your selected sites to '+title+'. Are you confirm?');
        }else {
          alert('Select a status')
        }
      
    }else{
    	alert('You did not select any site');
    }
     
     	
  })
  
   $('input.sr').on('click', function(){
     
     if ($('input.status_update_all').prop('checked')) {
       	$('#completion_status').val('Submit For Recurring')
       	let title =  'Recurring';
       	confirmAlert('You want to update the type of your selected sites to '+title+'. Are you confirm?');
     }else {
     	alert('You did not select any site');
     }
     
  })
  //let title = 'ff';
</script>
@endsection
