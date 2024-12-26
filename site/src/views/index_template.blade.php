<div class="column is-2">
    <?php
    if ($site->completion_status == 'Rejected') {
        $siteStatus = 'danger';
    } elseif ($site->completion_status == 'Completed') {
        $siteStatus = 'completed';
    } elseif ($site->completion_status == 'Running') {
        $siteStatus = 'running';
    } else {
        $siteStatus = '';
    }
  
  
  	$checkSiteInvoice = DB::table('site_invoices')->where('site_id', $site->id)->get();
  	if(count($checkSiteInvoice) > 0){
    	$hasSiteInvoice = 'border: 6px solid #ff9800';
    }
    ?>
    <div class="borderedCol {{$siteStatus}}" style="{{$hasSiteInvoice ?? null}}">
        <article class="media">
            <div class="media-content">
                <div class="content">
                    <p>
                        <strong>
                            <strong>Code: </strong>
                            <a href="{{ route('sites.show', $site->id) }}"
                               title="View site">
                                {{ $site->site_code }}
                            </a>
                        </strong>
                        <br/>

                        <small>
                            <strong>Location: </strong>
                            {{ $site->location }}
                            <br/>
                            <strong>Project: </strong>
                            @php
                                $project = \Tritiyo\Project\Models\Project::where('id', $site->project_id)->first()
                            @endphp
                            <a href="{{ route('projects.show', $site->project_id) }}"
                               target="_blank"
                               title="View project">
                                {{  $project->name }}
                            </a>
                            <br/>
                            <strong>Project Manager:</strong>
                            @php
                                $pm = \Tritiyo\Project\Models\Project::where('id', $site->project_id)->first()->manager;
                                $pm_name = \App\Models\User::where('id', $pm)->first()->name;
                            @endphp
                            <a href="{{ route('hidtory.user', $pm) }}"
                               target="_blank"
                               title="View project manager">
                                {{ $pm_name }}
                            </a>
                            <br/>
                            <strong>Task Created: </strong>
                            {{ $site->created_at }}
                            <br/>
                            <strong>Status: </strong>
                            {{ $site->completion_status ?? NULL }}
                          	<br>
                          @if(count($checkSiteInvoice) > 0)
                          <strong>Total Invoice </strong>
                          {{count($checkSiteInvoice)}}
                          @endif
                        </small>
                        <br/>
                    </p>
                </div>
                <nav class="level is-mobile">
                    <div class="level-left">
                        <a href="{{ route('sites.show', $site->id) }}"
                           class="level-item"
                           title="View user data">
                            <span class="icon is-small"><i class="fas fa-eye"></i></span>
                        </a>
                        @if(auth()->user()->isApprover(auth()->user()->id))
                            @if($site->completion_status == 'Completed')
                            @else
                                <a href="{{ route('sites.edit', $site->id) }}"
                                   class="level-item"
                                   title="View all transaction">
                                <span class="icon is-info is-small">
                                    <i class="fas fa-edit"></i>
                                </span>
                                </a>
                            @endif
                      	@elseif(auth()->user()->isAdmin(auth()->user()->id))
                      	  		<a href="{{ route('sites.edit', $site->id) }}"
                                   class="level-item"
                                   title="View all transaction">
                                <span class="icon is-info is-small">
                                    <i class="fas fa-edit"></i>
                                </span>
                                </a>
                        @endif
						@if(auth()->user()->isAdmin(auth()->user()->id))
                             {!! delete_data('sites.destroy',  $site->id) !!}
                      	@endif
                    </div>
                </nav>
            </div>
        </article>
    </div>
</div>
