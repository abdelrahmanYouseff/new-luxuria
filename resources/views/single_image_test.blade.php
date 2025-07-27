<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Single Image Test</title>
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
            max-width: 800px;
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
        .image-preview {
            max-width: 300px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
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
        .vehicle-select {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-container">
            <div class="header-section">
                <h1 class="mb-3">
                    <i class="bi bi-image"></i>
                    Single Image Upload Test
                </h1>
                <p class="mb-0">Test uploading a single image for a specific vehicle</p>
            </div>

            <div class="content-section">
                <!-- Status Messages -->
                <div id="statusMessage" style="display: none;" class="status-success">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle me-2"></i>
                        <span id="statusText"></span>
                    </div>
                </div>

                <!-- Vehicle Selection -->
                <div class="vehicle-select">
                    <h5><i class="bi bi-car-front"></i> Select Vehicle</h5>
                    <div class="row">
                        @foreach($vehicles as $vehicle)
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if($vehicle->image)
                                                    <img src="{{ $vehicle->image_url }}" alt="{{ $vehicle->make }} {{ $vehicle->model }}" style="width: 60px; height: 40px; object-fit: cover; border-radius: 5px;">
                                                @else
                                                    <div style="width: 60px; height: 40px; background: #e9ecef; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
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
                                            <div>
                                                <button
                                                    onclick="selectVehicle({{ $vehicle->id }}, '{{ $vehicle->make }} {{ $vehicle->model }}')"
                                                    class="btn btn-primary btn-sm"
                                                >
                                                    Select
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Upload Form -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-upload"></i>
                            Upload Image
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="uploadForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Selected Vehicle</label>
                                <input type="text" class="form-control" id="selectedVehicle" readonly placeholder="Please select a vehicle first">
                                <input type="hidden" id="selectedVehicleId">
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Select Image</label>
                                <input
                                    type="file"
                                    class="form-control"
                                    id="image"
                                    name="image"
                                    accept="image/*"
                                    required
                                />
                                <div class="form-text">
                                    Supported formats: JPEG, PNG, JPG, GIF, WEBP (max 2MB)
                                </div>
                            </div>

                            <!-- Image Preview -->
                            <div id="imagePreview" class="text-center mb-3" style="display: none;">
                                <img id="previewImg" class="image-preview" alt="Preview" />
                                <p id="fileName" class="text-muted small mt-2"></p>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                                id="uploadBtn"
                                disabled
                            >
                                <i class="bi bi-upload"></i>
                                Upload Image
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Test Results -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-check-circle"></i>
                            Test Results
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="testResults">
                            <p class="text-muted">Upload an image to see the test results here.</p>
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
                        <a href="/bulk-image-upload" class="btn btn-outline-success">
                            <i class="bi bi-images"></i>
                            Bulk Upload
                        </a>
                        <a href="/test-vehicle-image-system" class="btn btn-outline-info">
                            <i class="bi bi-gear"></i>
                            Test System
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedVehicleId = null;
        let selectedVehicleName = '';

        // Select vehicle
        function selectVehicle(id, name) {
            selectedVehicleId = id;
            selectedVehicleName = name;
            document.getElementById('selectedVehicle').value = name;
            document.getElementById('selectedVehicleId').value = id;

            // Enable upload button if image is selected
            const imageFile = document.getElementById('image').files[0];
            if (imageFile) {
                document.getElementById('uploadBtn').disabled = false;
            }
        }

        // Image preview
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const fileName = document.getElementById('fileName');
            const uploadBtn = document.getElementById('uploadBtn');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    fileName.textContent = file.name;
                    preview.style.display = 'block';

                    // Enable upload button if vehicle is selected
                    if (selectedVehicleId) {
                        uploadBtn.disabled = false;
                    }
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
                uploadBtn.disabled = true;
            }
        });

        // Upload image
        document.getElementById('uploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!selectedVehicleId) {
                showStatus('Please select a vehicle first', 'error');
                return;
            }

            const imageFile = document.getElementById('image').files[0];
            if (!imageFile) {
                showStatus('Please select an image', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('image', imageFile);

            const uploadBtn = document.getElementById('uploadBtn');
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Uploading...';

            try {
                const response = await fetch(`/vehicles/${selectedVehicleId}/image`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showStatus(result.message, 'success');

                    // Show test results
                    const testResults = document.getElementById('testResults');
                    testResults.innerHTML = `
                        <div class="alert alert-success">
                            <h6>✅ Upload Successful!</h6>
                            <p><strong>Vehicle:</strong> ${selectedVehicleName}</p>
                            <p><strong>Image Path:</strong> ${result.image_path}</p>
                            <p><strong>Image URL:</strong> <a href="${result.image_url}" target="_blank">${result.image_url}</a></p>
                            <hr>
                            <p><strong>Next Steps:</strong></p>
                            <ol>
                                <li>Go to <a href="/vehicles" target="_blank">Vehicles Page</a></li>
                                <li>Look for ${selectedVehicleName}</li>
                                <li>You should see the uploaded image</li>
                                <li>If not, try refreshing the page</li>
                            </ol>
                        </div>
                    `;

                    // Clear form
                    document.getElementById('image').value = '';
                    document.getElementById('imagePreview').style.display = 'none';
                } else {
                    showStatus(result.message, 'error');
                }
            } catch (error) {
                showStatus('Failed to upload image. Please try again.', 'error');
            } finally {
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = '<i class="bi bi-upload"></i> Upload Image';
            }
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
