@extends('layouts.app')

@section('title')
    Multiple Site Invoice Generate
@endsection


@section('column_left')
    <div class="column is-10 mx-auto">
        <div class="card tile is-child">
        <header class="card-header">
            <p class="card-header-title">
            <span class="icon">
                <i class="fas fa-file-invoice"></i>
            </span>
                Site Invoice Generate
            </p>
        </header>
        <div class="card-content">
            <div class="card-data">
                <div class="columns">
                    <div class="column">
                        <br/>
                        @php
                            if(!empty($invoiceNo)){
                                $route =  route('multiple.site.invoice.update');
                            } else  {
                                $route =  route('multiple.site.invoice.store');
                            }
                        @endphp
                        {{ Form::open(array('url' => $route, 'method' => 'post', 'value' => 'PATCH', 'id' => 'add_route', 'files' => true, 'autocomplete' => 'off')) }}
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
                            </div>
                            <div class="column is-4">

                                <div class="field">
                                    {{ Form::label('invoice_no', 'Invoice No', array('class' => 'label')) }}
                                    <div class="control">
                                        {{ Form::text('invoice_no', $invoiceNo ?? NULL, ['required', 'class' => 'input is-small', 'placeholder' => 'Enter invoice no...']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="column is-4">
                                <div class="field">
                                    {{ Form::label('invoice_date', 'Invoice Date', array('class' => 'label')) }}
                                    <div class="control">
                                        {{ Form::text('invoice_date', $invoiceDate ?? NULL, ['required', 'class' => 'input is-small', 'id' => 'datepicker', 'placeholder' => 'Enter invoice date...']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(!empty($invoiceNo))
                            <div class="my-2">
                                <a class="tag is-success" sstyle="float: right" id="add_more_btn">Add More</a>
                            </div>
                        @endif
                        <div id="siteBox">

                        @if(!empty($invoiceSites))
                            <!-- For Edit -->
                                @foreach($invoiceSites as $invoicesite)
                                    <div class="columns">
                                        <div class="column is-4">
                                            <div class="field">
                                                {{ Form::label('sites', 'Select a site', array('class' => 'label')) }}
                                                @php $sites = \Tritiyo\Site\Models\Site::where('id', $invoicesite->site_id)->latest()->first() @endphp
                                                <select class="input is-small" name="site_id[]" id="site_select1">
                                                    <option value="{{$sites->id}}">{{$sites->site_code}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="column is-4">
                                            <div class="field">
                                                {{ Form::label('invoice_amount', 'Invoice Amount', array('class' => 'label')) }}
                                                <div class="control">
                                                    {{ Form::text('invoice_amount[]', $invoicesite->invoice_amount ?? NULL, ['required', 'class' => 'input is-small', 'placeholder' => 'Enter invoice amount...']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="column is-3">
                                            <div class="field">
                                                {{ Form::label('invoice_type', 'Invoice Type', array('class' => 'label')) }}
                                                <div class="control">
                                                    <?php $invoice_types = ['' => 'Select a invoice type', 'Partial' => 'Partial', 'Full' => 'Full']; ?>
                                                    {{ Form::select('invoice_type[]', $invoice_types, $invoicesite->invoice_type ?? NULL, ['class' => 'input is-small', 'required']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="column is-1">
                                            <label>&nbsp;<br></label>
                                            <a class="tag is-danger" id="delete_btn">Delete</a>

                                        </div>
                                    </div>
                                @endforeach

                            <!-- End For Edit -->
                            @else
                             <div class="columns r-1" id ='r'>
                                <div class="column is-4 ar-1">
                                    <div class="field">
                                        {{ Form::label('sites', 'Select a site', array('class' => 'label')) }}
                                        @php
                                      			$sites = \Tritiyo\Site\Models\Site::latest()->get()
                                      	@endphp
                                        <select class="input is-small" name="site_id[]" id="site_select1">

                                        </select>
                                    </div>
                                </div>
                                <div class="column is-4 ar-1">
                                    <div class="field">
                                        {{ Form::label('invoice_amount', 'Invoice Amount', array('class' => 'label')) }}
                                        <div class="control">
                                            {{ Form::text('invoice_amount[]', $site->invoice_amount ?? NULL, ['required', 'class' => 'input is-small', 'placeholder' => 'Enter invoice amount...']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-3 ar-1">
                                    <div class="field">
                                        {{ Form::label('invoice_type', 'Invoice Type', array('class' => 'label')) }}
                                        <div class="control">
                                            <?php $invoice_types = ['' => 'Select a invoice type', 'Partial' => 'Partial', 'Full' => 'Full']; ?>
                                            {{ Form::select('invoice_type[]', $invoice_types, $site->invoice_type ?? NULL, ['class' => 'input is-small', 'id'=>'site_invoice1', 'required']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-1">
                                    <label>&nbsp;<br></label>
                                    <a class="tag is-success" id="add_more_btn">Add More</a>
                                </div>
                            </div>
                            @endif
                        </div>
                      <div id="append_row">
                      	
                      </div>
                        <div class="columns">
                            <div class="column">
                                <div class="field is-grouped">
                                    <div class="control">
                                        <button type="submit" class="button is-success is-small">Save Changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                      

                        {{ Form::close() }}
                    </div>
                </div>

                @if(!empty($invoiceSites))
                @else
                <div class="columns">
                    <div class="column is-12">


                        <div style="margin-top: 20px;">
                            <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                <tbody>
                                <tr>
                                    <th>Invoice No</th>
                                    <th>Project</th>
                                    <th>Amount</th>
                                    <th>Invoice Date</th>
                                    <th>Action</th>
                                </tr>

                                @php
                                    $site_invoices = \Tritiyo\Site\Models\SiteInvoice::orderBy('invoice_date', 'desc')->groupBy('invoice_no')->get();
                                @endphp
                                @foreach($site_invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->invoice_no }}</td>
                                        <td>{{ \Tritiyo\Project\Models\Project::where('id', $invoice->project_id)->first()->name ?? NULL }}</td>
                                        <td>{{ \Tritiyo\Site\Models\SiteInvoice::where('invoice_no', $invoice->invoice_no)->get()->sum('invoice_amount') }}</td>
                                        <td>{{ $invoice->invoice_date }}</td>
                                        <td>
                                            @if( $invoice->is_verified == 1 )
                                                <div class="tag is-warning">Verified</div>
                                            @else
                                            <a href="{{route('multiple.site.invoice.edit', $invoice->invoice_no)}}" class="tag is-success is-small">Edit</a>
                                            <a href="{{route('multiple.site.invoice.verify', $invoice->invoice_no)}}" class="tag is-info is-small">Verify</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>


                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    </div>

@endsection


@section('cusjs')

    <script xsrc="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>

        function pr(arg, arg2 = 1, siteid = 0){
            $.ajax({
                method: 'GET',
                url   : "{{route('project.based.site', ['', ''])}}/"+arg+"/"+siteid,
                success : function (data){
                    //console.log(data);
                    let siteList = {};
                    siteList += '<option></option>';
                    for(let i = 0; i < data.length; i++){
                        siteList += '<option class="s'+arg2+data[i].id+'" value="'+data[i].id+'">'+data[i].site_code+'</option>';
                        //console.log(data[i].site_code)
                    }
                    $('#site_select'+arg2).empty().append(siteList);
                }
            })
        }

    </script>

    <script>
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
                              <select class="input is-small site-select" name="site_id[]"  id="site_select${counter}">
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
                                  <?php $invoice_types = ['' => 'Select a invoice type', 'Partial' => 'Partial', 'Full' => 'Full']; ?>
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


    </script>





    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

    <script>

         // Select 2
         function siteSelectRefresh(arg = 1) {
             $('select#site_select'+arg).select2({
                 placeholder: "Select Site",
                 allowClear: true,
             });
         }
         //siteSelectRefresh()
    </script>


    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script>
        $(function () {
            $("#datepicker").datepicker({minDate: -20, maxDate: "+1M +15D", dateFormat: 'yy-mm-dd'});
        });
    </script>
@endsection
