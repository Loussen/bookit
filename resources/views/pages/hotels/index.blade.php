@extends('layouts.app')

@section('content')

    <div class="search-section">
        <input type="text" class="search-input" placeholder="Search hotels...">

        <div class="filter-tags">
            <div class="filter-tag active">All</div>
            <div class="filter-tag">Popular</div>
            <div class="filter-tag">Top Rated</div>
            <div class="filter-tag">Luxury</div>
            <div class="filter-tag">Budget</div>
        </div>
    </div>

    <div class="hotel-list">
        @foreach($hotels as $hotel)
            <div class="hotel-card" data-hotel-id="{{ $hotel->id }}">
                <div class="hotel-image">
                    <img src="{{ $hotel->photo_url }}" alt="{{ $hotel->name }}">
                    <div class="hotel-price-badge">
                        From ${{ $hotel->price_range }}
                    </div>
                </div>

                <div class="hotel-content">
                    <h3 class="hotel-title">{{ $hotel->name }}</h3>

                    <div class="hotel-location">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        {{ $hotel->address }}
                    </div>

                    <div class="hotel-rating">
                        <div class="rating-stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $hotel->rating)
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                        </div>
                        <span class="rating-count">({{ random_int(50, 500) }} reviews)</span>
                    </div>

                    <p class="hotel-description">
                        {{ Str::limit($hotel->description, 100) }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tg = window.Telegram.WebApp;
                tg.ready();
                tg.expand();

                // Otel kartlarına tıklama olayı
                document.querySelectorAll('.hotel-card').forEach(card => {
                    card.addEventListener('click', function() {
                        const hotelId = this.dataset.hotelId;
                        window.location.href = `/hotels/${hotelId}`;
                    });
                });

                // Filter tag'lere tıklama olayı
                document.querySelectorAll('.filter-tag').forEach(tag => {
                    tag.addEventListener('click', function() {
                        document.querySelectorAll('.filter-tag').forEach(t => t.classList.remove('active'));
                        this.classList.add('active');
                    });
                });
            });
        </script>
    @endpush
@endsection
