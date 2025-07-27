<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test API Vehicles</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        .success { background-color: #f0fdf4; border-color: #bbf7d0; }
        .info { background-color: #e0f2fe; border-color: #0284c7; }
        .warning { background-color: #fef3c7; border-color: #f59e0b; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        tr:hover {
            background-color: #f9fafb;
        }

        .vehicle-info {
            display: flex;
            align-items: center;
        }
        .vehicle-image {
            width: 48px;
            height: 48px;
            background: #dbeafe;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: #1e40af;
            font-size: 20px;
        }
        .vehicle-details h4 {
            margin: 0 0 4px 0;
            font-size: 14px;
            font-weight: 600;
        }
        .vehicle-details p {
            margin: 0;
            font-size: 12px;
            color: #6b7280;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
        }
        .status-available { background: #dcfce7; color: #166534; }
        .status-rented { background: #fee2e2; color: #991b1b; }
        .status-maintenance { background: #fef3c7; color: #92400e; }

        .category-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }
        .category-economy { background: #dbeafe; color: #1e40af; }
        .category-luxury { background: #f3e8ff; color: #7c3aed; }
        .category-mid-range { background: #dcfce7; color: #166534; }

        .ownership-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }
        .ownership-owned { background: #dcfce7; color: #166534; }
        .ownership-leased { background: #fef3c7; color: #92400e; }

        .price {
            font-weight: 600;
            color: #059669;
        }

        .api-info {
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .demo-buttons {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }
        .demo-btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
        }
        .demo-btn:hover { background: #2563eb; }

        .loading {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }

        .error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 16px;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚗 Test API Vehicles</h1>

        <div class="api-info">
            <h3>API Information</h3>
            <p><strong>Endpoint:</strong> https://rlapp.rentluxuria.com/api/vehicles</p>
            <p><strong>Header:</strong> X-RLAPP-KEY: 28izx09iasdasd</p>
            <p><strong>Status:</strong> <span id="api-status">Loading...</span></p>
            <p><strong>Total Vehicles:</strong> <span id="total-vehicles">-</span></p>
        </div>

        <div class="test-section">
            <h3>Vehicles from Real API</h3>
            <div id="vehicles-container">
                <div class="loading">Loading vehicles from API...</div>
            </div>
        </div>

        <div class="test-section">
            <h3>Quick Links</h3>
            <div class="demo-buttons">
                <a href="/vehicles" target="_blank"><button class="demo-btn">Vehicles Page (Vue)</button></a>
                <a href="/test-vehicles-api" target="_blank"><button class="demo-btn">Raw API Response</button></a>
                <a href="/test-dropdown-actions" target="_blank"><button class="demo-btn">Test Dropdown Actions</button></a>
            </div>
        </div>

        <div class="warning test-section">
            <h3>API Data Structure</h3>
            <ul>
                <li>✅ <strong>Real API:</strong> Connected to https://rlapp.rentluxuria.com/api/vehicles</li>
                <li>✅ <strong>Authentication:</strong> Using X-RLAPP-KEY header</li>
                <li>✅ <strong>Data Transformation:</strong> Converting API format to frontend format</li>
                <li>✅ <strong>Error Handling:</strong> Fallback to mock data if API fails</li>
                <li>✅ <strong>Status Mapping:</strong> available → Available, rented → Rented, etc.</li>
                <li>✅ <strong>Category Mapping:</strong> Economy, Luxury, Mid-Range, etc.</li>
                <li>✅ <strong>Pricing:</strong> Daily rates from API pricing object</li>
                <li>🔄 <strong>Images:</strong> Not provided by API yet</li>
                <li>🔄 <strong>Odometer:</strong> Not provided by API yet</li>
            </ul>
        </div>
    </div>

    <script>
        // Fetch vehicles from API
        async function loadVehicles() {
            try {
                const response = await fetch('/test-vehicles-api');
                const data = await response.json();

                document.getElementById('api-status').textContent = data.status;
                document.getElementById('total-vehicles').textContent = data.totalVehicles || '-';

                if (data.status === 'success' && data.sampleVehicles) {
                    displayVehicles(data.sampleVehicles);
                } else {
                    document.getElementById('vehicles-container').innerHTML =
                        '<div class="error">Failed to load vehicles: ' + (data.error || 'Unknown error') + '</div>';
                }
            } catch (error) {
                document.getElementById('api-status').textContent = 'Error';
                document.getElementById('vehicles-container').innerHTML =
                    '<div class="error">Network error: ' + error.message + '</div>';
            }
        }

        function displayVehicles(vehicles) {
            const container = document.getElementById('vehicles-container');

            const table = `
                <table>
                    <thead>
                        <tr>
                            <th>Vehicle</th>
                            <th>Plate Number</th>
                            <th>Status</th>
                            <th>Category</th>
                            <th>Ownership</th>
                            <th>Daily Rate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${vehicles.map(vehicle => {
                            const vehicleInfo = vehicle.vehicle_info || {};
                            const pricing = vehicle.pricing || {};

                            return `
                                <tr>
                                    <td>
                                        <div class="vehicle-info">
                                            <div class="vehicle-image">🚗</div>
                                            <div class="vehicle-details">
                                                <h4>${vehicleInfo.make || 'Unknown'}</h4>
                                                <p>${vehicleInfo.model || 'N/A'} • ${vehicleInfo.year || 'N/A'}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="status-badge">${vehicle.plate_number || 'N/A'}</span></td>
                                    <td><span class="status-badge status-${vehicle.status || 'available'}">${vehicle.status || 'Available'}</span></td>
                                    <td><span class="category-badge category-${(vehicleInfo.category || 'economy').toLowerCase().replace(/[^a-z]/g, '-')}">${vehicleInfo.category || 'Economy'}</span></td>
                                    <td><span class="ownership-badge ownership-${vehicle.ownership_status || 'owned'}">${vehicle.ownership_status || 'Owned'}</span></td>
                                    <td><span class="price">AED ${pricing.daily || '0'}</span></td>
                                    <td>
                                        <button onclick="actionClicked('View', '${vehicle.id}')" style="padding: 4px 8px; margin: 2px; border: 1px solid #d1d5db; border-radius: 4px; background: white; cursor: pointer;">👁️</button>
                                        <button onclick="actionClicked('Edit', '${vehicle.id}')" style="padding: 4px 8px; margin: 2px; border: 1px solid #d1d5db; border-radius: 4px; background: white; cursor: pointer;">✏️</button>
                                        <button onclick="actionClicked('Image', '${vehicle.id}')" style="padding: 4px 8px; margin: 2px; border: 1px solid #d1d5db; border-radius: 4px; background: white; cursor: pointer;">📸</button>
                                    </td>
                                </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>
            `;

            container.innerHTML = table;
        }

        function actionClicked(action, vehicleId) {
            alert(`${action} action for Vehicle ${vehicleId}`);
        }

        // Load vehicles when page loads
        loadVehicles();
    </script>
</body>
</html>
