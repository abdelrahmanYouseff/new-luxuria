<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bulk Image Upload</title>
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
            margin: 50px auto;
            max-width: 1200px;
        }
        .header-section {
            background: linear-gradient(135deg, #111 0%, #333 100%);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
        }
        .content-section {
            padding: 40px;
        }
        .vehicle-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            border-left: 4px solid #667eea;
        }
        .image-preview {
            max-width: 150px;
            max-height: 100px;
            border-radius: 8px;
            object-fit: cover;
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
        .upload-progress {
            background: #e9ecef;
            border-radius: 10px;
            height: 20px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-bar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100%;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-container">
            <div class="header-section">
                <h1 class="mb-3">
                    <i class="bi bi-images"></i>
                    Bulk Image Upload
                </h1>
                <p class="mb-0">Upload images for multiple vehicles at once</p>
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
                    <h5><i class="bi bi-info-circle"></i> Instructions</h5>
                    <ol class="mb-0">
                        <li>Select images for the vehicles you want to update</li>
                        <li>Make sure the image filename matches the vehicle name (e.g., "Infiniti QX60.jpg")</li>
                        <li>Click "Upload All Images" to process them</li>
                        <li>Check the results and refresh the vehicles page</li>
                    </ol>
                </div>

                <!-- Upload Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-upload"></i>
                            Select Images
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="bulkUploadForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="images" class="form-label">Select Multiple Images</label>
                                <input
                                    type="file"
                                    class="form-control"
                                    id="images"
                                    name="images[]"
                                    accept="image/*"
                                    multiple
                                    required
                                />
                                <div class="form-text">
                                    Select multiple image files (JPEG, PNG, JPG, GIF, WEBP - max 2MB each)
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                id="uploadBtn"
                                disabled
                            >
                                <i class="bi bi-upload"></i>
                                Upload All Images
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Progress -->
                <div id="progressSection" style="display: none;">
                    <h5>Upload Progress</h5>
                    <div class="upload-progress">
                        <div class="progress-bar" id="progressBar" style="width: 0%"></div>
                    </div>
                    <p id="progressText" class="text-muted">0 / 0 images uploaded</p>
                </div>

                <!-- Vehicle List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-car-front"></i>
                            Available Vehicles
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($vehicles as $vehicle)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="vehicle-item">
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
                                                <span class="badge {{ $vehicle->image ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $vehicle->image ? 'Has Image' : 'No Image' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="text-center mt-4">
                    <h5>Quick Links</h5>
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        <a href="/vehicles" class="btn btn-outline-primary">
                            <i class="bi bi-car-front"></i>
                            View Vehicles Page
                        </a>
                        <a href="/test-vehicle-image-system" class="btn btn-outline-success">
                            <i class="bi bi-gear"></i>
                            Test Image System
                        </a>
                        <a href="/storage/vehicles" class="btn btn-outline-info">
                            <i class="bi bi-folder"></i>
                            View Storage
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // File selection
        document.getElementById('images').addEventListener('change', function(e) {
            const files = e.target.files;
            const uploadBtn = document.getElementById('uploadBtn');

            if (files.length > 0) {
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = `<i class="bi bi-upload"></i> Upload ${files.length} Images`;
            } else {
                uploadBtn.disabled = true;
                uploadBtn.innerHTML = '<i class="bi bi-upload"></i> Upload All Images';
            }
        });

        // Bulk upload
        document.getElementById('bulkUploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const files = document.getElementById('images').files;
            if (files.length === 0) {
                showStatus('Please select at least one image', 'error');
                return;
            }

            const uploadBtn = document.getElementById('uploadBtn');
            const progressSection = document.getElementById('progressSection');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');

            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Uploading...';
            progressSection.style.display = 'block';

            let successCount = 0;
            let errorCount = 0;

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const formData = new FormData();
                formData.append('image', file);

                // Try to find matching vehicle by filename
                const fileName = file.name.toLowerCase();
                let vehicleId = null;

                // Simple matching logic - you can improve this
                @foreach($vehicles as $vehicle)
                    if (fileName.includes('{{ strtolower($vehicle->make) }}') && fileName.includes('{{ strtolower($vehicle->model) }}')) {
                        vehicleId = {{ $vehicle->id }};
                    }
                @endforeach

                if (!vehicleId) {
                    errorCount++;
                    continue;
                }

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
                        successCount++;
                    } else {
                        errorCount++;
                    }
                } catch (error) {
                    errorCount++;
                }

                // Update progress
                const progress = ((i + 1) / files.length) * 100;
                progressBar.style.width = progress + '%';
                progressText.textContent = `${i + 1} / ${files.length} images processed`;
            }

            // Show final results
            if (successCount > 0) {
                showStatus(`Successfully uploaded ${successCount} images! ${errorCount > 0 ? `${errorCount} failed.` : ''}`, 'success');
            } else {
                showStatus(`Failed to upload images. ${errorCount} errors.`, 'error');
            }

            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="bi bi-upload"></i> Upload All Images';

            // Hide progress after 3 seconds
            setTimeout(() => {
                progressSection.style.display = 'none';
            }, 3000);
        });

        // Show status message
        function showStatus(message, type) {
            const statusDiv = document.getElementById('statusMessage');
            const statusText = document.getElementById('statusText');

            statusText.textContent = message;
            statusDiv.className = type === 'success' ? 'status-success' : 'status-error';
            statusDiv.style.display = 'block';

            // Hide after 5 seconds
            setTimeout(() => {
                statusDiv.style.display = 'none';
            }, 5000);
        }
    </script>
</body>
</html>
