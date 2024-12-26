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
                        Sites Report of Project Manager
                </header>

                <div class="card-content">
                    <div class="card-data">
                        <form method="post" action="{{ route('excel.manager.site.report.download') }}">
                            @csrf

                            <div class="field has-addons">
                                <div class="control">
                                    <div class="field">                                          
                                          @php
                                              $managers = \App\Models\User::where('role', 3)->latest()->get();
                                          @endphp
                                          <select class="input is-small" name="manager_id" id="project_select">
                                              <option value="">Select a manager</option>
                                              @foreach($managers as $manager)
                                                  <option value="{{ $manager->id }}" {{!empty($manager_id) && $manager_id == $manager->id ? 'selected': ''}} >{{$manager->name }}</option>
                                              @endforeach
                                          </select>
                                      </div>
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
