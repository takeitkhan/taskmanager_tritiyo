@extends('layouts.app')

@section('title')
    Add Mobile Bill
@endsection

@section('column_left')

    <?php
    if(request()->get('manager')){
        $dates = explode(' - ', request()->get('daterange'));
        $start = $dates[0];
        $end = $dates[1];
        $bills = \Tritiyo\Project\Models\MobileBill::orderBy('id', "DESC")->where('manager_id', request()->get('manager'))->whereBetween('received_date', [$start, $end])->paginate('50');
    } elseif(request()->get('daterange')){
        $dates = explode(' - ', request()->get('daterange'));
        $start = $dates[0];
        $end = $dates[1];
        $bills = \Tritiyo\Project\Models\MobileBill::orderBy('id', "DESC")->whereBetween('received_date', [$start, $end])->paginate('50');
    } else {
        $bills = \Tritiyo\Project\Models\MobileBill::orderBy('id', "DESC")->paginate('50');
    }
    ?>

    <article class="panel is-primary">
        <p class="panel-tabs">
            <a class="is-active">Add Mobile Bill</a>
        </p>
        <div class="customContainer">
            <form action="{{route('projects.add.mobile.bill.store')}}" method="post">
                @csrf
                <div id="mobile_bill_form">
                  <div class="columns" id="default_load">
                        <div class="column is-1">
                            <label></label> <br />
                            <a>  
                                <span style="cursor: pointer;" class="tag is-success" id="addrow">Add More &nbsp;</span>
                            </a>
                        </div>
                  </div>
                </div>
                <div class="columns">
                    <div class="column">
                        <div class="field is-grouped">
                            <div class="control">
                                <button id="task_create_btn" class="button is-success is-small">Save Changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="m-3">
            <div class="level">
                <div class="level-left">
                    <strong>Mobile Bill List</strong>
                </div>
                <div class="level-right">
                    <div class="level-item ">

                        <form method="get" action="{{route('projects.add.mobile.bill')}}">
                            @csrf
                            <div class="field has-addons">
                                <a href="{{ route('download_excel_mobile_bill') }}?manager={{ request()->get('manager') }}&daterange={{ request()->get('daterange') ??  date('Y-m-d', strtotime(date('Y-m-d'). ' - 30 days')) . ' - ' . date('Y-m-d') }}"
                                   class="button is-primary is-small">
                                    Download as excel
                                </a>
                                <div class="control mx-2">
                                    <?php $managers = \App\Models\User::where('role', '3')->where('employee_status', 'Enroll')->get(); ?>
                                    <select id="manager_search" class="input" name="manager">
                                        <option></option>
                                        @foreach($managers as $manager)
                                            <option value="{{$manager->id}}" {{$manager->id == request()->get('manager') ? 'selected' : NULL}}>{{$manager->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="control">
                                    <input class="input is-small" type="text" name="daterange" id="textboxID" autocomplete="off"
                                           value="{{ request()->get('daterange') ?? null }}">
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
            </div>

            <table class="bw_table">
            <tr>
                <th>Manager Name</th>
               	<th>Project Name</th>
                <th>Mobile Number</th>
                <th>Recieved Amount</th>
                <th>Recieved Date</th>
            </tr>
            @php $totalBill = []; @endphp
            @foreach($bills as $bill)
                <tr>
                    <td>{{\App\Models\User::where('id', $bill->manager_id)->first()->name}}</td>
                   <td>{{\Tritiyo\Project\Models\Project::where('id', $bill->project_id)->first()->name}}</td>
                    <td>{{$bill->mobile_number}}</td>
                    <td>{{$totalBill []=$bill->received_amount}}</td>
                    <td>{{$bill->received_date}}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2"></td>
                <td>Total: {{array_sum($totalBill)}}</td>
                <td></td>
            </tr>
        </table>
        </div>

        <div class="pagination_wrap pagination">

            @if(Request::get('key'))
                {{ $bills->appends(['key' => Request::get('key')])->links('pagination::bootstrap-4') }}
            @else
                {{$bills->links('pagination::bootstrap-4')}}
            @endif
        </div>
        </div>
    </article>


@endsection
@section('column_right')

@endsection
@section('cus_js')
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(function () {
            $("#textboxID").datepicker({minDate: -20, maxDate: "+1M +15D", dateFormat: 'yy-mm-dd'});
        });

        const btn = document.querySelector('select#show_page_completion_status');
        btn.onchange = function () {
            if (btn.value == 'Pending') {
                document.querySelector('#show_note').setAttribute('style', 'display:block')
            } else {
                document.querySelector('#show_note').setAttribute('style', 'display:none')
                document.querySelector('#show_page_pending_note').value = null;
            }
        }


    </script>
    <script>
        $('#manager_select').select2({
            placeholder: "Select Head of Site",
            // /allowClear: true
        });
        $('#manager_search').select2({
            placeholder: "Select Head of Site",
            // /allowClear: true
        });
    </script>
    <style type="text/css">
        .table.is-fullwidth {
            width: 100%;
            font-size: 15px;
            text-align: center;
        }
    </style>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
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





<script type="text/template" data-template="mobile_bill_form">

    
        {{-- <div class="column is-3">

            <div class="field">
                {{ Form::label('manager', 'Project Manager', array('class' => 'label')) }}
                <div class="control">
                    <?php $managers = \App\Models\User::where('role', '3')->where('employee_status', 'Enroll')->get(); ?>
                    <select id="manager_select" class="input" name="manager_id" required>
                        <option></option>
                        @foreach($managers as $manager)
                            <option value="{{$manager->id}}">{{$manager->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div> --}}
        
        <div class="column is-3">
            <div class="field">
                {{ Form::label('project', 'Project', array('class' => 'label')) }}
                <div class="control">
                    <?php  $projects = \Tritiyo\Project\Models\Project::get();?>
                    <select name="project_id[]" id="" class="input is-small">
                        <option value="" selected disabled>Select one</option>
                        @foreach ($projects as $project)
                        <?php 
                            $check = DB::table('project_ranges')->where('project_id', $project->id)
                                        ->orderBy('id', 'desc')
                                        ->first();
                        ?>
                        @if(!empty($check) && $check->project_status == 'Inactive')
                        @else
                        <option value="{{$project->id}}">{{$project->name}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="column is-3">
            <div class="field">
                {{ Form::label('mobile_number', 'Mobile Number', array('class' => 'label')) }}
                <div class="control">
                    <input type="text" name="mobile_number[]" value="" class="input is-small" required>
                </div>
            </div>
        </div>

        <div class="column is-3">
            <div class="field">
                {{ Form::label('received_amount', 'Amount', array('class' => 'label')) }}
                <div class="control">
                    <input type="text" name="received_amount[]" value="" class="input is-small" required>
                </div>
            </div>
        </div>

</script>







    <script>
        //Breakdown
            var HTML = $('script[data-template="mobile_bill_form"]').html();
            $("div#default_load").prepend(HTML); 
            $("#addrow").on("click", function() {
                fieldHTML = '<div class="columns">'
                fieldHTML += $('script[data-template="mobile_bill_form"]').html();
                fieldHTML += '<div class="column is-1">\
                                <label></label> <br />\
                                <a><span class="tag is-danger is-small ibtnDel">Delete</span></a>\
                                </div>';
                fieldHTML += '</div>';
                $("div#mobile_bill_form").append(fieldHTML);
                counter++;
            });

            $("div#mobile_bill_form").on("click", ".ibtnDel", function(event) {
                $(this).closest("div.columns").remove();
                counter -= 1
            });




    </script>




@endsection
