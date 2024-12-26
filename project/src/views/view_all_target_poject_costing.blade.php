
@extends('layouts.app')


@section('title')
    Target Costing of all Project
@endsection


<?php

?>
<section class="hero is-white borderBtmLight">

</section>
@section('column_left')
    <article class="panel is-primary">
        <p class="panel-tabs">
            <a class="is-active">Target Costing of all Project</a>
        </p>
        <div class="customContainer">
            <form action="{{route('view.all.target.projects.costing.kpi')}}" method="get">
                <div class="columns">
                    <div class="column is-2">
                        <div class="field">
                                <label for="" class="label">Filter by Project</label>
                                <select name="project_id" id="" class="input is-small">
                                    @php $projects = \Tritiyo\Project\Models\Project::get() @endphp
                                    <option value="">Select a project</option>
                                    @foreach($projects as $project)
                                        <option value="{{$project->id}}" {{request()->get('project_id') == $project->id ? 'selected' : ''}}>{{$project->name}}</option>
                                    @endforeach
                                </select>
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                                <label for="" class="label">Filter by Year</label>
                                <select name="year" id="" class="input is-small">
                                    @php $years = ['2021', '2022', '2023'] @endphp
                                    <option value="">All Year</option>
                                    @foreach($years as $year)
                                        <option value="{{$year}}" {{request()->get('year') == $year ? 'selected' : ''}}>{{$year}}</option>
                                    @endforeach
                                </select>
                        </div>
                    </div>
                    <div class="column is-1">
                        <label for="" class="label">&nbsp;</label>
                        <button type="submit" class="button is-small is-primary">Filter</button>
                    </div>
                </div>
            </form>

            <div class="columns">
                @php
                    $getTarget = \Tritiyo\Project\Models\TargetProjectKpi::orderBy('id', 'desc');

                        if(request()->get('project_id') && request()->get('year')){
                            $getTarget = $getTarget->where('project_id', request()->get('project_id'))->where('year', request()->get('year'));
                        } elseif(request()->get('project_id')){
                            $getTarget = $getTarget->where('project_id', request()->get('project_id'));
                        }elseif(request()->get('year')){
                            $getTarget = $getTarget->where('year', request()->get('year'));
                        }

                    $getTarget = $getTarget->groupBy('status_key')->get();
                    $totalProjectCosting = [];
                @endphp
                <table class="table is-fullwidth">
                    <tr>
                        <th>Project Name</th>
                        <th>Project Manager</th>
                        <th>Target Project Costing</th>
                        <th>Range</th>
                        <th>Year</th>
                    </tr>
                @foreach($getTarget as $data)
                    <tr>
                        <td>{{\Tritiyo\Project\Models\Project::where('id', $data->project_id)->first()->name }}</td>
                        <td>{{\App\Models\User::where('id', $data->manager)->first()->name }}</td>
                        <td>{{$totalProjectCosting []= $data->meta_value}}</td>
                        <td>{{$data->target_range}}</td>
                        <td>{{$data->year}}</td>
                    </tr>
                @endforeach
                    <tr>
                        <td colspan="2"></td>
                        <td>Total : {{array_sum($totalProjectCosting)}}</td>
                        <td colspan="2"></td>
                    </tr>
                </table>

            </div>
        </div>
    </article>
@endsection




@section('cusjs')


@endsection
