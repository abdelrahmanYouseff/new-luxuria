<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Image Management - {{ $vehicle->make }} {{ $vehicle->model }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .vehicle-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .vehicle-info h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .vehicle-info p {
            margin: 5px 0;
            color: #666;
        }
        .image-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .current-image {
            max-width: 400px;
            max-height: 300px;
            border: 2px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .no-image {
            width: 400px;
            height: 300px;
            background: #f8f9fa;
            border: 2px dashed #ddd;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 18px;
            margin: 0 auto 20px;
        }
        .upload-form {
            margin-bottom: 20px;
        }
        .file-input {
            display: none;
        }
        .upload-btn {
            background: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        .upload-btn:hover {
            background: #0056b3;
        }
        .remove-btn {
            background: #dc3545;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        .remove-btn:hover {
            background: #c82333;
        }
        .remove-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        .message {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: none;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .loading {
            display: none;
            text-align: center;
            color: #666;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vehicle Image Management</h1>
            <p>Manage images for vehicle: <strong>{{ $vehicle->make }} {{ $vehicle->model }}</strong></p>
        </div>

        <div class="vehicle-info">
            <h3>Vehicle Details</h3>
            <p><strong>Make:</strong> {{ $vehicle->make }}</p>
            <p><strong>Model:</strong> {{ $vehicle->model }}</p>
            <p><strong>Plate Number:</strong> {{ $vehicle->plate_number ?? 'N/A' }}</p>
            <p><strong>Year:</strong> {{ $vehicle->year ?? 'N/A' }}</p>
            <p><strong>Status:</strong> {{ $vehicle->status ?? 'N/A' }}</p>
        </div>

        <div class="image-section">
            <h3>Current Image</h3>
            @if($vehicle->image && $vehicle->image_url)
                <img src="{{ $vehicle->image_url }}" alt="{{ $vehicle->make }} {{ $vehicle->model }}" class="current-image">
            @else
                <div class="no-image">
                    No image uploaded
                </div>
            @endif
        </div>

        <div class="message" id="message"></div>

        <div class="upload-form">
            <form id="uploadForm" enctype="multipart/form-data">
                <input type="file" id="imageInput" class="file-input" accept="image/*">
                <button type="button" class="upload-btn" onclick="document.getElementById('imageInput').click()">
                    Choose Image
                </button>
                <button type="button" class="upload-btn" onclick="uploadImage()" id="uploadBtn" style="display: none;">
                    Upload Image
                </button>
                <button type="button" class="remove-btn" onclick="removeImage()" id="removeBtn"
                        {{ !$vehicle->image ? 'disabled' : '' }}>
                    Remove Image
                </button>
            </form>
        </div>

        <div class="loading" id="loading">
            Processing...
        </div>

        <a href="/vehicles" class="back-link">← Back to Vehicles</a>
    </div>

    <script>
        const vehicleId = {{ $vehicle->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Show file input when file is selected
        document.getElementById('imageInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                document.getElementById('uploadBtn').style.display = 'inline-block';
            } else {
                document.getElementById('uploadBtn').style.display = 'none';
            }
        });

        function showMessage(message, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.textContent = message;
            messageDiv.className = `message ${type}`;
            messageDiv.style.display = 'block';

            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 5000);
        }

        function showLoading(show) {
            document.getElementById('loading').style.display = show ? 'block' : 'none';
        }

        function uploadImage() {
            const fileInput = document.getElementById('imageInput');
            const file = fileInput.files[0];

            if (!file) {
                showMessage('Please select an image first.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('image', file);

            showLoading(true);

            fetch(`/vehicles/${vehicleId}/image`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                showLoading(false);
                if (data.success) {
                    showMessage(data.message, 'success');
                    // Reload page to show new image
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                showLoading(false);
                showMessage('Upload failed: ' + error.message, 'error');
            });
        }

        function removeImage() {
            if (!confirm('Are you sure you want to remove the current image?')) {
                return;
            }

            showLoading(true);

            fetch(`/vehicles/${vehicleId}/image`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                showLoading(false);
                if (data.success) {
                    showMessage(data.message, 'success');
                    // Reload page to show updated state
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                showLoading(false);
                showMessage('Remove failed: ' + error.message, 'error');
            });
        }
    </script>
</body>
</html>
