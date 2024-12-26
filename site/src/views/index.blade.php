@extends('layouts.app')

@section('title')
    Sites
@endsection
@if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
    @php
        $addUrl = route('sites.create');
    @endphp
@else
    @php
        $addUrl = '#';
    @endphp
@endif
<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Sites',
            'spSubTitle' => 'all sites here',
            'spShowTitleSet' => true
        ])

        @include('component.button_set', [
            'spShowButtonSet' => true,
            'spAddUrl' => null,
            'spAddUrl' => $addUrl,
            'spAllData' => route('sites.index'),
            'spSearchData' => route('sites.search'),
            'spTitle' => 'Sites',
        ])

        @if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
        <div class="column is-1">
            <div class="level-item is-4">
                    <a href="{{route('sites.import')}}" class="button is-small is-warning is-rounded" aria-haspopup="true" aria-controls="dropdown-menu3">
                    <span><i class="fas fa-plus"></i> Import</span>
                </a>
            </div>

        </div>
        @endif

        @if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id) || auth()->user()->isAccountant(auth()->user()->id))
            <div class="column is-1">
                <div class="level-item is-4">
                    <a href="{{route('sites.export.excel')}}" class="button is-small is-primary is-rounded" aria-haspopup="true" aria-controls="dropdown-menu3">
                        <span><i class="fas fa-download"></i> Export as Excel</span>
                    </a>
                </div>
            </div>
        @endif

        @include('component.filter_set', [
            'spShowFilterSet' => true,
            'spPlaceholder' => 'Search sites...',
            'spAddUrl' => route('sites.create'),
            'spAllData' => route('sites.index'),
            'spSearchData' => route('sites.search'),
            'spMessage' => $message = $message ?? NULl,
            'spStatus' => $status = $status ?? NULL
        ])
    </nav>
</section>

@section('column_left')
    @if(!empty($sites))
        <div class="columns is-multiline">
            @php
                //dd($sites);
                if(auth()->user()->isManager(auth()->user()->id)) {
                    $manager_id = auth()->user()->id;

                    if(request()->get('key')) {
                        $default = [
                            'search_key' => request()->get('key') ?? '',
                            'limit' => request()->get('limit') ?? 10,
                            'offset' => request()->get('offset') ?? 0
                        ];
                        $no = $default;

                        $key = $no['search_key'];
                        $limit = $no['limit'];
                        $offset = $no['offset'];

                        $sitesss = \Tritiyo\Site\Models\Site::leftjoin('projects', 'projects.id', 'sites.project_id')
                                    ->leftjoin('users', 'users.id', 'projects.manager')
                                    ->select('sites.*', 'projects.name', 'projects.code', 'projects.type', 'projects.customer','users.name')
                                    ->where('projects.manager', $manager_id)
          							->orderBy('sites.id', 'desc')
                                    ->where(function($query) use ($key) {
                                            $query->where('sites.project_id' ,'LIKE', '%'.$key.'%');
                                            $query->orWhere('sites.location' ,'LIKE', '%'.$key.'%');
                                            $query->orWhere('sites.site_code' ,'LIKE', '%'.$key.'%');
                                            $query->orWhere('sites.material' ,'LIKE', '%'.$key.'%');
                                            $query->orWhere('sites.site_head' ,'LIKE', '%'.$key.'%');
                                            $query->orWhere('sites.budget' ,'LIKE', '%'.$key.'%');
                                            $query->orWhere('sites.completion_status' ,'LIKE', '%'.$key.'%');
                                            $query->orWhere('projects.name' ,'LIKE', '%'.$key.'%');
                                            $query->orWhere('projects.code' ,'LIKE', '%'.$key.'%');
                                            $query->orWhere('projects.type' ,'LIKE', '%'.$key.'%');
                                            $query->orWhere('projects.customer' ,'LIKE', '%'.$key.'%');
                                            $query->orWhere('users.name' ,'LIKE', '%'.$key.'%');
                                    })
                                    //->toSql();
                                    //->paginate('48');
          							->get();

                                    //dd($sitesss);

                    } else {
                        $sitesss = \DB::table('sites')->leftJoin('projects', 'projects.id', 'sites.project_id')
                                    ->select('sites.*', 'projects.manager')
                                    ->where('projects.manager', $manager_id)
                                    ->groupBy('sites.project_id')
                                    ->groupBy('sites.id')
                                    ->paginate(48);
                    }
                } else {
                    $sitesss = $sites;
                }

            @endphp
            @foreach($sitesss as $site)
                @include('site::index_template')
            @endforeach
        </div>
        <div class="pagination_wrap pagination is-centered">
            @if(Request::get('key'))
          			@php 
          				$appendRequest = [
                          'key' => Request::get('key'),
                          'limit' => Request::get('limit') ?? 48,
                          'offset' => Request::get('offset') ?? 0
                        ];
          			//echo $sitesss->withQueryString()->appends($appendRequest)->links('pagination::bootstrap-4');
          			//exit();
          //{{$sitesss->withQueryString()->appends()->links('pagination::bootstrap-4') }}
          			@endphp
                  
            @else
                {{ $sitesss->links('pagination::bootstrap-4') }}
            @endif
        </div>
    @endif
@endsection
