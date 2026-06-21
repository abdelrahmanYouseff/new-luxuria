@php $isRtl = app()->getLocale() === 'ar'; @endphp
<a href="/cars/{{ $vehicle['id'] ?? '' }}" class="car-card-link" style="text-decoration:none;display:block;">
<div class="luxury-car-card p-0 mx-auto">
    <div class="p-4 pb-0 text-{{ $isRtl ? 'end' : 'start' }}">
        <h3 class="lux-heading mb-3 vehicle-name" style="color:#fff !important;font-size:1.4rem !important;font-weight:700 !important;">{{ translateCarName($vehicle['name'] ?? 'Luxury Car') }}</h3>
        <div class="d-flex justify-content-{{ $isRtl ? 'end' : 'start' }} gap-2 mb-3 flex-wrap">
            <span class="badge bg-dark fs-6 px-3 py-2 rounded-pill">{{ $vehicle['seats'] ?? 5 }} {{ __('app.card_seats') }}</span>
            <span class="badge bg-dark fs-6 px-3 py-2 rounded-pill">{{ $vehicle['doors'] ?? 4 }} {{ __('app.card_doors') }}</span>
            <span class="badge bg-success fs-6 px-3 py-2 rounded-pill">{{ $vehicle['deposit'] ?? __('app.card_no_deposit') }}</span>
        </div>
        <img src="{{ ($vehicle['image'] ?? '') ?: asset('asset/image.png') }}"
             alt="{{ $vehicle['name'] ?? 'Luxury Car' }}"
             class="img-fluid my-3 luxury-car-img"
             loading="lazy"
             onerror="this.src='{{ asset('asset/image.png') }}'">
    </div>
    <div class="luxury-card-gradient p-4 rounded-bottom-4">
        <div class="row text-center mb-3">
            <div class="col">
                <div class="lux-heading" style="color:#111;font-size:1.2rem;">{{ __('app.card_daily') }}</div>
                <div class="fs-5 fw-bold" style="color:#111;">{{ number_format($vehicle['dailyRate'] ?? 0) }} AED</div>
            </div>
            <div class="col">
                <div class="lux-heading" style="color:#111;font-size:1.2rem;">{{ __('app.card_weekly') }}</div>
                <div class="fs-5 fw-bold" style="color:#111;">{{ number_format($vehicle['weeklyRate'] ?? 0) }} AED</div>
            </div>
            <div class="col">
                <div class="lux-heading" style="color:#111;font-size:1.2rem;">{{ __('app.card_monthly') }}</div>
                <div class="fs-5 fw-bold" style="color:#111;">{{ number_format($vehicle['monthlyRate'] ?? 0) }} AED</div>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <span class="btn btn-lg lux-btn-book">{{ __('app.card_book_now') }}</span>
            <a href="https://wa.me/971502711549?text={{ urlencode('Hi, I\'m interested in booking the ' . ($vehicle['name'] ?? 'vehicle') . ' for ' . ($vehicle['dailyRate'] ?? 0) . ' AED/day') }}"
               target="_blank"
               class="lux-whatsapp-icon {{ $isRtl ? 'me-2' : 'ms-2' }}"
               onclick="event.stopPropagation();event.preventDefault();window.open(this.href,'_blank');">
                <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" width="44" height="44">
            </a>
        </div>
    </div>
</div>
</a>
