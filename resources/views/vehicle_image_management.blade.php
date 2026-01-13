<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manage vehicle images for {{ $vehicle->make }} {{ $vehicle->model }} in the Luxuria UAE fleet." />
    <title>Vehicle Image Management - {{ $vehicle->make }} {{ $vehicle->model }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .content {
            padding: 40px;
        }

        .vehicle-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 40px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .vehicle-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .vehicle-info h3 {
            color: #333;
            font-size: 1.5rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .vehicle-info h3 i {
            color: #667eea;
        }

        .vehicle-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .detail-item {
            background: white;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .detail-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .detail-item strong {
            color: #667eea;
            display: block;
            margin-bottom: 5px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-item span {
            color: #333;
            font-size: 1.1rem;
        }

        .image-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .image-section h3 {
            color: #333;
            font-size: 1.5rem;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .image-section h3 i {
            color: #667eea;
        }

        .current-image {
            max-width: 500px;
            max-height: 400px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .current-image:hover {
            transform: scale(1.02);
        }

        .drop-zone {
            width: 500px;
            height: 300px;
            margin: 0 auto 30px;
            border: 3px dashed #667eea;
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .drop-zone.dragover {
            border-color: #764ba2;
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            transform: scale(1.02);
        }

        .drop-zone i {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .drop-zone.dragover i {
            transform: scale(1.1);
        }

        .drop-zone-text {
            font-size: 1.2rem;
            color: #666;
            text-align: center;
            margin-bottom: 10px;
        }

        .drop-zone-subtext {
            font-size: 0.9rem;
            color: #999;
        }

        .file-input {
            display: none;
        }

        .upload-controls {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 107, 107, 0.3);
        }

        .btn-danger:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
        }

        .message {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: none;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .loading {
            display: none;
            text-align: center;
            color: #667eea;
            font-size: 1.1rem;
            margin: 20px 0;
        }

        .loading i {
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 30px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: #764ba2;
            transform: translateX(-5px);
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
            margin: 20px 0;
            display: none;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            width: 0%;
            transition: width 0.3s ease;
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 15px;
            }

            .header {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .content {
                padding: 30px 20px;
            }

            .drop-zone {
                width: 100%;
                max-width: 400px;
            }

            .upload-controls {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-car"></i> Vehicle Image Management</h1>
            <p>Manage images for vehicle: <strong>{{ $vehicle->make }} {{ $vehicle->model }}</strong></p>
        </div>

        <div class="content">
            <div class="vehicle-info">
                <h3><i class="fas fa-info-circle"></i> Vehicle Details</h3>
                <div class="vehicle-details">
                    <div class="detail-item">
                        <strong>Make</strong>
                        <span>{{ $vehicle->make }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Model</strong>
                        <span>{{ $vehicle->model }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Plate Number</strong>
                        <span>{{ $vehicle->plate_number ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Year</strong>
                        <span>{{ $vehicle->year ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Status</strong>
                        <span>{{ $vehicle->status ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Category</strong>
                        <span>{{ $vehicle->category ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="image-section">
                <h3><i class="fas fa-image"></i> Current Image</h3>
                @if($vehicle->image && $vehicle->image_url)
                    <img src="{{ $vehicle->image_url }}" alt="{{ $vehicle->make }} {{ $vehicle->model }}" class="current-image">
                @else
                    <div class="drop-zone" id="dropZone">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <div class="drop-zone-text">No image uploaded</div>
                        <div class="drop-zone-subtext">Drag & drop an image here or click to browse</div>
                    </div>
                @endif
            </div>

            <div class="message" id="message"></div>

            <div class="progress-bar" id="progressBar">
                <div class="progress-fill" id="progressFill"></div>
            </div>

            <div class="upload-controls">
                <input type="file" id="imageInput" class="file-input" accept="image/*">
                <button type="button" class="btn btn-primary" onclick="document.getElementById('imageInput').click()">
                    <i class="fas fa-folder-open"></i> Choose Image
                </button>
                <button type="button" class="btn btn-primary" onclick="uploadImage()" id="uploadBtn" style="display: none;">
                    <i class="fas fa-upload"></i> Upload Image
                </button>
                <button type="button" class="btn btn-danger" onclick="removeImage()" id="removeBtn"
                        {{ !$vehicle->image ? 'disabled' : '' }}>
                    <i class="fas fa-trash"></i> Remove Image
                </button>
                <a href="/vehicles" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Vehicles
                </a>
            </div>

            <div class="loading" id="loading">
                <i class="fas fa-spinner"></i> Processing...
            </div>
        </div>
    </div>

    <script>
        const vehicleId = {{ $vehicle->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let isUploading = false;

        // Drag and Drop functionality
        const dropZone = document.getElementById('dropZone');
        const imageInput = document.getElementById('imageInput');

        if (dropZone) {
            // Prevent default drag behaviors
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });

            // Highlight drop zone when item is dragged over it
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });

            // Handle dropped files
            dropZone.addEventListener('drop', handleDrop, false);

            // Handle click to browse
            dropZone.addEventListener('click', () => imageInput.click());
        }

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        function highlight(e) {
            dropZone.classList.add('dragover');
        }

        function unhighlight(e) {
            dropZone.classList.remove('dragover');
        }

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        }

        function handleFiles(files) {
            if (files.length > 0) {
                imageInput.files = files;
                showUploadButton();
                showMessage('File selected: ' + files[0].name, 'success');
            }
        }

        // Show file input when file is selected
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                showUploadButton();
                showMessage('File selected: ' + file.name, 'success');
            } else {
                hideUploadButton();
            }
        });

        function showUploadButton() {
            const uploadBtn = document.getElementById('uploadBtn');
            if (uploadBtn) {
                uploadBtn.style.display = 'inline-flex';
            }
        }

        function hideUploadButton() {
            const uploadBtn = document.getElementById('uploadBtn');
            if (uploadBtn) {
                uploadBtn.style.display = 'none';
            }
        }

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

        function showProgress(show) {
            document.getElementById('progressBar').style.display = show ? 'block' : 'none';
        }

        function updateProgress(percent) {
            document.getElementById('progressFill').style.width = percent + '%';
        }

        function uploadImage() {
            if (isUploading) return;

            const fileInput = document.getElementById('imageInput');
            const file = fileInput.files[0];

            if (!file) {
                showMessage('Please select an image first.', 'error');
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                showMessage('Please select a valid image file (JPEG, PNG, JPG, GIF, WebP).', 'error');
                return;
            }

            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                showMessage('File size must be less than 2MB.', 'error');
                return;
            }

            isUploading = true;
            const formData = new FormData();
            formData.append('image', file);

            showLoading(true);
            showProgress(true);
            updateProgress(0);

            // Simulate progress
            const progressInterval = setInterval(() => {
                updateProgress(Math.min(90, Math.random() * 100));
            }, 200);

            fetch(`/vehicles/${vehicleId}/image`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                clearInterval(progressInterval);
                updateProgress(100);
                showLoading(false);
                showProgress(false);
                isUploading = false;

                if (data.success) {
                    showMessage(data.message, 'success');
                    // Reload page to show new image
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                clearInterval(progressInterval);
                showLoading(false);
                showProgress(false);
                isUploading = false;
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
                    }, 1500);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                showLoading(false);
                showMessage('Remove failed: ' + error.message, 'error');
            });
        }

        // Add some nice animations on page load
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.detail-item, .btn, .vehicle-info, .image-section');
            elements.forEach((element, index) => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    element.style.transition = 'all 0.6s ease';
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
