<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vehicle Image Management - {{ $vehicle->make }} {{ $vehicle->model }}</title>
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
            max-width: 1000px;
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
        .vehicle-info {
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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="mb-2">
                            <i class="bi bi-image"></i>
                            Vehicle Image Management
                        </h1>
                        <p class="mb-0">
                            Managing image for {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->plate_number }})
                        </p>
                    </div>
                    <a href="/vehicles" class="btn btn-outline-light">
                        <i class="bi bi-arrow-left"></i>
                        Back to Vehicles
                    </a>
                </div>
            </div>

            <div class="content-section">
                <!-- Status Messages -->
                <div id="statusMessage" style="display: none;" class="status-success">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle me-2"></i>
                        <span id="statusText"></span>
                    </div>
                </div>

                <div class="row">
                    <!-- Current Image Section -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-image"></i>
                                    Current Image
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                @if($vehicle->image)
                                    <img
                                        src="{{ $vehicle->image_url }}"
                                        alt="{{ $vehicle->make }} {{ $vehicle->model }}"
                                        class="image-preview mb-3"
                                    />
                                    <p class="text-muted small">{{ $vehicle->image }}</p>
                                    <button
                                        onclick="removeImage()"
                                        class="btn btn-danger btn-sm"
                                        id="removeBtn"
                                    >
                                        <i class="bi bi-trash"></i>
                                        Remove Image
                                    </button>
                                @else
                                    <div class="upload-area mb-3">
                                        <i class="bi bi-image fs-1 text-muted mb-3"></i>
                                        <p class="text-muted">No image uploaded</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Upload Section -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-upload"></i>
                                    Upload New Image
                                </h5>
                            </div>
                            <div class="card-body">
                                <form id="uploadForm" enctype="multipart/form-data">
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
                    </div>
                </div>

                <!-- Vehicle Information -->
                <div class="vehicle-info">
                    <h5><i class="bi bi-info-circle"></i> Vehicle Information</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Make:</strong><br>
                            {{ $vehicle->make }}
                        </div>
                        <div class="col-md-3">
                            <strong>Model:</strong><br>
                            {{ $vehicle->model }}
                        </div>
                        <div class="col-md-3">
                            <strong>Plate Number:</strong><br>
                            {{ $vehicle->plate_number }}
                        </div>
                        <div class="col-md-3">
                            <strong>Image Status:</strong><br>
                            <span class="badge {{ $vehicle->image ? 'bg-success' : 'bg-danger' }}">
                                {{ $vehicle->image ? 'Has Image' : 'No Image' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="text-center mt-4">
                    <h5>Quick Links</h5>
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        <a href="/vehicles" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-car-front"></i>
                            View All Vehicles
                        </a>
                        <a href="/database/vehicles" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-database"></i>
                            Database Management
                        </a>
                        <a href="/storage/vehicles" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-folder"></i>
                            View Storage
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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
                    uploadBtn.disabled = false;
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

            const formData = new FormData();
            const imageFile = document.getElementById('image').files[0];

            if (!imageFile) {
                showStatus('Please select an image', 'error');
                return;
            }

            formData.append('image', imageFile);

            const uploadBtn = document.getElementById('uploadBtn');
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Uploading...';

            try {
                const response = await fetch(`/vehicles/{{ $vehicle->id }}/image`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showStatus(result.message, 'success');
                    // Reload page after 2 seconds to show new image
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
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

        // Remove image
        async function removeImage() {
            if (!confirm('Are you sure you want to remove this image?')) {
                return;
            }

            const removeBtn = document.getElementById('removeBtn');
            removeBtn.disabled = true;
            removeBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Removing...';

            try {
                const response = await fetch(`/vehicles/{{ $vehicle->id }}/image`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showStatus(result.message, 'success');
                    // Reload page after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showStatus(result.message, 'error');
                }
            } catch (error) {
                showStatus('Failed to remove image. Please try again.', 'error');
            } finally {
                removeBtn.disabled = false;
                removeBtn.innerHTML = '<i class="bi bi-trash"></i> Remove Image';
            }
        }

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
