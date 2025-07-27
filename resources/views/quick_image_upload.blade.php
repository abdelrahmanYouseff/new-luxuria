<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quick Image Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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
            max-width: 1000px;
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
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }
        .vehicle-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .image-preview {
            width: 80px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
        }
        .upload-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            transition: all 0.3s ease;
        }
        .upload-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .status-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 10px;
            border-radius: 6px;
            margin: 10px 0;
            font-size: 14px;
        }
        .status-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 10px;
            border-radius: 6px;
            margin: 10px 0;
            font-size: 14px;
        }
        .progress-bar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 4px;
            border-radius: 2px;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-container">
            <div class="header-section">
                <h2 class="mb-2">
                    <i class="bi bi-images"></i>
                    Quick Image Upload
                </h2>
                <p class="mb-0">Upload images for vehicles quickly and easily</p>
            </div>

            <div class="content-section">
                <!-- Status Messages -->
                <div id="statusMessage" style="display: none;" class="status-success">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle me-2"></i>
                        <span id="statusText"></span>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle"></i> Instructions</h6>
                    <ol class="mb-0 small">
                        <li>Click "Upload Image" on any vehicle</li>
                        <li>Select an image file</li>
                        <li>Wait for upload to complete</li>
                        <li>Go to <a href="/vehicles" target="_blank">Vehicles Page</a> and refresh</li>
                    </ol>
                </div>

                <!-- Progress -->
                <div id="progressSection" style="display: none;" class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small">Upload Progress</span>
                        <span id="progressText" class="small text-muted">0 / 0</span>
                    </div>
                    <div class="bg-light rounded" style="height: 4px;">
                        <div class="progress-bar" id="progressBar" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Vehicles Grid -->
                <div class="row">
                    @foreach($vehicles as $vehicle)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="vehicle-card">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @if($vehicle->image)
                                            <img src="{{ $vehicle->image_url }}" alt="{{ $vehicle->make }} {{ $vehicle->model }}" class="image-preview">
                                        @else
                                            <div class="image-preview bg-light d-flex align-items-center justify-content-center">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $vehicle->make }} {{ $vehicle->model }}</h6>
                                        <p class="text-muted small mb-1">{{ $vehicle->plate_number }}</p>
                                        <span class="badge {{ $vehicle->image ? 'bg-success' : 'bg-danger' }} small">
                                            {{ $vehicle->image ? 'Has Image' : 'No Image' }}
                                        </span>
                                    </div>
                                    <div>
                                        <input
                                            type="file"
                                            id="file_{{ $vehicle->id }}"
                                            accept="image/*"
                                            style="display: none;"
                                            onchange="uploadImage({{ $vehicle->id }}, this.files[0])"
                                        />
                                        <button
                                            onclick="document.getElementById('file_{{ $vehicle->id }}').click()"
                                            class="upload-btn"
                                        >
                                            <i class="bi bi-upload"></i>
                                            Upload
                                        </button>
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
                        <a href="/vehicles" class="btn btn-primary btn-sm">
                            <i class="bi bi-car-front"></i>
                            View Vehicles
                        </a>
                        <a href="/single-image-test" class="btn btn-success btn-sm">
                            <i class="bi bi-image"></i>
                            Single Test
                        </a>
                        <a href="/bulk-image-upload" class="btn btn-info btn-sm">
                            <i class="bi bi-images"></i>
                            Bulk Upload
                        </a>
                        <button onclick="refreshAllImages()" class="btn btn-warning btn-sm">
                            <i class="bi bi-arrow-clockwise"></i>
                            Refresh All
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let uploadCount = 0;
        let totalUploads = 0;

        // Upload image for specific vehicle
        async function uploadImage(vehicleId, file) {
            if (!file) return;

            const formData = new FormData();
            formData.append('image', file);

            // Show progress
            totalUploads++;
            showProgress();

            try {
                const response = await fetch(`/vehicles/${vehicleId}/image`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    uploadCount++;
                    showStatus(`✅ Uploaded image for vehicle ${vehicleId}`, 'success');

                    // Update the vehicle card
                    updateVehicleCard(vehicleId, result.image_url);
                } else {
                    showStatus(`❌ Failed to upload for vehicle ${vehicleId}: ${result.message}`, 'error');
                }
            } catch (error) {
                showStatus(`❌ Error uploading for vehicle ${vehicleId}`, 'error');
            }

            updateProgress();
        }

        // Update vehicle card after upload
        function updateVehicleCard(vehicleId, imageUrl) {
            const card = document.querySelector(`[onclick*="${vehicleId}"]`).closest('.vehicle-card');
            const imgContainer = card.querySelector('.image-preview').parentElement;
            const badge = card.querySelector('.badge');

            // Update image
            imgContainer.innerHTML = `<img src="${imageUrl}" alt="Vehicle" class="image-preview">`;

            // Update badge
            badge.className = 'badge bg-success small';
            badge.textContent = 'Has Image';
        }

        // Show progress
        function showProgress() {
            document.getElementById('progressSection').style.display = 'block';
        }

        // Update progress
        function updateProgress() {
            const progress = (uploadCount / totalUploads) * 100;
            document.getElementById('progressBar').style.width = progress + '%';
            document.getElementById('progressText').textContent = `${uploadCount} / ${totalUploads}`;

            if (uploadCount === totalUploads) {
                setTimeout(() => {
                    document.getElementById('progressSection').style.display = 'none';
                    uploadCount = 0;
                    totalUploads = 0;
                }, 3000);
            }
        }

        // Refresh all images
        function refreshAllImages() {
            showStatus('🔄 Refreshing page...', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }

        // Show status message
        function showStatus(message, type) {
            const statusDiv = document.getElementById('statusMessage');
            const statusText = document.getElementById('statusText');

            statusText.textContent = message;
            statusDiv.className = type === 'success' ? 'status-success' : 'status-error';
            statusDiv.style.display = 'block';

            // Hide after 4 seconds
            setTimeout(() => {
                statusDiv.style.display = 'none';
            }, 4000);
        }
    </script>
</body>
</html>
