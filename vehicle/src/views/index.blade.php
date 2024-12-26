@extends('layouts.app')

@section('title')
    Vehicles
@endsection
@if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
    @php
        $addUrl = route('vehicles.create');
    @endphp
@else
    @php
        $addUrl = '#';
    @endphp
@endif
<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Vehicles',
            'spSubTitle' => 'all vehicles here',
            'spShowTitleSet' => true
        ])

        @include('component.button_set', [
            'spShowButtonSet' => true,
            'spAddUrl' => null,
            'spTitle' => 'Vehicles',
            'spAddUrl' => $addUrl,
            'spAllData' => route('vehicles.index'),
            'spSearchData' => route('vehicles.search'),
            'spTitle' => 'Vehicles',
        ])

        @include('component.filter_set', [
            'spShowFilterSet' => true,
            'spAddUrl' => route('vehicles.create'),
            'spAllData' => route('vehicles.index'),
            'spSearchData' => route('vehicles.search'),
            'spPlaceholder' => 'Search vehicles...',
            'spMessage' => $message = $message ?? NULl,
            'spStatus' => $status = $status ?? NULL
        ])
    </nav>
</section>

@section('column_left')
    @if(!empty($vehicles))
        <div class="columns is-multiline">
            @foreach($vehicles as $vehicle)
                <div class="column is-2">
                    <div class="borderedCol">
                        <article class="media">
                            <div class="media-content">
                                <div class="content">
                                    <p>
                                        <strong>
                                            <a href="{{ route('vehicles.show', $vehicle->id) }}"
                                               title="View route">
                                                <strong> {{ $vehicle->name }} </strong>
                                            </a>
                                        </strong>
                                        <br/>
                                        <small>
                                            <strong>Size: </strong> {{ $vehicle->size }},
                                        </small>
                                        <br/>
                                        <small>
                                            <strong>Probably Cost:</strong> {{ $vehicle->probably_cost }}
                                        </small>
                                        <br/>
                                    </p>
                                </div>
                                <nav class="level is-mobile">
                                    <div class="level-left">
                                        <a href="{{ route('vehicles.show', $vehicle->id) }}"
                                           class="level-item"
                                           title="View user data">
                                            <span class="icon is-small"><i class="fas fa-eye"></i></span>
                                        </a>
                                        @if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
                                            <a href="{{ route('vehicles.edit', $vehicle->id) }}"
                                            class="level-item"
                                            title="View all transaction">
                                                <span class="icon is-info is-small"><i class="fas fa-edit"></i></span>
                                            </a>
                                        @endif

                                        {{--                                        {!! delete_data('vehicles.destroy',  $vehicle->id) !!}--}}
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
                {{ $vehicles->appends(['key' => Request::get('key')])->links('pagination::bootstrap-4') }}
            @else
                {{ $vehicles->links('pagination::bootstrap-4') }}
            @endif
        </div>
    @endif
@endsection
