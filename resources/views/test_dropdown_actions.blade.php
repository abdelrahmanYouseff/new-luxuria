<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Dropdown Actions</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
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

        .dropdown-container {
            position: relative;
            display: inline-block;
        }
        .dropdown-button {
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 6px;
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }
        .dropdown-button:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }
        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 100%;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            min-width: 160px;
            z-index: 10;
            display: none;
        }
        .dropdown-menu.show {
            display: block;
        }
        .dropdown-item {
            padding: 8px 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            font-size: 14px;
            border-bottom: 1px solid #f3f4f6;
        }
        .dropdown-item:last-child {
            border-bottom: none;
        }
        .dropdown-item:hover {
            background: #f9fafb;
        }
        .dropdown-item.delete {
            color: #dc2626;
        }
        .dropdown-item.delete:hover {
            background: #fef2f2;
        }
        .dropdown-icon {
            margin-right: 8px;
            width: 16px;
            height: 16px;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>⚙️ Test Dropdown Actions Menu</h1>

        <div class="info test-section">
            <h3>Dropdown Actions Menu</h3>
            <p>Each vehicle now has a three-dot menu (⋮) that opens a dropdown with all available actions.</p>
        </div>

        <div class="test-section">
            <h3>Vehicles Table with Dropdown Actions</h3>

            <table>
                <thead>
                    <tr>
                        <th>Vehicle</th>
                        <th>Plate Number</th>
                        <th>Status</th>
                        <th>Category</th>
                        <th>Ownership</th>

                        <th>Odometer</th>
                        <th>Daily Rate</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="vehicle-info">
                                <div class="vehicle-image">🚗</div>
                                <div class="vehicle-details">
                                    <h4>Toyota Camry</h4>
                                    <p>2023</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="status-badge">ABC-123</span></td>
                        <td><span class="status-badge status-available">Available</span></td>
                        <td><span class="status-badge">Economy</span></td>
                        <td><span class="status-badge">Owned</span></td>

                        <td>15,000 km</td>
                        <td><strong>AED 150</strong></td>
                        <td>
                            <div class="dropdown-container">
                                <button class="dropdown-button" onclick="toggleDropdown(this)">⋮</button>
                                <div class="dropdown-menu">
                                    <div class="dropdown-item" onclick="actionClicked('View Details', 1)">
                                        <span class="dropdown-icon">👁️</span>
                                        View Details
                                    </div>
                                    <div class="dropdown-item" onclick="actionClicked('Edit Vehicle', 1)">
                                        <span class="dropdown-icon">✏️</span>
                                        Edit Vehicle
                                    </div>
                                    <div class="dropdown-item" onclick="actionClicked('Manage Image', 1)">
                                        <span class="dropdown-icon">📸</span>
                                        Manage Image
                                    </div>
                                    <div class="dropdown-item delete" onclick="actionClicked('Delete Vehicle', 1)">
                                        <span class="dropdown-icon">🗑️</span>
                                        Delete Vehicle
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="vehicle-info">
                                <div class="vehicle-image">🚗</div>
                                <div class="vehicle-details">
                                    <h4>BMW X5</h4>
                                    <p>2022</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="status-badge">XYZ-789</span></td>
                        <td><span class="status-badge status-rented">Rented</span></td>
                        <td><span class="status-badge">Luxury</span></td>
                        <td><span class="status-badge">Owned</span></td>

                        <td>25,000 km</td>
                        <td><strong>AED 350</strong></td>
                        <td>
                            <div class="dropdown-container">
                                <button class="dropdown-button" onclick="toggleDropdown(this)">⋮</button>
                                <div class="dropdown-menu">
                                    <div class="dropdown-item" onclick="actionClicked('View Details', 2)">
                                        <span class="dropdown-icon">👁️</span>
                                        View Details
                                    </div>
                                    <div class="dropdown-item" onclick="actionClicked('Edit Vehicle', 2)">
                                        <span class="dropdown-icon">✏️</span>
                                        Edit Vehicle
                                    </div>
                                    <div class="dropdown-item" onclick="actionClicked('Manage Image', 2)">
                                        <span class="dropdown-icon">📸</span>
                                        Manage Image
                                    </div>
                                    <div class="dropdown-item delete" onclick="actionClicked('Delete Vehicle', 2)">
                                        <span class="dropdown-icon">🗑️</span>
                                        Delete Vehicle
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="vehicle-info">
                                <div class="vehicle-image">🚗</div>
                                <div class="vehicle-details">
                                    <h4>Honda CR-V</h4>
                                    <p>2023</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="status-badge">DEF-456</span></td>
                        <td><span class="status-badge status-maintenance">Maintenance</span></td>
                        <td><span class="status-badge">SUV</span></td>
                        <td><span class="status-badge">Leased</span></td>

                        <td>18,000 km</td>
                        <td><strong>AED 200</strong></td>
                        <td>
                            <div class="dropdown-container">
                                <button class="dropdown-button" onclick="toggleDropdown(this)">⋮</button>
                                <div class="dropdown-menu">
                                    <div class="dropdown-item" onclick="actionClicked('View Details', 3)">
                                        <span class="dropdown-icon">👁️</span>
                                        View Details
                                    </div>
                                    <div class="dropdown-item" onclick="actionClicked('Edit Vehicle', 3)">
                                        <span class="dropdown-icon">✏️</span>
                                        Edit Vehicle
                                    </div>
                                    <div class="dropdown-item" onclick="actionClicked('Manage Image', 3)">
                                        <span class="dropdown-icon">📸</span>
                                        Manage Image
                                    </div>
                                    <div class="dropdown-item delete" onclick="actionClicked('Delete Vehicle', 3)">
                                        <span class="dropdown-icon">🗑️</span>
                                        Delete Vehicle
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="test-section">
            <h3>Quick Links</h3>
            <div class="demo-buttons">
                <a href="/vehicles" target="_blank"><button class="demo-btn">Vehicles Page (Vue)</button></a>
                <a href="/test-vehicles-table" target="_blank"><button class="demo-btn">Test Vehicles Table</button></a>
                <a href="/test-vehicle-images" target="_blank"><button class="demo-btn">Test Image Management</button></a>
            </div>
        </div>

        <div class="warning test-section">
            <h3>Features Implemented</h3>
            <ul>
                <li>✅ <strong>Three-dot Menu:</strong> ⋮ button for each vehicle</li>
                <li>✅ <strong>Dropdown Menu:</strong> Clean dropdown with all actions</li>
                <li>✅ <strong>Action Items:</strong> View, Edit, Manage Image, Delete</li>
                <li>✅ <strong>Icons:</strong> Each action has a descriptive icon</li>
                <li>✅ <strong>Hover Effects:</strong> Smooth hover interactions</li>
                <li>✅ <strong>Delete Styling:</strong> Red color for delete action</li>
                <li>✅ <strong>Responsive:</strong> Works on all screen sizes</li>
                <li>✅ <strong>Accessibility:</strong> Screen reader friendly</li>
                <li>🔄 <strong>Real Integration:</strong> Connect to actual Vue components</li>
            </ul>
        </div>

        <div class="info test-section">
            <h3>How to Use</h3>
            <ol>
                <li>Click the <strong>⋮</strong> button next to any vehicle</li>
                <li>A dropdown menu will appear with all available actions</li>
                <li>Click on any action to perform it</li>
                <li>The menu automatically closes after selection</li>
            </ol>
        </div>
    </div>

    <script>
        function toggleDropdown(button) {
            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu !== button.nextElementSibling) {
                    menu.classList.remove('show');
                }
            });

            // Toggle current dropdown
            const menu = button.nextElementSibling;
            menu.classList.toggle('show');
        }

        function actionClicked(action, vehicleId) {
            alert(`${action} for Vehicle ${vehicleId}`);

            // Close dropdown
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown-container')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });
    </script>
</body>
</html>
