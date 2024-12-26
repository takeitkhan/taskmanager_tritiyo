<div class="card tile is-child">
    <header class="card-header">
        <p class="card-header-title">
            <span class="icon">
                <i class="fas fa-file-invoice"></i>
            </span>
            Site Invoice History
        </p>
    </header>
    <div class="card-content">
        <div class="card-data">
            <div class="columns">
                <div class="column">
                    <br/>
                    {{ Form::open(array('url' => route('site_invoices.store'), 'method' => 'post', 'value' => 'PATCH', 'id' => 'add_route', 'files' => true, 'autocomplete' => 'off')) }}
                    @csrf
                    <div class="columns">
                      
                      <?php
                      /**
                      
                      id
                      user_id
                      site_id	int
                      project_id
                      range_id
                      range_status_key
                      invoice_no
                      invoice_amount
                      invoice_date
                      invoice_type
                      status_key
                      is_verified

                      **/
                      ?>

                        <div class="column is-12">
                          	<div class="field">
                              	@php
                              			$ranges = Tritiyo\Project\Helpers\ProjectHelper::get_all_ranges_by_project_id($site->project_id);
                              	@endphp
                                {{ Form::label('projects', 'Select a range', array('class' => 'label')) }}
                                <select class="input is-small" name="range_status_key"  id="range_select" required>
                                  	@foreach($ranges as $range)                                  			
                                  			<option value="{{ $range->status_key }}">{{ $range->start_date }} - {{ $range->end_date ?? date('Y-m-d') }}</option>
                                  	@endforeach
                                </select>
                            </div>
                          
                          	<div class="field">
                                {{ Form::label('invoice_no', 'Invoice No', array('class' => 'label')) }}
                                <div class="control">
                                    {{ Form::hidden('site_id', $site->id ?? NULL, ['required']) }}
                                    {{ Form::hidden('project_id', $site->project_id ?? NULL, ['required']) }}
                                    {{ Form::text('invoice_no', $site->invoice_no ?? NULL, ['required', 'class' => 'input is-small', 'placeholder' => 'Enter invoice no...']) }}
                                </div>
                            </div>
                            <div class="field">
                                {{ Form::label('invoice_amount', 'Invoice Amount', array('class' => 'label')) }}
                                <div class="control">
                                    {{ Form::text('invoice_amount', $site->invoice_amount ?? NULL, ['required', 'class' => 'input is-small', 'placeholder' => 'Enter invoice amount...']) }}
                                </div>
                            </div>
                            <div class="field">
                                {{ Form::label('invoice_date', 'Invoice Date', array('class' => 'label')) }}
                                <div class="control">
                                    {{ Form::text('invoice_date', $site->invoice_date ?? NULL, ['required', 'class' => 'input is-small', 'id' => 'datepicker', 'placeholder' => 'Enter invoice date...']) }}
                                </div>
                            </div>
                          	<div class="field">
                                {{ Form::label('invoice_powo', 'Invoice PO/WO', array('class' => 'label')) }}
                                <div class="control">
                                    {{ Form::text('invoice_powo', $site->invoice_powo ?? NULL, ['required', 'class' => 'input is-small',  'placeholder' => 'Enter invoice PO/WO...']) }}
                                </div>
                            </div>
                            <div class="field">
                                {{ Form::label('invoice_type', 'Invoice Type', array('class' => 'label')) }}
                                <div class="control">
                                    <?php $invoice_types = ['' => 'Select a invoice type', 'Partial' => 'Partial', 'Full' => 'Full']; ?>
                                    {{ Form::select('invoice_type', $invoice_types, $site->invoice_type ?? NULL, ['class' => 'input is-small', 'required']) }}
                                </div>
                            </div>
                        </div>

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


            <div class="columns">
                <div class="column is-12">


                    <div style="margin-top: 20px;">
                        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                            <tbody>
                            <tr>
                                <th>Site ID</th>
                              	<th>On Range</th>
                                <th>Invoice No</th>
                                <th>Amount</th>
                                <th>Invoice Date</th>
                                <th>Invoice Type</th>
                            </tr>

                            @php
                                $site_invoices = \Tritiyo\Site\Models\SiteInvoice::where('site_id', $site->id)->orderBy('id', 'desc')->get();
                              	$total_amount = [];
                            @endphp
                            @foreach($site_invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->site_id }}</td>
                                  	<td>
                                      		{{ Tritiyo\Project\Models\ProjectRange::where('status_key', $invoice->range_status_key)->where('project_status', 'Active')->first()->status_update_date }} - 
                                      		{{ Tritiyo\Project\Models\ProjectRange::where('status_key', $invoice->range_status_key)->where('project_status', 'Inactive')->first()->status_update_date ?? date('Y-m-d') }}
                                  	</td>
                                    <td>{{ $invoice->invoice_no }}</td>
                                    <td>
                                      {{ $total_amount[] = $invoice->invoice_amount }}
                                  	</td>
                                    <td>{{ $invoice->invoice_date }}</td>
                                    <td>{{ $invoice->invoice_type }}</td>
                                </tr>
                            @endforeach
                              	<tr>
                                  <td colspan="3">Total Amount</td>
                                  <td>{{ array_sum($total_amount) }}</td>
                                  <td colspan="2">&nbsp;</td>
                              	</tr>
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>