@extends('layouts.app')
@section('column_left')

    <div class="columns is-vcentered  pt-2">
        <div class="column is-6 mx-auto">
            <div class="card tile is-child xquick_view">
                <header class="card-header">
                    <p class="card-header-title">
                    <span class="icon">
                        <i class="fas fa-tasks default"></i>
                    </span>
                        Report of sites
                </header>

                <div class="card-content">
                    <div class="card-data">
                        <form method="post" action="{{ route('download_excel_site_accountant') }}">
                            @csrf

                            <div class="field has-addons">
                                <div class="control">
                                    <input class="input is-small" type="text" name="daterange" id="textboxID"
                                           value="{{ $date ?? null }}">
                                </div>
                                <div class="control">
                                    <?php
                                    $projects = \Tritiyo\Project\Models\Project::pluck('name', 'id');//->prepend('Select Project', '');
                                    //dd($projects);
                                    ?>
                                    <select name="project_id" class="select" id="project_select" required>
                                        @foreach($projects as $key => $project)
                                            <option
                                                value="{{ $key ?? NULL }}"
                                                {{ (!empty($task->project_id) && ($project->id == $task->project_id)) ? 'selected="selected"' : NULL }}>
                                                {{ $project ?? NULL }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="control">
                                    <input name="search" type="submit"
                                           class="button is-small is-primary has-background-primary-dark"
                                           value="Download As Excel"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <div class="card-content">
                    <div class="card-data">
                        <?php
                        //$shellexec = shell_exec('getmac');
                        //echo $shellexec;
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection


@section('cusjs')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
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
@endsection
