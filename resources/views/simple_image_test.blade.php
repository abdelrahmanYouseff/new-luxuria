<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Image Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .result { margin: 20px 0; padding: 15px; border-radius: 5px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Simple Image Upload Test</h1>

        @if(session('success'))
            <div class="result success">
                <h4>✅ Success!</h4>
                <p>{{ session('success') }}</p>
                @if(session('image_path'))
                    <p><strong>Image Path:</strong> {{ session('image_path') }}</p>
                    <p><strong>Image URL:</strong> <a href="{{ asset('storage/' . session('image_path')) }}" target="_blank">{{ asset('storage/' . session('image_path')) }}</a></p>
                    <img src="{{ asset('storage/' . session('image_path')) }}" alt="Uploaded Image" style="max-width: 300px; border: 1px solid #ccc;">
                @endif
            </div>
        @endif

        @if(session('error'))
            <div class="result error">
                <h4>❌ Error!</h4>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Upload Image for Vehicle</h5>

                <form method="POST" action="/simple-upload" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="vehicle_id" class="form-label">Select Vehicle:</label>
                        <select class="form-select" id="vehicle_id" name="vehicle_id" required>
                            <option value="">Choose a vehicle...</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">
                                    {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->plate_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Select Image:</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Upload Image</button>
                </form>
            </div>
        </div>

        <div class="mt-4">
            <h3>Test Links</h3>
            <a href="/vehicles" class="btn btn-outline-primary">View Vehicles Page</a>
            <a href="/test-image-urls" class="btn btn-outline-info">Test Image URLs</a>
            <a href="/storage/vehicles" class="btn btn-outline-secondary">View Storage Folder</a>
        </div>
    </div>
</body>
</html>
