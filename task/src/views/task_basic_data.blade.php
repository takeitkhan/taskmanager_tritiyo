{{--{!! Tritiyo\Task\Helpers\TaskHelper::actionHelper('task_approver_edited', true, true)  !!}--}}
@php
    $task_sites = DB::select('SELECT site_id FROM `tasks_site` WHERE task_id = '. $task->id .' GROUP BY site_id');
    $task_resources = DB::select('SELECT resource_id FROM `tasks_site` WHERE task_id = '. $task->id .' GROUP BY resource_id');
    $task_vehicle = \Tritiyo\Task\Models\TaskVehicle::where('task_id', $task->id)->get();
    $task_material = \Tritiyo\Task\Models\TaskMaterial::where('task_id', $task->id)->get();
@endphp
<div class="card tile is-child xquick_view pt-0">

    <header class="card-header">
        <p class="card-header-title">
            <span class="icon"><i class="fas fa-tasks default"></i></span>
                Task General Information
                @if(request()->routeIs('tasks.show', $task->id))
                    @if(auth()->user()->isResource(auth()->user()->id))

                    @else
                        <a href="{{ route('tasks.edit', $task->id) }}?task_id={{ $task->id }}&information=taskinformation"  class="tag is-link" style="position: absolute; right: 30px;">
                            Edit
                        </a>
                    @endif
                @endif
        </p>

         @if(auth()->user()->isAdmin(auth()->user()->id))
                		  {!! delete_data('tasks.destroy',  $task->id, 'Only admin can delete') !!}
                @endif
    </header>

    <div class="card-content">
        <div class="card-data">
            <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">

                <tr>
                    <td><strong>Task Type</strong></td>
                    <td><span class="tag is-info">{{ ucwords($task->task_type) ?? NULL }}</span></td>
                    <td>
                        <strong>Task Name</strong>
                    <br/>
                      <strong>Task ID</strong> <br/>
                      <strong>Task Code</strong>
                    </td>
                    <td>
                        {{ $task->task_name ?? NULL }} <br/>
                        {{ $task->id ?? NULL }} <br/>
                        {{ $task->task_code ?? NULL }}

                    </td>

                </tr>
                <tr>
                    <td><strong>Site Head</strong></td>
                    <td>
                        <span class="has-text-info">
                          	<?php
                          		if(!empty($task->site_head)) {
                            ?>
                            <a href="{{ route('hidtory.user', $task->site_head) }}" target="_blank">
                                {{ \App\Models\User::where('id', $task->site_head)->first()->name ?? NULL }} ({{ $task->site_head ?? NULL }})
                            </a>
                          	<?php } ?>
                        </span>
                    </td>

                </tr>
                <tr>
                    <td><strong>Task Created Time</strong></td>
                    <td>{{ $task->created_at }}</td>
                    <td><strong>Task Created For</strong></td>
                    <td>{{ $task->task_for ?? NULL }}</td>
                </tr>
                <tr>
                    <td><strong>Project Name</strong></td>
                    <td>
                        <a href="{{ route('projects.show', $task->project_id) }}" target="_blank">
                            {{ \Tritiyo\Project\Models\Project::where('id', $task->project_id)->first()->name }}
                        </a>
                    </td>
                    <td><strong>Project Manager</strong></td>
                    <td>
                      <a href="{{ route('hidtory.user', $task->user_id) }}" target="_blank">
                      {{ \App\Models\User::where('id', $task->user_id)->first()->name }}
                      </a>
                  </td>
                </tr>

                @if(auth()->user()->isManager(auth()->user()->id) ||auth()->user()->isApprover(auth()->user()->id) || auth()->user()->isCFO(auth()->user()->id) || auth()->user()->isAccountant(auth()->user()->id))
                <tr>
                    <td colspan="4" style="background: #b5eace">
                        Remaining Balance:
                        <?php
/**                        $total = \Tritiyo\Project\Helpers\ProjectHelper::all_range_budgets($task->project_id);
                        $mobileBill = number_format(Tritiyo\Project\Models\MobileBill::where('project_id', $task->project_id)->get()->sum('received_amount'), 2);
                        $budgetuse = \Tritiyo\Project\Helpers\ProjectHelper::used_range_budgets($task->project_id);
                       echo $total - ($budgetuse + $mobileBill);
      **/
                                  $task_id = $task->id;
                                  $task = \Tritiyo\Task\Models\Task::where('id', $task_id)->first();
                      				//dd($task);
                                //   /** Current Range Budget **/
                                //   $total_budget = DB::select("SELECT SUM(budget_amount) AS total_budget FROM `project_budgets` WHERE `project_id` = '". $task->project_id ."' AND `current_range_id` = '". $task->current_range_id ."' ");
                                //   $current_range_total_budget = (float) $total_budget[0]->total_budget;


                                //   /** Current Range Usage **/
                                //   $total_usages_on_current_range = DB::select("SELECT SUM(reba_amount) AS reba_amount FROM `ttrb` WHERE  `project_id` = '". $task->project_id ."' AND `current_range_id` = '". $task->current_range_id ."' ");
                                //   $range_usage = (float)$total_usages_on_current_range[0]->reba_amount;

                                //   $total_mobile_bill_on_current_range = DB::select("SELECT SUM(received_amount) AS total_mobile_bill FROM `mobile_bill` WHERE `project_id` = '". $task->project_id ."' AND `range_id` = '". $task->current_range_id ."' ");
                                //   $mobile_bill = (float)$total_mobile_bill_on_current_range[0]->total_mobile_bill;

                                //   /** Actual Usage **/
                                //   $actual_usage =  $current_range_total_budget - ($range_usage + $mobile_bill);

                        ?>
                        {{-- BDT. {{ $actual_usage }} --}}
                       BDT 
                       {{-- {{\Tritiyo\Project\Helpers\ProjectHelper::remainingBalance($task->project_id, $task->current_range_id)}} --}}
                       @php
                        $remaining_balance = \Tritiyo\Project\Helpers\ProjectHelper::remainingBalance($task->project_id, $task->current_range_id);
                        $today_use = \Tritiyo\Task\Helpers\RequisitionData::todayManagerUsedAmount($task->project_id, $task->current_range_id);
                        echo $remaining_balance - $today_use;
                    @endphp
                    </td>

                </tr>
                @endif



                <tr>
                    <td colspan="4"><strong>Task Details</strong></td>
                </tr>
                <tr>
                    <td colspan="4">{{ $task->task_details ?? NULL }}</td>
                </tr>

                @if(!empty($task->anonymous_proof_details))
                    <tr>
                        <td colspan="4"><strong>Anonymous Proof</strong></td>
                    </tr>
                    <tr>
                        <td colspan="4">
                                <?php echo preg_replace("/[\n]/","<br/>", $task->anonymous_proof_details);?>
                        </td>
                    </tr>
                @endif


                @if(!empty($task_sites))
                    <tr>
                        <td colspan="4"><strong>Site and resource information</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            @foreach($task_sites as $data)
                                <a href="{{ route('sites.show', $data->site_id) }}" target="_blank">
                                    {{ \Tritiyo\Site\Models\Site::where('id', $data->site_id)->first()->site_code  }}
                                </a>
                                <br/>
                            @endforeach
                        </td>
                        <td colspan="2">
                            @foreach($task_resources as $data)
                          		<a href="{{ route('hidtory.user', $data->resource_id) }}" target="_blank">
                                	{{ \App\Models\User::where('id', $data->resource_id )->first()->name ?? NULL }}
                          		</a>
                                <br/>
                            @endforeach
                        </td>
                    </tr>
                @endif
                @if(count($task_vehicle) > 0 || count($task_material) > 0)
                    <tr>
                        <td colspan="2"><strong>Vehicle information</strong></td>
                        <td colspan="2"><strong>Material information</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                <tr>
                                    <td>Name</td>
                                    <td>Rent</td>
                                </tr>
                                @foreach($task_vehicle as $data)
                                    <tr>
                                        <td>{{ \Tritiyo\Vehicle\Models\Vehicle::where('id', $data->vehicle_id)->first()->name  }}</td>
                                        <td>{{ $data->vehicle_rent  }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                        <td colspan="2">
                            <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                <tr>
                                    <td>Material</td>
                                    <td>Qty</td>
                                    <td>Amount</td>
                                </tr>
                                @if(count($task_material) > 0)
                                    @foreach($task_material as $data)
                                        <tr>
                                            <td>{{ \Tritiyo\Material\Models\Material::where('id', $data->material_id)->first()->name  }}</td>
                                            <td>{{ $data->material_qty  }}</td>
                                            <td>{{ $data->material_amount  }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </table>
                        </td>
                    </tr>

                @endif
            </table>
        </div>
    </div>
</div>
