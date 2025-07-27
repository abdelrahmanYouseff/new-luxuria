<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Image Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select, input[type="file"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .result { margin: 20px 0; padding: 15px; border-radius: 4px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .image-preview { max-width: 200px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚗 Quick Image Upload Test</h1>

        @if(session('success'))
            <div class="result success">
                <h3>✅ Success!</h3>
                <p>{{ session('success') }}</p>
                @if(session('image_path'))
                    <p><strong>Path:</strong> {{ session('image_path') }}</p>
                    <p><strong>URL:</strong> <a href="{{ asset('storage/' . session('image_path')) }}" target="_blank">{{ asset('storage/' . session('image_path')) }}</a></p>
                    <img src="{{ asset('storage/' . session('image_path')) }}" alt="Uploaded" class="image-preview">
                @endif
            </div>
        @endif

        @if(session('error'))
            <div class="result error">
                <h3>❌ Error!</h3>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <form method="POST" action="/simple-upload" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="vehicle_id">Select Vehicle:</label>
                <select id="vehicle_id" name="vehicle_id" required>
                    <option value="">Choose a vehicle...</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">
                            {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->plate_number }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="image">Select Image:</label>
                <input type="file" id="image" name="image" accept="image/*" required>
            </div>

            <button type="submit">📤 Upload Image</button>
        </form>

        <div style="margin-top: 30px;">
            <h3>🔗 Test Links:</h3>
            <p><a href="/vehicles" target="_blank">View Vehicles Page</a></p>
            <p><a href="/test-image-urls" target="_blank">Test Image URLs</a></p>
            <p><a href="/storage/vehicles" target="_blank">View Storage Folder</a></p>
        </div>
    </div>
</body>
</html>
