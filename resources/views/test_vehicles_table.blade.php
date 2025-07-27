<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Vehicles Table</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
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

        button {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover { background: #2563eb; }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-available { background: #d1fae5; color: #065f46; }
        .status-rented { background: #fee2e2; color: #991b1b; }
        .status-maintenance { background: #fef3c7; color: #92400e; }

        .category-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .category-economy { background: #dbeafe; color: #1e40af; }
        .category-luxury { background: #f3e8ff; color: #7c3aed; }
        .category-suv { background: #d1fae5; color: #065f46; }

        .ownership-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .ownership-owned { background: #d1fae5; color: #065f46; }
        .ownership-leased { background: #fed7aa; color: #c2410c; }

        .mock-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .mock-table th {
            background: #f9fafb;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .mock-table td {
            padding: 12px;
            border-top: 1px solid #f3f4f6;
        }
        .mock-table tr:hover {
            background: #f9fafb;
        }
        .vehicle-info {
            display: flex;
            align-items: center;
        }
        .vehicle-icon {
            width: 40px;
            height: 40px;
            background: #dbeafe;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: #1e40af;
            font-size: 20px;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        .action-btn {
            padding: 6px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            background: white;
            cursor: pointer;
            font-size: 12px;
        }
        .action-btn:hover {
            background: #f3f4f6;
        }
        .action-btn.delete {
            color: #dc2626;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚗 Test Vehicles Table</h1>

        <div class="info test-section">
            <h3>Vehicles Table Features</h3>
            <p>This page demonstrates the vehicles table with mock data and all features.</p>
        </div>

        <div class="test-section">
            <h3>1. Mock Vehicles Data</h3>
            <p>The table displays 5 sample vehicles with the following information:</p>
            <ul>
                <li><strong>Vehicle:</strong> Name, Model, and Icon</li>
                <li><strong>Plate Number:</strong> License plate with badge styling</li>
                <li><strong>Status:</strong> Available (Green), Rented (Red), Maintenance (Yellow)</li>
                <li><strong>Category:</strong> Economy (Blue), Luxury (Purple), SUV (Green)</li>
                <li><strong>Ownership:</strong> Owned (Green), Leased (Orange)</li>
                <li><strong>Details:</strong> Year, Color, Transmission</li>
                <li><strong>Odometer:</strong> Kilometers with formatting</li>
                <li><strong>Daily Rate:</strong> AED pricing</li>
                <li><strong>Actions:</strong> Edit, View, Delete buttons</li>
            </ul>
        </div>

        <div class="test-section">
            <h3>2. Sample Table Display</h3>
            <table class="mock-table">
                <thead>
                    <tr>
                        <th>Vehicle</th>
                        <th>Plate Number</th>
                        <th>Status</th>
                        <th>Category</th>
                        <th>Ownership</th>
                        <th>Details</th>
                        <th>Odometer</th>
                        <th>Daily Rate</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="vehicle-info">
                                <div class="vehicle-icon">🚗</div>
                                <div>
                                    <div style="font-weight: 500; color: #111827;">Toyota Camry</div>
                                    <div style="font-size: 12px; color: #6b7280;">2023</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="status-badge" style="background: #f3f4f6; color: #374151;">ABC-123</span></td>
                        <td><span class="status-badge status-available">Available</span></td>
                        <td><span class="category-badge category-economy">Economy</span></td>
                        <td><span class="ownership-badge ownership-owned">Owned</span></td>
                        <td>2023 • White • Automatic</td>
                        <td>15,000 km</td>
                        <td><strong>AED 150</strong></td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn">✏️</button>
                                <button class="action-btn">👁️</button>
                                <button class="action-btn delete">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="vehicle-info">
                                <div class="vehicle-icon">🚗</div>
                                <div>
                                    <div style="font-weight: 500; color: #111827;">BMW X5</div>
                                    <div style="font-size: 12px; color: #6b7280;">2022</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="status-badge" style="background: #f3f4f6; color: #374151;">XYZ-789</span></td>
                        <td><span class="status-badge status-rented">Rented</span></td>
                        <td><span class="category-badge category-luxury">Luxury</span></td>
                        <td><span class="ownership-badge ownership-owned">Owned</span></td>
                        <td>2022 • Black • Automatic</td>
                        <td>25,000 km</td>
                        <td><strong>AED 350</strong></td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn">✏️</button>
                                <button class="action-btn">👁️</button>
                                <button class="action-btn delete">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="vehicle-info">
                                <div class="vehicle-icon">🚗</div>
                                <div>
                                    <div style="font-weight: 500; color: #111827;">Honda CR-V</div>
                                    <div style="font-size: 12px; color: #6b7280;">2023</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="status-badge" style="background: #f3f4f6; color: #374151;">DEF-456</span></td>
                        <td><span class="status-badge status-maintenance">Maintenance</span></td>
                        <td><span class="category-badge category-suv">SUV</span></td>
                        <td><span class="ownership-badge ownership-leased">Leased</span></td>
                        <td>2023 • Silver • Automatic</td>
                        <td>18,000 km</td>
                        <td><strong>AED 200</strong></td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn">✏️</button>
                                <button class="action-btn">👁️</button>
                                <button class="action-btn delete">🗑️</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="test-section">
            <h3>3. Quick Links</h3>
            <p>Test the actual vehicles page:</p>
            <a href="/vehicles" target="_blank"><button>Vehicles Page</button></a>
            <a href="/dashboard" target="_blank"><button>Dashboard</button></a>
            <a href="/test-vehicles" target="_blank"><button>Test Vehicles</button></a>
        </div>

        <div class="warning test-section">
            <h3>4. Features to Implement</h3>
            <ul>
                <li>✅ <strong>Table Display:</strong> Complete with all columns</li>
                <li>✅ <strong>Status Badges:</strong> Color-coded status indicators</li>
                <li>✅ <strong>Category Badges:</strong> Different colors for each category</li>
                <li>✅ <strong>Action Buttons:</strong> Edit, View, Delete functionality</li>
                <li>✅ <strong>Responsive Design:</strong> Works on all screen sizes</li>
                <li>🔄 <strong>Add Vehicle:</strong> Form to add new vehicles</li>
                <li>🔄 <strong>Edit Vehicle:</strong> Modal/form to edit existing vehicles</li>
                <li>🔄 <strong>Delete Confirmation:</strong> Proper confirmation dialogs</li>
                <li>🔄 <strong>Search & Filter:</strong> Search and filter functionality</li>
                <li>🔄 <strong>Pagination:</strong> Handle large datasets</li>
            </ul>
        </div>
    </div>

    <script>
        // Add some interactivity to the mock table
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.textContent.includes('✏️') ? 'Edit' :
                              this.textContent.includes('👁️') ? 'View' : 'Delete';
                alert(`${action} action clicked! This would open the appropriate modal/form.`);
            });
        });
    </script>
</body>
</html>
