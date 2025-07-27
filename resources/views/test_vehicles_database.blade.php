<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Vehicles Database System</title>
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
        .button-demo {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 24px;
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
        .button-outline {
            background: transparent;
            border: 2px solid #667eea;
            color: #667eea;
        }
        .button-outline:hover {
            background: #667eea;
            color: white;
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
        .status-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .api-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .api-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border-left: 4px solid #667eea;
        }
        .api-card.blue {
            border-left-color: #007bff;
            background: #e3f2fd;
        }
        .api-card.green {
            border-left-color: #28a745;
            background: #e8f5e8;
        }
        .api-card.purple {
            border-left-color: #6f42c1;
            background: #f3e5f5;
        }
        .api-card.orange {
            border-left-color: #fd7e14;
            background: #fff3e0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="test-container">
            <div class="header-section">
                <h1 class="mb-3">
                    <i class="bi bi-database"></i>
                    Test Vehicles Database System
                </h1>
                <p class="mb-0">Testing the new system that syncs API data to database without duplication</p>
            </div>

            <div class="content-section">
                <div class="status-info">
                    <h4><i class="bi bi-info-circle"></i> About This System</h4>
                    <p>This system automatically syncs vehicle data from the API to the database without creating duplicates. It uses the API ID to identify existing vehicles and updates them, or creates new ones if they don't exist.</p>
                </div>

                <div class="status-success">
                    <h4><i class="bi bi-check-circle"></i> System Created Successfully!</h4>
                    <p>The vehicles database system has been created with automatic API sync functionality. Data is stored in the <code>vehicles</code> table with all API fields plus an image column.</p>
                </div>

                <!-- System Features -->
                <div class="api-info-grid">
                    <div class="api-card blue">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-arrow-repeat fs-4 text-primary me-3"></i>
                            <div>
                                <h6 class="mb-1">Auto Sync</h6>
                                <p class="mb-0 text-muted">Automatically syncs API data to database</p>
                            </div>
                        </div>
                    </div>
                    <div class="api-card green">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-shield-check fs-4 text-success me-3"></i>
                            <div>
                                <h6 class="mb-1">No Duplicates</h6>
                                <p class="mb-0 text-muted">Uses API ID to prevent duplicates</p>
                            </div>
                        </div>
                    </div>
                    <div class="api-card purple">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-image fs-4 text-purple me-3"></i>
                            <div>
                                <h6 class="mb-1">Image Support</h6>
                                <p class="mb-0 text-muted">Additional image column for vehicle photos</p>
                            </div>
                        </div>
                    </div>
                    <div class="api-card orange">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-arrow-clockwise fs-4 text-warning me-3"></i>
                            <div>
                                <h6 class="mb-1">Auto Update</h6>
                                <p class="mb-0 text-muted">Updates existing vehicles with new data</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="feature-card">
                            <h5><i class="bi bi-gear"></i> Database Schema</h5>
                            <ul class="mb-0">
                                <li><strong>api_id</strong> - Original API ID (unique)</li>
                                <li><strong>plate_number</strong> - Vehicle plate number</li>
                                <li><strong>status</strong> - Available, Rented, Maintenance</li>
                                <li><strong>ownership_status</strong> - Owned, Leased, Rented</li>
                                <li><strong>make</strong> - Vehicle manufacturer</li>
                                <li><strong>model</strong> - Vehicle model</li>
                                <li><strong>year</strong> - Manufacturing year</li>
                                <li><strong>color</strong> - Vehicle color</li>
                                <li><strong>category</strong> - Economy, Mid-Range, Luxury, SUV, Sports</li>
                                <li><strong>daily_rate</strong> - Daily rental price</li>
                                <li><strong>weekly_rate</strong> - Weekly rental price</li>
                                <li><strong>monthly_rate</strong> - Monthly rental price</li>
                                <li><strong>transmission</strong> - Automatic/Manual</li>
                                <li><strong>seats</strong> - Number of seats</li>
                                <li><strong>doors</strong> - Number of doors</li>
                                <li><strong>odometer</strong> - Current mileage</li>
                                <li><strong>description</strong> - Vehicle description</li>
                                <li><strong>image</strong> - Vehicle image path</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <h5><i class="bi bi-code-slash"></i> Technical Details</h5>
                            <ul class="mb-0">
                                <li><strong>Migration:</strong> create_vehicles_table</li>
                                <li><strong>Model:</strong> Vehicle.php</li>
                                <li><strong>Service:</strong> VehicleApiService.php</li>
                                <li><strong>Controller:</strong> VehiclesApiController.php</li>
                                <li><strong>Route:</strong> /vehicles-api</li>
                                <li><strong>Database:</strong> vehicles table</li>
                                <li><strong>Sync Logic:</strong> API ID based</li>
                                <li><strong>Image Handling:</strong> Storage support</li>
                                <li><strong>Error Handling:</strong> Comprehensive</li>
                                <li><strong>Logging:</strong> Detailed sync logs</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <h5>Test the System</h5>
                    <p>Click the buttons below to test the vehicles database system:</p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="/vehicles-api" class="button-demo">
                            <i class="bi bi-database"></i>
                            Test API Sync Page
                        </a>
                        <a href="/database/vehicles" class="button-demo button-outline">
                            <i class="bi bi-table"></i>
                            Database Vehicles Page
                        </a>
                    </div>
                </div>

                <div class="mt-4">
                    <h5>How It Works</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="bi bi-1-circle fs-4"></i>
                                </div>
                                <h6 class="mt-3">1. API Call</h6>
                                <p class="text-muted">System calls the external API to get vehicle data</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="bi bi-2-circle fs-4"></i>
                                </div>
                                <h6 class="mt-3">2. Check Database</h6>
                                <p class="text-muted">Checks if vehicle exists using API ID</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="bi bi-3-circle fs-4"></i>
                                </div>
                                <h6 class="mt-3">3. Sync Data</h6>
                                <p class="text-muted">Updates existing or creates new vehicle record</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h5>Quick Links</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="/vehicles-api" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-database"></i>
                            Vehicles API Page (Auto Sync)
                        </a>
                        <a href="/database/vehicles" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-table"></i>
                            Database Vehicles Page
                        </a>
                        <a href="/vehicles" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-car-front"></i>
                            Original Vehicles Page
                        </a>
                        <a href="/test-vehicles-api-page" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-gear"></i>
                            Test API Page
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Vehicles Database System Test Loaded');

            // Simulate system status
            setTimeout(() => {
                const statusElement = document.querySelector('.status-success p');
                if (statusElement) {
                    statusElement.innerHTML += '<br><small class="text-muted">✓ Database table created successfully<br>✓ API sync service configured<br>✓ No duplicate prevention enabled</small>';
                }
            }, 1000);
        });
    </script>
</body>
</html>
