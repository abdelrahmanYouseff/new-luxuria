<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Vehicle Image System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .test-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin: 50px auto;
            max-width: 1200px;
        }
        .header-section {
            background: linear-gradient(135deg, #111 0%, #333 100%);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }
        .content-section {
            padding: 40px;
        }
        .feature-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            border-left: 4px solid #667eea;
        }
        .status-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .vehicle-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .vehicle-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: 1px solid #e9ecef;
        }
        .vehicle-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .button-demo {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .button-demo:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="test-container">
            <div class="header-section">
                <h1 class="mb-3">
                    <i class="bi bi-image"></i>
                    Test Vehicle Image System
                </h1>
                <p class="mb-0">Testing the new vehicle image management system with individual pages</p>
            </div>

            <div class="content-section">
                <div class="status-success">
                    <h4><i class="bi bi-check-circle"></i> System Ready!</h4>
                    <p>The vehicle image management system is now ready. Each vehicle has its own dedicated image management page.</p>
                </div>

                <!-- System Features -->
                <div class="feature-card">
                    <h5><i class="bi bi-gear"></i> System Features</h5>
                    <ul class="mb-0">
                        <li><strong>Individual Pages:</strong> Each vehicle has its own image management page</li>
                        <li><strong>Upload Images:</strong> Upload new images for specific vehicles</li>
                        <li><strong>Remove Images:</strong> Remove existing images</li>
                        <li><strong>Image Preview:</strong> Preview before upload</li>
                        <li><strong>Real-time Updates:</strong> See changes immediately</li>
                        <li><strong>Multiple Formats:</strong> Support for JPEG, PNG, JPG, GIF, WEBP</li>
                        <li><strong>File Size Limit:</strong> Maximum 2MB per image</li>
                    </ul>
                </div>

                <!-- URL Structure -->
                <div class="feature-card">
                    <h5><i class="bi bi-link-45deg"></i> URL Structure</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Vue Version (Inertia):</h6>
                            <code>/vehicles/{id}/image</code>
                            <p class="text-muted small">Modern SPA interface with Inertia.js</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Blade Version:</h6>
                            <code>/vehicles/{id}/image-blade</code>
                            <p class="text-muted small">Traditional Blade template</p>
                        </div>
                    </div>
                </div>

                <!-- Test Vehicles -->
                <div class="feature-card">
                    <h5><i class="bi bi-car-front"></i> Test Vehicles</h5>
                    <p>Click on any vehicle below to test its image management page:</p>

                    <div class="vehicle-grid">
                        @foreach($vehicles as $vehicle)
                            <div class="vehicle-card">
                                <div class="text-center mb-3">
                                    @if($vehicle->image)
                                        <img src="{{ $vehicle->image_url }}" alt="{{ $vehicle->make }} {{ $vehicle->model }}" class="vehicle-image">
                                    @else
                                        <div class="vehicle-image bg-gray-100 d-flex align-items-center justify-content-center">
                                            <i class="bi bi-image fs-1 text-muted"></i>
                                        </div>
                                    @endif
                                </div>

                                <h6 class="mb-2">{{ $vehicle->make }} {{ $vehicle->model }}</h6>
                                <p class="text-muted small mb-3">{{ $vehicle->plate_number }}</p>

                                <div class="d-flex gap-2">
                                    <a href="/vehicles/{{ $vehicle->id }}/image-blade" class="button-demo btn-sm">
                                        <i class="bi bi-image"></i>
                                        Manage Image
                                    </a>
                                    <a href="/vehicles/{{ $vehicle->id }}/image" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-gear"></i>
                                        Vue Version
                                    </a>
                                </div>

                                <div class="mt-2">
                                    <span class="badge {{ $vehicle->image ? 'bg-success' : 'bg-danger' }}">
                                        {{ $vehicle->image ? 'Has Image' : 'No Image' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="feature-card">
                    <h5><i class="bi bi-link-45deg"></i> Quick Links</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="/vehicles" class="btn btn-outline-primary">
                            <i class="bi bi-car-front"></i>
                            Main Vehicles Page
                        </a>
                        <a href="/quick-image-test" class="btn btn-outline-success">
                            <i class="bi bi-upload"></i>
                            Quick Upload Test
                        </a>
                        <a href="/test-image-upload" class="btn btn-outline-info">
                            <i class="bi bi-gear"></i>
                            Advanced Upload Test
                        </a>
                        <a href="/storage/vehicles" class="btn btn-outline-warning">
                            <i class="bi bi-folder"></i>
                            View Storage
                        </a>
                    </div>
                </div>

                <!-- How to Use -->
                <div class="feature-card">
                    <h5><i class="bi bi-question-circle"></i> How to Use</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center p-3">
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="bi bi-1-circle fs-4"></i>
                                </div>
                                <h6 class="mt-3">1. Select Vehicle</h6>
                                <p class="text-muted">Choose a vehicle from the list above</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3">
                                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="bi bi-2-circle fs-4"></i>
                                </div>
                                <h6 class="mt-3">2. Upload Image</h6>
                                <p class="text-muted">Select and upload an image file</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3">
                                <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="bi bi-3-circle fs-4"></i>
                                </div>
                                <h6 class="mt-3">3. Preview</h6>
                                <p class="text-muted">See the uploaded image</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3">
                                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="bi bi-4-circle fs-4"></i>
                                </div>
                                <h6 class="mt-3">4. View in List</h6>
                                <p class="text-muted">Check the vehicles page to see the image</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Vehicle Image System Test Loaded');

            // Add hover effects to vehicle cards
            const vehicleCards = document.querySelectorAll('.vehicle-card');
            vehicleCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 10px 25px rgba(0,0,0,0.15)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
                });
            });
        });
    </script>
</body>
</html>
