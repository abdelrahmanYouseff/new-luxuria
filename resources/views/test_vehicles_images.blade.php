<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Vehicles with Images</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin: 30px auto;
            max-width: 1200px;
        }
        .header-section {
            background: linear-gradient(135deg, #111 0%, #333 100%);
            color: white;
            padding: 25px;
            border-radius: 15px 15px 0 0;
        }
        .content-section {
            padding: 30px;
        }
        .vehicle-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }
        .vehicle-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .vehicle-image {
            width: 120px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid #e9ecef;
        }
        .vehicle-image-placeholder {
            width: 120px;
            height: 80px;
            border-radius: 8px;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #dee2e6;
        }
        .status-badge {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 12px;
        }
        .status-available {
            background: #d4edda;
            color: #155724;
        }
        .status-rented {
            background: #f8d7da;
            color: #721c24;
        }
        .status-maintenance {
            background: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-container">
            <div class="header-section">
                <h2 class="mb-2">
                    <i class="bi bi-car-front"></i>
                    Test Vehicles with Images
                </h2>
                <p class="mb-0">Testing vehicle images display</p>
            </div>

            <div class="content-section">
                <!-- Debug Info -->
                <div class="alert alert-info">
                    <h6>Debug Information</h6>
                    <p><strong>Total Vehicles:</strong> {{ $vehicles->count() }}</p>
                    <p><strong>Vehicles with Images:</strong> {{ $vehicles->whereNotNull('image')->count() }}</p>
                    <p><strong>Vehicles without Images:</strong> {{ $vehicles->whereNull('image')->count() }}</p>
                </div>

                <!-- Vehicles List -->
                <div class="row">
                    @foreach($vehicles as $vehicle)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="vehicle-card">
                                <div class="d-flex align-items-start">
                                    <div class="me-3">
                                        @if($vehicle->image)
                                            <img
                                                src="{{ $vehicle->image_url }}"
                                                alt="{{ $vehicle->make }} {{ $vehicle->model }}"
                                                class="vehicle-image"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                            />
                                            <div class="vehicle-image-placeholder" style="display: none;">
                                                <span class="text-muted">Image Error</span>
                                            </div>
                                        @else
                                            <div class="vehicle-image-placeholder">
                                                <span class="text-muted">No Image</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $vehicle->make }} {{ $vehicle->model }}</h6>
                                        <p class="text-muted small mb-1">{{ $vehicle->plate_number }}</p>

                                        <div class="d-flex flex-wrap gap-1 mb-2">
                                            <span class="status-badge status-{{ strtolower($vehicle->status) }}">
                                                {{ ucfirst($vehicle->status) }}
                                            </span>
                                            <span class="status-badge bg-secondary text-white">
                                                {{ ucfirst($vehicle->category) }}
                                            </span>
                                            <span class="status-badge bg-info text-white">
                                                {{ ucfirst($vehicle->ownership_status) }}
                                            </span>
                                        </div>

                                        <div class="small text-muted">
                                            <div><strong>Daily Rate:</strong> AED {{ number_format($vehicle->daily_rate, 2) }}</div>
                                            <div><strong>Odometer:</strong> {{ number_format($vehicle->odometer) }} km</div>
                                            @if($vehicle->image)
                                                <div><strong>Image Path:</strong> {{ $vehicle->image }}</div>
                                                <div><strong>Image URL:</strong> {{ $vehicle->image_url }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Quick Actions -->
                <div class="text-center mt-4">
                    <h6>Quick Actions</h6>
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        <a href="/quick-image-upload" class="btn btn-primary btn-sm">
                            <i class="bi bi-upload"></i>
                            Upload Images
                        </a>
                        <a href="/test-vehicle-image-system" class="btn btn-success btn-sm">
                            <i class="bi bi-gear"></i>
                            Test System
                        </a>
                        <button onclick="location.reload()" class="btn btn-warning btn-sm">
                            <i class="bi bi-arrow-clockwise"></i>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Check if images are loading correctly
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.vehicle-image');
            let loadedImages = 0;
            let totalImages = images.length;

            images.forEach(img => {
                if (img.complete) {
                    loadedImages++;
                } else {
                    img.addEventListener('load', function() {
                        loadedImages++;
                        console.log(`Image loaded: ${img.src}`);
                    });
                    img.addEventListener('error', function() {
                        console.log(`Image failed to load: ${img.src}`);
                    });
                }
            });

            console.log(`Total images: ${totalImages}, Loaded: ${loadedImages}`);
        });
    </script>
</body>
</html>
