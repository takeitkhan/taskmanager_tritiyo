@extends('layouts.app')


@section('column_left')
    <?php
    $today = date('Y-m-d');
   


    $resourcesAvailable = \DB::select("SELECT * FROM (SELECT *,
                                                (SELECT site_head FROM tasks WHERE tasks.site_head = users.id AND tasks.task_for = '$today') AS site_head,
                                                (SELECT user_id FROM tasks WHERE tasks.site_head = users.id AND tasks.task_for = '$today') AS manager,
                                                (SELECT resource_id FROM tasks_site WHERE tasks_site.resource_id = users.id AND tasks_site.task_for = '$today' GROUP BY tasks_site.site_id LIMIT 0,1) AS resource_used,
                                                users.id AS useriddddd
                                        FROM users WHERE users.role = 2 AND users.employee_status  NOT IN ( 'Terminated', 'Left Job', 'Long Leave', 'On Hold')
                                    ) AS mm WHERE mm.site_head IS NULL AND mm.resource_used IS NULL");


    $dateForEmergency = date('Y-m-d');



    /*  Nipun */

    $siteHeadBookedForEmergency = \App\Models\User::leftjoin('tasks', 'users.id', 'tasks.site_head')
                                                ->select('users.id', 'users.name', 'users.designation', 'users.department', 'tasks.site_head as site_head')
                                                ->where('tasks.task_for' ,  $dateForEmergency)
                                                  ->where('tasks.task_type' ,  'emergency')
                                                ->where('users.role', 2)
                                                ->get();


    $resourcesBookedForEmergency = \App\Models\User::leftjoin('tasks_site', 'users.id', 'tasks_site.resource_id')
                                                ->select('users.id', 'users.name', 'users.department', 'users.designation')
                                                ->where('tasks_site.task_for' ,  $dateForEmergency)
                                                  ->where('tasks_site.task_type' ,  'emergency')
                                                ->where('users.role', 2)
                                               ->groupBy('tasks_site.resource_id')
                                                ->get();

                                                  //dd($resourcesBookedForEmergency);


                                    //End

    $dateForGeneral = date("Y-m-d", strtotime("+1 day"));



   /*  Nipun */

        $siteHeadBookedForGeneral = \App\Models\User::leftjoin('tasks', 'users.id', 'tasks.site_head')
                                                    ->select('users.id', 'users.name', 'users.designation', 'users.department',  'tasks.site_head as site_head')
                                                    ->where('tasks.task_for' ,  $dateForGeneral)
                                                      ->where('tasks.task_type' ,  'general')
                                                    ->where('users.role', 2)
                                                    ->get();


        $resourcesBookedForGeneral = \App\Models\User::leftjoin('tasks_site', 'users.id', 'tasks_site.resource_id')
                                                    ->select('users.id', 'users.name', 'users.designation', 'users.department')
                                                    ->where('tasks_site.task_for' ,  $dateForGeneral)
                                                      ->where('tasks_site.task_type' ,  'general')
                                                    ->where('users.role', 2)
                                                   ->groupBy('tasks_site.resource_id')
                                                    ->get();

       





        /* this day booked for general */
        $thisDateForGeneral = date('Y-m-d');

        $thisDateSiteHeadBookedForGeneral = \App\Models\User::leftjoin('tasks', 'users.id', 'tasks.site_head')
        ->select('users.id', 'users.name', 'users.designation', 'users.department',  'tasks.site_head as site_head')
        ->where('tasks.task_for' ,  $thisDateForGeneral)
        ->where('tasks.task_type' ,  'general')
        ->where('users.role', 2)
        ->get();


        $thisDateResourcesBookedForGeneral = \App\Models\User::leftjoin('tasks_site', 'users.id', 'tasks_site.resource_id')
        ->select('users.id', 'users.name', 'users.designation', 'users.department')
        ->where('tasks_site.task_for' ,  $thisDateForGeneral)
        ->where('tasks_site.task_type' ,  'general')
        ->where('users.role', 2)
        ->groupBy('tasks_site.resource_id')
        ->get();



        //End

    ?>
    <div class="columns is-vcentered  pt-2">
        <div class="column is-12 mx-auto">
            <div class="card tile is-child xquick_view">
                <div class="card-content">
                    <div class="card-data">
                            <div class="columns">
                            <!--  Available Resource / Site Head  General Task-->
                                <div class="column is-3 mx-auto">
                                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                        <tr>
                                            <th class="has-background-primary-dark" colspan="5">A Resource or Site Head is Avaialble for today</th>
                                        </tr>
                                        <tr>
                                          <th class="has-background-primary-info">S/N</th>
                                            <th class="has-background-primary-info">Name</th>
                                            <th class="has-background-primary-info">Designation</th>
                                          <th class="has-background-primary-info">Department</th>
                                           <th class="has-background-primary-info">Status</th>
                                        </tr>
                                        @foreach($resourcesAvailable as $key => $user)
                                            <tr>
                                              <td>{{++$key}}</td>
                                                <td style="">
                                                   <a target="_blank" href="{{route('hidtory.user', $user->id)}}">{{ $user->name }}</a>
                                                </td>
                                                <td style="">
                                                    {{ \DB::table('designations')->where('id', $user->designation)->first()->name }}
                                                </td>
                                                 <td>  {{$user->department ?? NULL}} </td>
                                              <td>  {{$user->employee_status ?? NULL}} </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>

                              <!--- Booked  Resource / SIte Head Emergency Task  -->
                                <div class="column is-3 mx-auto">
                                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                        <tr>
                                            <th class="has-background-danger-dark" colspan="5">A Resource or Site Head is Booked for {{$dateForEmergency}}   -  Emergency</th>
                                        </tr>
                                        <tr>
                                              <th class="has-background-primary-info" style="width: 10px;">S/N</th>
                                            <th class="has-background-primary-info">Name</th>
                                            <th class="has-background-primary-info">Designation</th>
                                           <th class="has-background-primary-info">Department</th>
                                              <th class="has-background-primary-info">Role</th>
                                        </tr>
                                      <!--- Booked  Site Head  Emergency Task  -->
                                      @foreach($siteHeadBookedForEmergency as $key =>  $user)
                                            <tr>
                                              <td>{{++$key}}</td>
                                                <td style="">
                                                    <a target="_blank" href="{{route('hidtory.user', $user->id)}}">{{ $user->name }}</a>
                                                </td>
                                                <td style="">
                                                    {{ \DB::table('designations')->where('id', $user->designation)->first()->name }}
                                                </td>

                                                  <td>  {{$user->department ?? NULL}} </td>
                                                <td>
                                                	<?php
                                                    	if(!empty($user->site_head)){
                                                        	echo 'Site Head';
                                                        } else {
                                                        	echo 'Resource';
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                        @endforeach
                                      	<!--- Booked  Resource  Emergency Task  -->
                                        @foreach($resourcesBookedForEmergency as $key => $user)
                                            <tr>
                                              <td> {{count($siteHeadBookedForEmergency) + (++$key) }} </td>
                                                <td style="">
                                                    <a target="_blank" href="{{route('hidtory.user', $user->id)}}">{{ $user->name }}</a>
                                                </td>
                                                <td style="">
                                                    {{ \DB::table('designations')->where('id', $user->designation)->first()->name }}
                                                </td>
                                              <td>  {{$user->department ?? NULL}} </td>
                                                <td>
                                                	<?php
                                                    	if(!empty($user->site_head)){
                                                        	echo 'Site Head';
                                                        } else {
                                                        	echo 'Resource';
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>

                                <!-- This Date Booked Resource / Site Head  General Task-->

                                <div class="column is-3 mx-auto">
                                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                        <tr>
                                            <th class="has-background-warning-dark" colspan="5">A Resource or Site Head is Booked for {{$thisDateForGeneral}}  -  General</th>
                                        </tr>
                                        <tr>
                                            <th class="has-background-primary-info">S/N</th>
                                            <th class="has-background-primary-info">Name</th>
                                            <th class="has-background-primary-info">Designation</th>
                                            <th class="has-background-primary-info">Department</th>
                                            <th class="has-background-primary-info">Role</th>
                                        </tr>
                                        <!--- This Date Booked  Site Head General Task  -->
                                        @foreach($thisDateSiteHeadBookedForGeneral as $key => $user)
                                            <tr>
                                                <td>{{++$key}}</td>
                                                <td style="">
                                                    <a target="_blank" href="{{route('hidtory.user', $user->id)}}">{{ $user->name }}</a>
                                                </td>
                                                <td style="">
                                                    {{ \DB::table('designations')->where('id', $user->designation)->first()->name }}
                                                </td>
                                                <td>  {{$user->department ?? NULL}} </td>
                                                <td>
                                                    <?php
                                                    if(!empty($user->site_head)){
                                                        echo 'Site Head';
                                                    } else {
                                                        echo 'Resource';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        @endforeach

                                    <!--- This Date Booked  Resource General Task  -->
                                        @foreach($thisDateResourcesBookedForGeneral as $key => $user)
                                            <tr>
                                                <td> {{count($siteHeadBookedForGeneral) + (++$key) }} </td>
                                                <td style="">
                                                    <a target="_blank" href="{{route('hidtory.user', $user->id)}}">{{ $user->name }}</a>
                                                </td>
                                                <td style="">
                                                    {{ \DB::table('designations')->where('id', $user->designation)->first()->name }}
                                                </td>
                                                <td>  {{$user->department ?? NULL}} </td>
                                                <td>
                                                    <?php
                                                    if(!empty($user->site_head)){
                                                        echo 'Site Head';
                                                    } else {
                                                        echo 'Resource';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>

                              	<!--  Booked Resource / Site Head  General Task-->
                                <div class="column is-3 mx-auto">
                                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                        <tr>
                                            <th class="has-background-link-dark" colspan="5">A Resource or Site Head is Booked for {{$dateForGeneral}}  -  General</th>
                                        </tr>
                                        <tr>
                                          	<th class="has-background-primary-info">S/N</th>
                                            <th class="has-background-primary-info">Name</th>
                                            <th class="has-background-primary-info">Designation</th>
                                            <th class="has-background-primary-info">Department</th>
                                             <th class="has-background-primary-info">Role</th>
                                        </tr>
                                       <!--- Booked  Site Head General Task  -->
                                      @foreach($siteHeadBookedForGeneral as $key => $user)
                                            <tr>
                                              <td>{{++$key}}</td>
                                                <td style="">
                                                    <a target="_blank" href="{{route('hidtory.user', $user->id)}}">{{ $user->name }}</a>
                                                </td>
                                                <td style="">
                                                    {{ \DB::table('designations')->where('id', $user->designation)->first()->name }}
                                                </td>
                                                  <td>  {{$user->department ?? NULL}} </td>
                                                <td>
                                                	<?php
                                                    	if(!empty($user->site_head)){
                                                        	echo 'Site Head';
                                                        } else {
                                                        	echo 'Resource';
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                        @endforeach

                                      <!--- Booked  Resource General Task  -->
                                        @foreach($resourcesBookedForGeneral as $key => $user)
                                            <tr>
                                              <td> {{count($siteHeadBookedForGeneral) + (++$key) }} </td>
                                                <td style="">
                                                    <a target="_blank" href="{{route('hidtory.user', $user->id)}}">{{ $user->name }}</a>
                                                </td>
                                                <td style="">
                                                    {{ \DB::table('designations')->where('id', $user->designation)->first()->name }}
                                                </td>
                                                  <td>  {{$user->department ?? NULL}} </td>
                                                <td>
                                                	<?php
                                                    	if(!empty($user->site_head)){
                                                        	echo 'Site Head';
                                                        } else {
                                                        	echo 'Resource';
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>


                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection




@section('cusjs')
    <style>
        .columns .column .is-3 {
            display: unset;
            display: unset;
            flex-wrap: unset;
        }
    </style>
@endsection
