@extends('layouts.app')

@section('title')
      Site Invoices List
@endsection


@section('column_left')
    <div class="column is-10 mx-auto">
        <div class="card tile is-child">          
            <header class="card-header">
                  <p class="card-header-title">
                  <span class="icon">
                      <i class="fas fa-file-invoice"></i>
                  </span>
                  Site Invoice List
                </p>              	
            </header>      
            <div class="card-content">              
                <div class="card-data">
                      <div class="level-right">
                        <div class="level-item ">
                          <form method="get" action="{{ route('multiple.site.invoices.list') }}">
                            @csrf

                            <div class="field has-addons">
                              <?php /** <a href="{{ route('download_excel_project') }}?id={{ $project->id }}&daterange={{ request()->get('daterange') ?? date('Y-m-d', strtotime(date('Y-m-d'). ' - 30 days')) . ' - ' . date('Y-m-d') }}"
                                     class="button is-primary is-small">
                                    Download as excel
                                  </a>
                                  **/ ?>
                              <div class="control">
                                <input class="input is-small" type="text" name="invoice_no"
                                       value="{{ request()->get('invoice_no') ?? null }}" placeholder="Search with invoice no...">
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
               		<table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                      		<tr>
                              		<th>Project Manager</th>
                              		<th>Invoice Info</th>
                              		<th>Project Name</th>
                              		<th>Bill for</th>
                              		<th>Invoice Total Amount</th>
                              		<th>Invoice Date</th>
                              		<th>PO/WO no.</th>
                              		<th>Completion Status</th>
                      		</tr>
                            @foreach($lists as $key => $value)
                                  <tr>
                                          <td>{{ App\Models\User::where('id', $value->action_performed_by)->first()->name ?? NULL }}</td>
                                          <td>
                                            	<a href="{{ route('multiple.site.invoice.together.edit.view', $value->id) }}?from=list">{{ $value->invoice_info_no ?? NULL }}</a>
                  						 </td>
                                          <td>
                                            	{{ Tritiyo\Project\Models\Project::where('id', $value->project_id)->first()->name ?? NULL }}
                                    	 </td>
                                          <td>
                                            	@php
                                            			$start = Tritiyo\Project\Helpers\ProjectHelper::get_range_by_status_key($value->range_status_key, 'Active');
                                            			$end = Tritiyo\Project\Helpers\ProjectHelper::get_range_by_status_key($value->range_status_key, 'Inactive');
                                            	@endphp
                                            	{{ $start . " - " . ($end ?? date('Y-m-d')) }}
                                    	  </td>                                    	
                                          <td>{{ $value->invoice_total_amount ?? NULL }}</td>
                                    	  <td>{{ $value->invoice_date ?? NULL }}</td>
                                    	  <td>{{ $value->invoice_powo ?? NULL }}</td>
                                    	  <td>{{ $value->completion_status ?? NULL }}</td>
                                  </tr>
                            @endforeach
                  	</table>
                    <div class="pagination_wrap pagination is-centered">
                      {{ $lists->links('pagination::bootstrap-4') }}
                    </div>
              	</div>
          </div>
      </div>
</div>

@endsection