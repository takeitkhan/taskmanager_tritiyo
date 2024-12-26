@extends('layouts.app')

@section('title')
    Site import
@endsection

@section('column_left')

    <div class="card tile is-child">
        <header class="card-header">
            <p class="card-header-title">
                <span class="icon"><i class="fas fa-tasks default"></i></span>
                Site import
              <form method="post" action="{{route('sites.import.excel')}}" enctype="multipart/form-data" class="m-0">
                @csrf
                <a href="{{asset('/downloads/site-import-format-download.xlsx')}}">Excel Format Download</a>
                    <input type="submit" name="reset" value="Reset" class="button is-small is-warning mt-0">
              </form>
            </p>
        </header>
        <div class="card-content">
            <div class="control">
                <form method="post" action="{{route('sites.import.excel')}}" enctype="multipart/form-data">
                    @csrf
                    <p>Upload Excel File</p>
                    <input type="file" name="import" class="input is-samll" required>
                    <input type="submit" name="upload" value="Upload" class="button is-small is-link mt-2">
                </form>
            </div>

            @if(session()->get('siteunmatched'))
                <form method="post" action="{{route('sites.unmatched.store')}}">
                    @csrf
                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth mt-2">
                        <tr>
                            <th class="has-background-primary-dark" colspan="7">New Site Found ({{count(session()->get('siteunmatched'))}})</th>
                            <td><button type="submit" class="button is-small is-link">Insert</button></td>
                        </tr>
                        <tr>
                            <th>SL</th>
                            <th>Site Code</th>
                            <th>Location</th>
                            <th>Project Id</th>
                            <th>Task Limit</th>
                        </tr>
                        @foreach(session()->get('siteunmatched') as $key => $data)
                            <tr>
                                <td>
                                    {{++$key}}
                                </td>
                                <td>
                                    <input type="hidden" name="unmatched[{{$key}}][site_code]" value="{{ $data['site_code'] }}">
                                    {{ $data['site_code'] }}
                                </td>
                                <td>
                                    <input type="hidden" name="unmatched[{{$key}}][location]" value="{{ $data['location'] }}">
                                    {{ $data['location'] }}
                                </td>
                                <td>
                                    <input type="hidden" name="unmatched[{{$key}}][project_id]" value="{{ $data['project_id'] }}">
                                    <input type="hidden" name="unmatched[{{$key}}][pm]" value="{{ $data['pm'] }}">
                                    {{ $data['project_id'] }}
                                </td>

                                <td>
                                    @if(!empty($data['task_limit']))
                                        <input type="hidden" name="unmatched[{{$key}}][task_limit]" value="{{ $data['task_limit'] }}">
                                        {{ $data['task_limit'] }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </form>
            @endif
			@if(session()->get('matechedSiteButNotPending'))
          		 @foreach( session()->get('matechedSiteButNotPending') as $key => $data)
          			 <div class="has-background-danger-light mb-2 p-2">{!! $data['message'] !!}</div>
          		 @endforeach
          	@endif

            @if(session()->get('sitematched'))
                <?php //dd(session()->get('sitematched'));?>
                <form method="post" action="{{route('sites.matched.update')}}">
                    @csrf
                    <table class="table is-bordered is-striped is-narrow is-fullwidth is-hoverable mt-2">
                        
                        @if(!empty(session()->get('sitematched')[0]['message']))
                        @else
                      <tr>
                            <th class="has-background-danger-dark" colspan="7">Existing Pending Site Found ({{count(session()->get('sitematched'))}})</th>
                        </tr>
                            <tr>
                                <th class="">Site Code</th>
                                <th class="has-background-warning-dark">Completion Status</th>
                                <th class="has-background-warning-dark">Pending Note</th>
                                <th class="has-background-warning-dark">Project Id</th>
                                <th>Task Limit</th>
                                <th class="">Site Type</th>
                                <th class="">Activity Details</th>
                            </tr>
                            @foreach(session()->get('sitematched') as $key => $data)
                                <tr>
                                    @if(!empty($data['message']) && $data['message'])
                                        <td>{{ $data['site_code'] }}</td>
                                        <td colspan="5">{{ $data['message'] }}</td>
                                    @else
                                        <input type="hidden" name="matched[{{$key}}][site_id]" value="{{ $data['site_id'] }}" />
                                        {{--                            <input type="hidden" name="matched[{{$key}}][range_ids]" value="{{ $data['range_ids'] }}">--}}
                                        <td>{{ $data['site_code'] }}</td>
                                        <td>{{ $data['completion_status'] }}</td>
                                        <td>{{ $data['pending_note'] }}</td>
                                        <td>
                                            <input type="hidden" name="matched[{{$key}}][project_id]" value="{{ $data['project_id'] }}">
                                            <input type="hidden" name="unmatched[{{$key}}][pm]" value="{{ $data['pm'] }}">
                                            {{ $data['project_id'] }}
                                        </td>
                                        <td>
                                            <input type="hidden" name="matched[{{$key}}][task_limit]" value="{{ $data['task_limit'] }}">
                                            {{ $data['task_limit'] }}
                                        </td>
                                        <td>
                                            <select name="matched[{{$key}}][site_type]" class="input is-small" required>
                                                <option value="">select</option>
                                                <option value="Fresh">Fresh</option>
                                                <option value="Old">Old</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="input is-small" name="matched[{{$key}}][activity_details]">
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                      <tr>
                            
                            @if(!empty(session()->get('sitematched')[0]['message']))
                         		 <th class="has-background-danger-dark" colspan ="7">{!! session()->get('sitematched')[0]['message'] !!}</th>
                            @else
                        		<td colspan="6">&nbsp;</td>
                                <td style="text-align: end"><button type="submit" class="button is-small is-link">update</button></td>
                            @endif
                        </tr>
                    </table>
                </form>
            @endif

        </div>
    </div>


@endsection

@section('column_right')



@endsection
