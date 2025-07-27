<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Vehicles Table with Year</title>
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
            max-width: 1400px;
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
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        .table th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }
        .vehicle-image {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
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
        .year-badge {
            background: #e3f2fd;
            color: #1565c0;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="main-container">
            <div class="header-section">
                <h2 class="mb-2">
                    <i class="bi bi-table"></i>
                    Test Vehicles Table with Year Column
                </h2>
                <p class="mb-0">Testing vehicles table with year column display</p>
            </div>

            <div class="content-section">
                <!-- Debug Info -->
                <div class="alert alert-info">
                    <h6>Debug Information</h6>
                    <p><strong>Total Vehicles:</strong> {{ $vehicles->count() }}</p>
                    <p><strong>Vehicles with Year:</strong> {{ $vehicles->whereNotNull('year')->count() }}</p>
                    <p><strong>Vehicles without Year:</strong> {{ $vehicles->whereNull('year')->count() }}</p>
                </div>

                <!-- Vehicles Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Vehicle</th>
                                <th>Plate Number</th>
                                <th>Status</th>
                                <th>Category</th>
                                <th>Ownership</th>
                                <th>Year</th>
                                <th>Daily Rate</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicles as $vehicle)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if($vehicle->image)
                                                    <img
                                                        src="{{ $vehicle->image_url }}"
                                                        alt="{{ $vehicle->make }} {{ $vehicle->model }}"
                                                        class="vehicle-image"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
                                                    />
                                                    <div class="vehicle-image" style="display: none; background: #e9ecef; display: flex; align-items: center; justify-content: center;">
                                                        <span class="text-muted small">Error</span>
                                                    </div>
                                                @else
                                                    <div class="vehicle-image" style="background: #e9ecef; display: flex; align-items: center; justify-content: center;">
                                                        <span class="text-muted small">No Image</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $vehicle->make }} {{ $vehicle->model }}</div>
                                                <div class="text-muted small">{{ $vehicle->color ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $vehicle->plate_number ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ strtolower($vehicle->status ?? 'available') }}">
                                            {{ ucfirst($vehicle->status ?? 'Available') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($vehicle->category ?? 'Standard') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ ucfirst($vehicle->ownership_status ?? 'Owned') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge year-badge">{{ $vehicle->year ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">AED {{ number_format($vehicle->daily_rate ?? 0) }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="/cars/{{ $vehicle->id }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Quick Actions -->
                <div class="text-center mt-4">
                    <h6>Quick Actions</h6>
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        <a href="/test-vehicles-images" class="btn btn-primary btn-sm">
                            <i class="bi bi-images"></i>
                            Test Images
                        </a>
                        <a href="/quick-image-upload" class="btn btn-success btn-sm">
                            <i class="bi bi-upload"></i>
                            Upload Images
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
