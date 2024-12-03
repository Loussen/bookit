@extends('layouts.app')

@section('title', 'Hotels List')

@section('content')
    <div class="container py-4">
        <h1 class="text-center mb-4">Our Hotels</h1>

        <div class="row">
            @foreach($hotels as $hotel)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card hotel-card h-100">
                        <img src="{{ $hotel->photo_url }}"
                             class="card-img-top"
                             alt="{{ $hotel->name }}"
                             style="height: 200px; object-fit: cover;">

                        <div class="card-body">
                            <h5 class="card-title">{{ $hotel->name }}</h5>

                            <p class="card-text text-muted">
                                <i class="fas fa-map-marker-alt"></i> {{ $hotel->address }}
                            </p>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-warning">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star{{ $i <= $hotel->rating ? '' : '-o' }}"></i>
                            @endfor
                        </span>
                                <span class="badge bg-primary">{{ $hotel->price_range }}</span>
                            </div>

                            <p class="card-text">{{ Str::limit($hotel->description, 100) }}</p>
                        </div>

                        <div class="card-footer bg-white border-top-0">
                            <a href="{{ route('hotels.show', $hotel) }}"
                               class="btn btn-primary w-100">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
