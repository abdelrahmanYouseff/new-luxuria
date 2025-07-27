<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Vehicles API Button</title>
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
            max-width: 800px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="test-container">
            <div class="header-section">
                <h1 class="mb-3">
                    <i class="bi bi-car-front"></i>
                    Test Vehicles API Button
                </h1>
                <p class="mb-0">Testing the "Vehicles API" button functionality</p>
            </div>

            <div class="content-section">
                <div class="status-info">
                    <h4><i class="bi bi-info-circle"></i> About This Test</h4>
                    <p>This page simulates the Vehicles page header with the new "Vehicles API" button. The button should open the API test page in a new tab.</p>
                </div>

                <!-- Simulated Header -->
                <div class="row align-items-center justify-content-between mb-4">
                    <div class="col">
                        <h2 class="h3 mb-0">
                            <i class="bi bi-car-front"></i>
                            Vehicles
                        </h2>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex gap-3">
                            <a href="/test-api-vehicles" target="_blank" class="button-demo">
                                <i class="bi bi-database"></i>
                                Vehicles API
                            </a>
                            <button class="button-demo button-outline">
                                <i class="bi bi-arrow-clockwise"></i>
                                Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <div class="status-success">
                    <h4><i class="bi bi-check-circle"></i> Button Added Successfully!</h4>
                    <p>The "Vehicles API" button has been added to the Vehicles page header. It will open the API test page in a new tab when clicked.</p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="feature-card">
                            <h5><i class="bi bi-gear"></i> Button Features</h5>
                            <ul class="mb-0">
                                <li>Primary button with database icon</li>
                                <li>Opens in new tab</li>
                                <li>Positioned next to Refresh button</li>
                                <li>Responsive design</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <h5><i class="bi bi-code-slash"></i> Technical Details</h5>
                            <ul class="mb-0">
                                <li>Vue.js component updated</li>
                                <li>TypeScript support added</li>
                                <li>Icon from Bootstrap Icons</li>
                                <li>Targets /test-api-vehicles route</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <h5>Test the Button</h5>
                    <p>Click the "Vehicles API" button above to test the functionality:</p>
                    <a href="/test-api-vehicles" target="_blank" class="button-demo">
                        <i class="bi bi-database"></i>
                        Test Vehicles API
                    </a>
                </div>

                <div class="mt-4">
                    <h5>Quick Links</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="/vehicles" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-car-front"></i>
                            Vehicles Page (Requires Login)
                        </a>
                        <a href="/test-api-vehicles" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-database"></i>
                            API Test Page
                        </a>
                        <a href="/test-vehicles" class="btn btn-outline-info btn-sm">
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
            console.log('Vehicles API Button Test Page Loaded');

            // Add click tracking
            const apiButton = document.querySelector('a[href="/test-api-vehicles"]');
            if (apiButton) {
                apiButton.addEventListener('click', function() {
                    console.log('Vehicles API button clicked');
                });
            }
        });
    </script>
</body>
</html>
