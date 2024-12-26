@extends('layouts.app')

@section('title')
    Multiple Site Invoice Generate Together
@endsection


@section('column_left')
	@php
			$list = request()->get('from');
			if($list == 'list') {
					$check = TRUE;
			} else {
					$check = FALSE;
			}
	@endphp

    <div class="column is-10 mx-auto">
        <div class="card tile is-child">
            <header class="card-header">
                <p class="card-header-title">
                <span class="icon">
                    <i class="fas fa-file-invoice"></i>
                </span>
                     {{ ($check == TRUE) ? 'Site Invoice List' : 'Site Invoice Generate Together'  }}
                </p>
            </header>
            <div class="card-content">
                <div class="card-data">
                  	<div class="columns">
                      	<div class="column is-7">
                          <section class="hero is-">
                            <div class="hero-body">
                              <h1 style="font-size: 20px;">{{ ($check == TRUE) ? 'Site Invoice List' : 'Invoices created'  }}</h1>
                              <h2 style="font-size: 15px;">{{ ($check == TRUE) ? 'You just can see the invoices here' : 'Now, Please edit invoices as you need'  }}</h2>
                            </div>
                          </section>
                        </div>
                      	<div class="column is-3">
                          	<table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                              	<tbody>
                                  	<tr>
                                      	<td>
                                          		<b>Project Name:</b>
                                                @php
                                                      $project_name = \Tritiyo\Project\Models\Project::where('id', $invoiceInfo->project_id)->first()->name;
                                                @endphp
                                                {{ $project_name ?? NULL }}
                                      	</td>
                                  	</tr>
                                 	<tr>
                                      	<td>
                                          		<b>Invoice Date:</b>
                                          		{{ $invoiceInfo->invoice_date ?? NULL }}
                                      	</td>
                                  	</tr>
                                  	<tr>
                                      	<td>
                                          		<b>Created Date:</b>
                                          		{{ $invoiceInfo->created_at ?? NULL }}
                                      	</td>
                                  	</tr>
                                  	<tr>
                                      	<td>                                          		
                                          		<b>PO/WO No.:</b>
                                          		{{ $invoiceInfo->invoice_powo ?? NULL }}
                                      	</td>
                                  	</tr>
                                    <tr>
                                          <td>                                          		
                                                  <b>Invoice No.:</b>
                                            	 {{ $invoiceInfo->invoice_info_no ?? NULL }}
                                          </td>
                                      </tr>
                                      <tr>
                                            <td>                                          		
                                                    <b>Total Amount:</b>
                                              		{{ $invoiceInfo->invoice_total_amount ?? NULL }}
                                            </td>
                                        </tr>
                              	</tbody>
                          	</table>
                        </div>
                      	<div class="column is-3">
                          	&nbsp;
                      	</div>
                  </div>
                  
                  
                  @php
                      $route =  route('multiple.site.invoice.together.update');
                      $total = Tritiyo\Site\Models\SiteInvoice::where('invoice_no', $invoiceInfo->invoice_info_no)->get()->sum('invoice_amount');
                                    
                      $tot = number_format($total, 2);
                      $invoiceInfoTotal = number_format($invoiceInfo->invoice_total_amount, 2);                          			
                  @endphp
                  
                  @if($invoiceInfo->completion_status == 'Done')
                  
                  @else
                      @if($tot == $invoiceInfoTotal)                          			
                  			{{ Form::open(array('url' => route('invoice.edit.done'), 'method' => 'post', 'value' => 'PATCH', 'id' => 'add_route', 'files' => true, 'autocomplete' => 'off')) }}
                      @else
							{{ Form::open(array('url' => $route, 'method' => 'post', 'value' => 'PATCH', 'id' => 'add_route', 'files' => true, 'autocomplete' => 'off')) }}
                      @endif
                  @endif
                  
                  
                  <div class="columns">
                      	<div class="column is-9">                          		
                                  <div class="reload_me">                    	
                                          <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                            <tbody>
                                                <tr>
                                                    <th width="350">Project Name</th>
                                                    <th width="180">Site Name</th>
                                                    <th width="100">Invoice No.</th>
                                                    <th width="100">Invoice Date</th>
                                                    <th width="100">Invoice Amount</th>
                                                    <th width="100">Invoice Type</th>
                                                </tr>
                                              	@php
                                              			$i_total = [];
                                              	@endphp
                                                @foreach($invoiceSites as $key => $value)
                                              	 <input type="hidden" name="invoiceNo[]" value="{{$value->id}}" />
                                                <tr>
                                                    <td>                                                      
                                                      @php
                                                            $project = \Tritiyo\Project\Models\Project::where('id', $value->project_id)->get()->first();
                                                      @endphp
                                                      {{ $project->name }}
                                                    </td>
                                                    <td>
                                                      @php
                                                            $sites = \Tritiyo\Site\Models\Site::where('id', $value->site_id)->latest()->first()
                                                      @endphp
                                                      {{ $sites->site_code }}
                                                    </td>
                                                    <td>{{ $value->invoice_no }}</td>
                                                    <td>{{ $value->invoice_date }}</td>
                                                    <td>
                                                      @php
                                                      	if($invoiceInfo->completion_status == 'Done') {
                                                      		$readonly = 'disabled';
                                                      	} else {
                                                      		$readonly = null;
                                                      	}
                                                      @endphp
                                                      {{ Form::text('invoice_amount[]', $value->invoice_amount ?? NULL, ['required', 'class' => 'input is-small', 'id' => 'invoice_amount_' . $value->id, 'placeholder' => 'Enter invoice amount...', $readonly]) }}
                                                      @php
                                                      		$i_total[] = $value->invoice_amount;
                                                      @endphp
                                                  </td>
                                                    <td>
                                                      <?php $invoice_types = ['Partial' => 'Partial', 'Full' => 'Full']; ?>
                                                      {{ Form::select('invoice_type[]', $invoice_types, $value->invoice_type ?? NULL, ['class' => 'input is-small', 'id'=>'site_invoice_' .  $value->id, 'required', $readonly]) }}                                      
                                                    </td>
                                                </tr>
                                                @endforeach
                                              	<tr>
                                                  	<td colspan="4">
                                                  	</td>
                                                  	<td>
                                                      	{{ $total = array_sum($i_total) }}
                                                  	</td>
                                                  	<td></td>
                                              	</tr>
                                            </tbody>
                                        </table>
                                  </div>                                 
                    	</div>
                    	<div class="column is-3">
                          
                          		@if($invoiceInfo->completion_status == 'Done')
                          				<div class="button is-success is-small">Final Saved</div>
                                @else                          			
                                    @if($tot == $invoiceInfoTotal)
                                              <input type="hidden" name="invoice_info_id" value="{{ $invoiceInfo->id }}" />
                                              <button class="button is-primary is-small">Final Save</button>
                                    @else
                                          		<button class="button is-primary is-small">Save</button>
                                    @endif
                                @endif
                          
                          		<a href="{{  ($check == TRUE) ? route('multiple.site.invoices.list')  : route('multiple.site.invoice.together')  }}" class="button is-info is-small ml-2">
                                  	{{ ($check == TRUE) ? 'Back to Site Invoice List' : 'Back to invoice generator'  }}                                  	
                          		</a>
                          		
                    	</div>
                  </div>
                  {{ Form::close() }}				                  
                  
              </div>
          </div>
      </div>
</div>

@endsection

@section('cusjs')
	<script type="text/javascript">
            	      
      	function saveInvoiceData(id) {
          		var url = "{{ route('multiple.site.invoice.together.single.edit', ['', '']) }}/"+ id    
          
          		var datas = {
                        invoice_amount: $('#invoice_amount_' + id).val(),
                        invoice_type: $( "#site_invoice_" + id + " option:selected").val()
                }
          
                $.ajax({
                      url: url,
                      type: "GET",
                      headers: {
                        	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      beforeSend: function(xhr){
                            xhr.setRequestHeader("Content-Type","application/json");
                            xhr.setRequestHeader("Accept","application/json");
                      },
                      data: datas,
                      dataType:"json",
                  	  success: function() {
                        	$('#reload_me').load( document.URL + ' #reload_me');
                      }
                });
        }
	</script>
@endsection