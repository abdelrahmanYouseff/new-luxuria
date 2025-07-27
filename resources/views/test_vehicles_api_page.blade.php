<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Vehicles API Page</title>
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
            max-width: 1000px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="test-container">
            <div class="header-section">
                <h1 class="mb-3">
                    <i class="bi bi-database"></i>
                    Test Vehicles API Page
                </h1>
                <p class="mb-0">Testing the new Vehicles API page with sidebar and header</p>
            </div>

            <div class="content-section">
                <div class="status-info">
                    <h4><i class="bi bi-info-circle"></i> About This Test</h4>
                    <p>This page simulates the new Vehicles API page that will open when you click the "Vehicles API" button. It will have the full Inertia layout with sidebar and header.</p>
                </div>

                <!-- Simulated Header -->
                <div class="row align-items-center justify-content-between mb-4">
                    <div class="col">
                        <h2 class="h3 mb-0">
                            <i class="bi bi-database"></i>
                            Vehicles API
                        </h2>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex gap-3">
                            <button class="button-demo button-outline">
                                <i class="bi bi-arrow-clockwise"></i>
                                Refresh API
                            </button>
                        </div>
                    </div>
                </div>

                <div class="status-success">
                    <h4><i class="bi bi-check-circle"></i> Page Created Successfully!</h4>
                    <p>The Vehicles API page has been created with full Inertia layout including sidebar and header. It will show API data in a table format.</p>
                </div>

                <!-- API Information Cards -->
                <div class="api-info-grid">
                    <div class="api-card blue">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-globe fs-4 text-primary me-3"></i>
                            <div>
                                <h6 class="mb-1">API Endpoint</h6>
                                <p class="mb-0 text-muted">https://rlapp.rentluxuria.com/api/vehicles</p>
                            </div>
                        </div>
                    </div>
                    <div class="api-card green">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-key fs-4 text-success me-3"></i>
                            <div>
                                <h6 class="mb-1">API Key</h6>
                                <p class="mb-0 text-muted">28izx09iasdasd</p>
                            </div>
                        </div>
                    </div>
                    <div class="api-card purple">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-database fs-4 text-purple me-3"></i>
                            <div>
                                <h6 class="mb-1">Total Vehicles</h6>
                                <p class="mb-0 text-muted">Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="feature-card">
                            <h5><i class="bi bi-gear"></i> Page Features</h5>
                            <ul class="mb-0">
                                <li>Full Inertia layout with sidebar</li>
                                <li>API information cards</li>
                                <li>Vehicles data table</li>
                                <li>Export JSON functionality</li>
                                <li>Raw data viewer</li>
                                <li>Copy vehicle ID</li>
                                <li>Test endpoint links</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <h5><i class="bi bi-code-slash"></i> Technical Details</h5>
                            <ul class="mb-0">
                                <li>Vue.js component: VehiclesApi.vue</li>
                                <li>Controller: VehiclesApiController.php</li>
                                <li>Route: /vehicles-api</li>
                                <li>TypeScript support</li>
                                <li>Bootstrap Icons</li>
                                <li>Responsive design</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <h5>Test the Button</h5>
                    <p>Click the "Vehicles API" button in the Vehicles page to test the functionality:</p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="/vehicles" class="button-demo">
                            <i class="bi bi-car-front"></i>
                            Go to Vehicles Page
                        </a>
                        <a href="/vehicles-api" class="button-demo button-outline">
                            <i class="bi bi-database"></i>
                            Direct to API Page
                        </a>
                    </div>
                </div>

                <div class="mt-4">
                    <h5>Quick Links</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="/vehicles" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-car-front"></i>
                            Vehicles Page (Requires Login)
                        </a>
                        <a href="/vehicles-api" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-database"></i>
                            Vehicles API Page (Requires Login)
                        </a>
                        <a href="/test-api-vehicles" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-globe"></i>
                            Raw API Test Page
                        </a>
                        <a href="/test-vehicles" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-gear"></i>
                            Vehicles Test Page
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Vehicles API Page Test Loaded');

            // Simulate API data loading
            setTimeout(() => {
                const totalVehicles = document.querySelector('.api-card.purple p');
                if (totalVehicles) {
                    totalVehicles.textContent = '4 vehicles (Mock Data)';
                }
            }, 1000);
        });
    </script>
</body>
</html>
