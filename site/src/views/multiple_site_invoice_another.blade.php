@extends('layouts.app')

@section('title')
    Multiple Site Invoice Generate Together
@endsection


@section('column_left')
    <div class="column is-10 mx-auto">
        <div class="card tile is-child">
            <header class="card-header">
                <p class="card-header-title">
                <span class="icon">
                    <i class="fas fa-file-invoice"></i>
                </span>
                    Site Invoice Generate Together
                </p>
            </header>
            <div class="card-content">
                <div class="card-data">
                  
                  
                  <br/>
                @php
                    if(!empty($invoiceNo)){
                        $route =  route('multiple.site.invoice.together.update');
                    } else  {
                        $route =  route('multiple.site.invoice.together.store');
                    }
                @endphp

                {{ Form::open(array('url' => $route, 'method' => 'post', 'value' => 'PATCH', 'id' => 'add_route', 'files' => true, 'autocomplete' => 'off')) }}
                        <div class="columns">
                            <div class="column">                        
                                @csrf
                                @if(!empty($invoiceNo))
                                    <input type="hidden" name="invoiceNo" value="{{$invoiceNo}}" />
                                @endif
                              
                              
                                <div class="columns">
                                  	<div class="column is-4">
                                      	<div class="field">
                                            {{ Form::label('projects', 'Select a project', array('class' => 'label')) }}
                                            @php
                                                if(auth()->user()->isCFO(auth()->user()->id) || auth()->user()->isAccountant(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id) ){
                                                    $projects = \Tritiyo\Project\Models\Project::latest()->get();
                                                } else {
                                                    $projects = \Tritiyo\Project\Models\Project::where('manager', auth()->user()->id)->latest()->get();
                                                }
                                            @endphp
                                            <select class="input is-small" name="project_id" id="project_select" date-project="{{!empty($projectId) ? $projectId : ''}}">
                                                <option>Select a project</option>
                                                @foreach($projects as $project)
                                                    <option value="{{$project->id}}" {{!empty($projectId) && $projectId == $project->id ? 'selected': ''}} >{{$project->name }}</option>
                                                @endforeach
                                            </select>
                                      	</div>
                                      	<div class="field">
                                          	{{ Form::label('projects', 'Select a range', array('class' => 'label')) }}
                                          	<select class="input is-small" name="range_status_key"  id="range_select" required>
                                          	</select>
                                      	</div>
                                      	<div class="field">
                                            <input type="checkbox" id="checkbox" > Select All
                                            {{ Form::label('sites', 'Select multiple sites for invoicing', array('class' => 'label')) }}

                                            @php
                                            //$sites = \Tritiyo\Site\Models\Site::where('id', $invoicesite->site_id)->latest()->first()
                                            @endphp

                                            <select class="js-example-tokenizer input" multiple="multiple" name="site_id[]"  id="site_select1">                                    
                                            </select>
                                          </div>
                                      
                                      	 <div class="field">
                                            {{ Form::label('invoice_no', 'Invoice No', array('class' => 'label')) }}
                                            <div class="control">
                                                {{ Form::text('invoice_no', $invoiceNo ?? NULL, ['required', 'class' => 'input is-small', 'placeholder' => 'Enter invoice no...']) }}
                                            </div>
                                        </div>
                                      
                                      	<div class="field">
                                            {{ Form::label('invoice_date', 'Invoice Date', array('class' => 'label')) }}
                                            <div class="control">
                                                {{ Form::text('invoice_date', $invoiceDate ?? NULL, ['required', 'class' => 'input is-small', 'id' => 'datepicker', 'placeholder' => 'Enter invoice date...']) }}
                                            </div>
                                        </div>
                                      	<div class="field">
                                            {{ Form::label('total_invoice_amount', 'Total Invoice Amount', array('class' => 'label')) }}
                                            <div class="control">
                                                {{ Form::text('total_invoice_amount', $invoiceDate ?? NULL, ['required', 'class' => 'input is-small', 'placeholder' => 'Enter total invoice amount...']) }}
                                            </div>
                                        </div>
                                      	<div class="field">
                                            {{ Form::label('invoice_powo', 'Invoice PO/WO No.', array('class' => 'label')) }}
                                            <div class="control">
                                                {{ Form::text('invoice_powo', $invoiceDate ?? NULL, ['required', 'class' => 'input is-small', 'placeholder' => 'Enter invoice po/wo no...']) }}
                                            </div>
                                        </div>
                                                                                                                   
                                          <div class="field">
                                            {{ Form::label('invoice_type', 'Initial Invoice Type', array('class' => 'label')) }}
                                            <div class="control">
                                              <?php $invoice_types = ['' => 'Select a invoice type', 'Partial' => 'Partial', 'Full' => 'Full']; ?>
                                              {{ Form::select('invoice_type[]', $invoice_types, $invoicesite->invoice_type ?? NULL, ['class' => 'input is-small', 'required']) }}
                                            </div>
                                          </div>
                                      
                                  	</div>
                                    <div class="column is-8">
                                      
                                      @php
                                      		$invoices = Tritiyo\Site\Models\SiteInvoiceInfo::where('action_performed_by', auth()->user()->id)->where('completion_status', 'Undone')->get();
                                      @endphp
                                      
                                      @if(count($invoices) > 0)
                                      <article class="panel is-primary">
                                          <h5  class="subtitle is-5 has-text-centered">
                                              Invoices you created
                                          </h5>
                                        
                                        	<table class="table table-bordered is-fullwidth">  
                                              <thead>
                                                <tr>
                                                  <th>Project Name</th>
                                                  <th>Range</th>
                                                  <th>Invoice No.</th>
                                                  <th>PO/WO No.</th>
                                                  <th>Total Sites</th>
                                                  <th>Total Amount</th>
                                                  <th>Completion Status</th>
                                                </tr>
                                              </thead>                                              
                                              <tbody>
                                                @foreach($invoices as $key => $value)
                                                  <tr>
                                                      <td>
                                                        	@php
                                                        			$project_name = \Tritiyo\Project\Models\Project::where('id', $value->project_id)->first()->name;
                                                        	@endphp
                                                        <a href="{{ route('multiple.site.invoice.together.edit.view', $value->id) }}">{{ $project_name ?? NULL }}</a>
                                                    	</td>
                                                      <td>{{ $value->range_id ?? NULL }}</td>
                                                      <td>
                                                        	<a href="{{ route('multiple.site.invoice.together.edit.view', $value->id) }}">{{ $value->invoice_info_no ?? NULL }}</a>
                                                      </td>
                                                      <td>{{ $value->invoice_powo ?? NULL }}</td>
                                                      <td>
                                                        	@php
                                                        		$total_sites = \Tritiyo\Site\Models\SiteInvoice::where('invoice_no', $value->invoice_info_no)->get()->count();
                                                        	@endphp
                                                        	{{ $total_sites ?? NULL }}
                                                      </td>
                                                      <td>{{ $value->invoice_total_amount ?? NULL }}</td>
                                                    	<td>{{ $value->completion_status ?? NULL }}</td>
                                                  </tr>
                                                @endforeach
                                              </tbody>
                                        </table>
                                      </article>
                                      @else
                                      	<article class="panel is-primary">
                                          	<h5  class="subtitle is-5 has-text-centered">
                                              There are no undone invoice found
                                          </h5>
                                      	</article>
                                      @endif
                                      
                                      
                                      
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="columns">
                            <div class="column is-12">
                              <div class="field is-grouped">
                                <div class="control">
                                  <button type="submit" class="button is-success is-small">Save Changes</button>
                                </div>
                              </div> 
                            </div>
                        </div>
                    </div>
                {{ Form::close() }}              
            </div>
        </div>
    </div>
@endsection


@section('cusjs')           
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" type="text/javascript"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
     
<script>
jQuery.browser = {
    msie: false,
    version: 0
};
</script>
    <script>

        function pr(arg, arg2 = 1, siteid = 0) {
            $.ajax({
                method: 'GET',
                url   : "{{route('project.based.site', ['', ''])}}/"+arg+"/"+siteid,
                success : function (data){
                    //console.log(data);
                    let siteList = {};
                    //siteList += '<option></option>';
                    for(let i = 0; i < data.length; i++){
                        siteList += '<option class="s'+arg2+data[i].id+'" value="'+data[i].id+'">'+data[i].site_code+'</option>';
                        //console.log(data[i].site_code)
                    }
                    $('#site_select'+arg2).empty().append(siteList);
                }
            })
        }
		$('select#project_select').change(function(){
          		let project_id = $(this).find(':selected').val();
          
          		$.ajax({
                    method: 'GET',
                    url   : "{{ route('project.based.range', ['', ''])}}/"+ project_id,
                    success : function (data) {
                        //console.log(data);
                        let siteList = {};
                        //siteList += '<option></option>';
                        for(let i = 0; i < data.length; i++) {
                          	let end_date = null;
                          	if(data[i].end_date == null) {
                              	end_date = '<?php echo date('Y-m-d'); ?>';
                            } else {
                              	end_date = data[i].end_date;
                            }
                            siteList += '<option value="'+data[i].status_key+'">'+ data[i].start_date + ' - ' + end_date +'</option>';
                            //console.log(data[i].site_code)
                        }
                        $('#range_select').empty().append(siteList);
                    }
                });          		
        });


        /** catchSiteList **/

        var counter = 2;
		window.localStorage.clear();
        $('select#project_select').change(function(){
          	//window.localStorage.clear();
          	localStorage.setItem('catchSiteList', 0)
          	//localStorage.getItem('catchSiteList');
            let pid = $(this).val();
            //$('#siteBox #r').empty();
          	$('#site_select1').val();
            pr(pid);
            $(this).attr('date-project', pid);
            $("div#append_row").empty();
            siteSelectRefresh();
        })
		function catchAllSelectSite(list){
           	let lst = localStorage.getItem('catchSiteList');
        	localStorage.setItem('catchSiteList', lst+','+list)
        }

        $("#add_more_btn").on("click", function () {
            var pid = $('select#project_select').attr('date-project');
            let ee = counter-1;
            let list = $('select#site_select'+ee).val();
          	let invoice = $('select#site_invoice'+ee).val();
          	//alert(ee);          	          
         	 if(list && invoice){
                  if(invoice == 'Full'){
                    catchAllSelectSite(list);
                  }

                  let catchList = localStorage.getItem('catchSiteList');
                  //alert(catchList);
                  pr(pid, counter, catchList);
                  var cols = `
                          <div id="r" class="columns r-${counter}">
                              <div class="column is-4 ar-${counter}">
                              <div class="field">
                               {{ Form::label('sites', 'Select a site', array('class' => 'label')) }}
                              <select class="js-example-tokenizer input site-select" name="site_id[]"  id="site_select${counter}" multiple="multiple">
                              </select>
                              </div>
                              </div>
                              <div class="column is-4 ar-${counter}">
                              <div class="field">
                                  {{ Form::label('invoice_amount', 'Invoice Amount', array('class' => 'label')) }}
                              <div class="control">
                                  {{ Form::text('invoice_amount[]', $site->invoice_amount ?? NULL, ['required', 'class' => 'input is-small', 'placeholder' => 'Enter invoice amount...']) }}
                              </div>
                              </div>
                              </div>
                              <div class="column is-3 ar-${counter}">
                              <div class="field">
                                  {{ Form::label('invoice_type', 'Invoice Type', array('class' => 'label')) }}
                              <div class="control">
                                  <?php //$invoice_types = ['' => 'Select a invoice type', 'Partial' => 'Partial', 'Full' => 'Full']; ?>
                                  {{ Form::select('invoice_type[]', $invoice_types, $site->invoice_type ?? NULL, ['class' => 'input is-small', 'id'=>'site_invoice${counter}', 'required']) }}
                              </div>
                              </div>
                              </div>
                              <div class="column is-1">
                              <label>&nbsp;<br></label>
                              <a class="tag is-danger" id="delete_btn" data-id="${counter}">Delete</a>
                              </div>
                              </div>
                        `;

                  $("div#append_row").append(cols);
                 // $('option.s'+counter+list).remove();
               		$('.ar-'+ee).css('pointer-events','none')
                  siteSelectRefresh(counter);
                  counter++;
          } else {
            alert('Select type of Invoice && Select Site')
          }
        });

        $("select#site_select2").click(function (){
            //alert('ok');
        })


        $("div#append_row").on("click", "#delete_btn", function (event) {
          	let id = $(this).data('id');
          	let mid = id -1
            $(this).closest("div.columns").remove();
          	$(".ar-"+mid).css('pointer-events','unset')
            counter -= 1
        });


        $(".js-example-tokenizer").select2({
            tags: true,
            tokenSeparators: [',', ' ']
        });

        $(function() {
          //minDate: -20, maxDate: "+1M +15D",
        	$("#datepicker").datepicker({ dateFormat: 'yy-mm-dd'});        
        });
        
        </script>

        <script>

            // Select 2
            function siteSelectRefresh(arg = 1) {
              $('select#site_select' + arg).select2({
                placeholder: "Select Site",
                allowClear: true,
              });
              
              
              $("#checkbox").click(function(){
              if($("#checkbox").is(':checked') ){
                $("select#site_select" + arg + "> option").prop("selected","selected");
                $("select#site_select" + arg + "> option").trigger("change");
              }else{
                $("select#site_select" + arg + "> option").removeAttr("selected");
                $("select#site_select" + arg + "> option").trigger("change");
              }
            });
            }
            //siteSelectRefresh()
            
           
        </script>


@endsection
