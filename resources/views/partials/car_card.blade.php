<a href="/cars/{{ $vehicle['id'] ?? '01982195-f38f-714b-8382-a030bd88d121' }}" class="car-card-link" style="text-decoration:none;display:block;">
<div class="luxury-car-card p-0 mx-auto">
    <div class="p-4 pb-0 text-start">
        <h3 class="lux-heading mb-3 vehicle-name" style="color:#fff !important;font-size:1.1rem !important;font-weight:700 !important;">{{ $vehicle['name'] ?? 'BMW 7 Series 740i' }} {{ $vehicle['model'] ?? '' }}</h3>
        <div class="d-flex justify-content-start gap-2 mb-3 flex-wrap">
            <span class="badge bg-dark fs-6 px-3 py-2 rounded-pill">{{ $vehicle['seats'] ?? 5 }} Seats</span>
            <span class="badge bg-dark fs-6 px-3 py-2 rounded-pill">{{ $vehicle['doors'] ?? 4 }} Doors</span>
            <span class="badge bg-success fs-6 px-3 py-2 rounded-pill">{{ $vehicle['deposit'] ?? 'No Deposit' }}</span>
        </div>
        @if(isset($vehicle['image']) && $vehicle['image'])
            <img src="{{ $vehicle['image'] }}" alt="{{ $vehicle['name'] ?? 'Luxury Car' }}" class="img-fluid my-3 luxury-car-img" onerror="this.src='{{ asset('asset/image.png') }}'">
        @else
            <img src="{{ asset('asset/image.png') }}" alt="Luxury Car" class="img-fluid my-3 luxury-car-img">
        @endif
    </div>
    <div class="luxury-card-gradient p-4 rounded-bottom-4">
        <div class="row text-center mb-3">
            <div class="col">
                <div class="lux-heading" style="color:#111;font-size:1.2rem;">Daily</div>
                <div class="fs-5 fw-bold" style="color:#111;">{{ $vehicle['dailyRate'] ?? 999 }} AED</div>
            </div>
            <div class="col">
                <div class="lux-heading" style="color:#111;font-size:1.2rem;">Weekly</div>
                <div class="fs-5 fw-bold" style="color:#111;">{{ $vehicle['weeklyRate'] ?? 5999 }} AED</div>
            </div>
            <div class="col">
                <div class="lux-heading" style="color:#111;font-size:1.2rem;">Monthly</div>
                <div class="fs-5 fw-bold" style="color:#111;">{{ $vehicle['monthlyRate'] ?? 19999 }} AED</div>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <span class="btn btn-lg lux-btn-book">Book Now</span>
            <a href="https://wa.me/971501234567?text=Hi, I'm interested in booking the {{ $vehicle['name'] ?? 'BMW 7 Series 740i' }} {{ $vehicle['model'] ?? '' }} for {{ $vehicle['dailyRate'] ?? 999 }} AED/day" target="_blank" class="lux-whatsapp-icon ms-2"><img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" width="44" height="44"></a>
        </div>
    </div>
</div>
</a>
<style>
.car-card-link:hover .luxury-car-card {
    box-shadow: 0 8px 32px 0 rgba(191,161,51,0.18), 0 0 0 4px #bfa13333;
    transition: box-shadow 0.2s;
}
.car-card-link {
    color: inherit !important;
}
.vehicle-name {
    font-size: 2.2rem !important;
    font-weight: 700 !important;
    color: #fff !important;
}
</style>
