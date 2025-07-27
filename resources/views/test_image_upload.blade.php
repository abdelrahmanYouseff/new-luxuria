<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Image Upload</title>
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
            max-width: 800px;
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
        .upload-area {
            border: 2px dashed #667eea;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            border-color: #764ba2;
            background: #e3f2fd;
        }
        .upload-area.dragover {
            border-color: #28a745;
            background: #d4edda;
        }
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .vehicle-card {
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
        .status-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="test-container">
            <div class="header-section">
                <h1 class="mb-3">
                    <i class="bi bi-image"></i>
                    Test Image Upload for Vehicles
                </h1>
                <p class="mb-0">Upload images for vehicles and test the image display system</p>
            </div>

            <div class="content-section">
                <div class="status-success">
                    <h4><i class="bi bi-info-circle"></i> Image Upload System</h4>
                    <p>This page allows you to test uploading images for vehicles. The images will be stored in <code>storage/app/public/vehicles/</code> and accessible via the symlink.</p>
                </div>

                <!-- Vehicle Selection -->
                <div class="vehicle-card">
                    <h5><i class="bi bi-car-front"></i> Select Vehicle</h5>
                    <form id="vehicleForm" method="POST" action="/test-upload-image" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <label for="vehicle_id" class="form-label">Vehicle</label>
                                <select class="form-select" id="vehicle_id" name="vehicle_id" required>
                                    <option value="">Select a vehicle...</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">
                                            {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->plate_number }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="image" class="form-label">Vehicle Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i>
                                Upload Image
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Image Preview -->
                <div id="imagePreview" class="vehicle-card" style="display: none;">
                    <h5><i class="bi bi-eye"></i> Image Preview</h5>
                    <div class="text-center">
                        <img id="previewImg" class="preview-image" src="" alt="Preview">
                    </div>
                </div>

                <!-- Upload Results -->
                @if(session('success'))
                    <div class="status-success">
                        <h4><i class="bi bi-check-circle"></i> Upload Successful!</h4>
                        <p>{{ session('success') }}</p>
                        @if(session('image_path'))
                            <p><strong>Image Path:</strong> {{ session('image_path') }}</p>
                            <p><strong>Image URL:</strong> <a href="{{ asset('storage/' . session('image_path')) }}" target="_blank">{{ asset('storage/' . session('image_path')) }}</a></p>
                        @endif
                    </div>
                @endif

                @if(session('error'))
                    <div class="status-error">
                        <h4><i class="bi bi-exclamation-triangle"></i> Upload Failed!</h4>
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                <!-- Test Links -->
                <div class="vehicle-card">
                    <h5><i class="bi bi-link-45deg"></i> Test Links</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="/vehicles" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-car-front"></i>
                            View Vehicles Page
                        </a>
                        <a href="/database/vehicles" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-database"></i>
                            Database Management
                        </a>
                        <a href="/storage/vehicles" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-folder"></i>
                            View Storage Folder
                        </a>
                    </div>
                </div>

                <!-- Current Images -->
                <div class="vehicle-card">
                    <h5><i class="bi bi-images"></i> Current Vehicle Images</h5>
                    <div class="row">
                        @foreach($vehiclesWithImages as $vehicle)
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <img src="{{ $vehicle->image_url }}" class="card-img-top" alt="{{ $vehicle->make }} {{ $vehicle->model }}" style="height: 150px; object-fit: cover;">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $vehicle->make }} {{ $vehicle->model }}</h6>
                                        <p class="card-text small">{{ $vehicle->plate_number }}</p>
                                        <p class="card-text small text-muted">{{ $vehicle->image }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($vehiclesWithImages->isEmpty())
                        <p class="text-muted">No vehicles with images found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Image preview
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        // Form validation
        document.getElementById('vehicleForm').addEventListener('submit', function(e) {
            const vehicleId = document.getElementById('vehicle_id').value;
            const image = document.getElementById('image').files[0];

            if (!vehicleId) {
                e.preventDefault();
                alert('Please select a vehicle');
                return;
            }

            if (!image) {
                e.preventDefault();
                alert('Please select an image');
                return;
            }
        });
    </script>
</body>
</html>
