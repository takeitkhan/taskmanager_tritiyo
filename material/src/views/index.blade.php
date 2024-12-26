@extends('layouts.app')

@section('title')
    Materials
@endsection
@if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
    @php
        $addUrl = route('materials.create');
    @endphp
@else
    @php
        $addUrl = '#';
    @endphp
@endif
<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Materials',
            'spSubTitle' => 'all materials here',
            'spShowTitleSet' => true
        ])

        @include('component.button_set', [
            'spShowButtonSet' => true,
            'spAddUrl' => null,
            'spTitle' => 'Materials',
            'spAddUrl' => $addUrl,
            'spAllData' => route('materials.index'),
            'spSearchData' => route('materials.search'),
        ])

        @include('component.filter_set', [
            'spShowFilterSet' => true,
            'spAddUrl' => route('materials.create'),
            'spAllData' => route('materials.index'),
            'spSearchData' => route('materials.search'),
            'spPlaceholder' => 'Search materials...',
            'spMessage' => $message = $message ?? NULl,
            'spStatus' => $status = $status ?? NULL
        ])
    </nav>
</section>

@section('column_left')
    @if(!empty($materials))
        <div class="columns is-multiline">
            @foreach($materials as $material)
                <div class="column is-2">
                    <div class="borderedCol">
                        <article class="media">
                            <div class="media-content">
                                <div class="content">
                                    <p>
                                        <strong>
                                            <a href="{{ route('materials.show', $material->id) }}"
                                               title="View route">
                                                <strong>{{ $material->name }} </strong>
                                            </a>
                                        </strong>
                                        <br/>
                                        <small>
                                            <strong>Unit: </strong> {{ $material->unit }}
                                        </small>
                                        <br/>
                                    </p>
                                </div>
                                <nav class="level is-mobile">
                                    <div class="level-left">
                                        <a href="{{ route('materials.show', $material->id) }}"
                                           class="level-item"
                                           title="View user data">
                                            <span class="icon is-small"><i class="fas fa-eye"></i></span>
                                        </a>
                                        @if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
                                            <a href="{{ route('materials.edit', $material->id) }}"
                                            class="level-item"
                                            title="View all transaction">
                                                <span class="icon is-info is-small"><i class="fas fa-edit"></i></span>
                                            </a>
                                        @endif

                                        {{--                                        {!! delete_data('materials.destroy',  $material->id) !!}--}}
                                    </div>
                                </nav>
                            </div>
                        </article>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="pagination_wrap pagination is-centered">
            @if(Request::get('key'))
                {{ $materials->appends(['key' => Request::get('key')])->links('pagination::bootstrap-4') }}
            @else
                {{ $materials->links('pagination::bootstrap-4')}}
            @endif
        </div>
    @endif
@endsection
