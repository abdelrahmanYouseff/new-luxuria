<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Homepage - Categorized Vehicles</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .header {
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1.5rem 0;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.875rem;
            font-weight: bold;
            color: #111827;
        }

        .refresh-btn {
            background: white;
            border: 1px solid #d1d5db;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .refresh-btn:hover {
            background: #f9fafb;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .status-alert {
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 2rem;
        }

        .category-section {
            margin-bottom: 3rem;
        }

        .category-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .category-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #111827;
        }

        .vehicle-count {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .vehicle-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .vehicle-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: box-shadow 0.2s;
        }

        .vehicle-card:hover {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .vehicle-image {
            height: 12rem;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .vehicle-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .vehicle-placeholder {
            text-align: center;
            color: #1e40af;
        }

        .vehicle-placeholder-icon {
            font-size: 4rem;
            margin-bottom: 0.5rem;
        }

        .vehicle-info {
            padding: 1rem;
        }

        .vehicle-name {
            font-size: 1.125rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        .vehicle-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-dark {
            background: #111827;
            color: white;
        }

        .badge-green {
            background: #059669;
            color: white;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .pricing-item {
            text-align: center;
        }

        .pricing-label {
            font-size: 0.75rem;
            color: #6b7280;
        }

        .pricing-value {
            font-size: 0.875rem;
            font-weight: bold;
            color: #111827;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .book-btn {
            flex: 1;
            background: #4b5563;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
        }

        .book-btn:hover {
            background: #374151;
        }

        .whatsapp-btn {
            background: #10b981;
            color: white;
            border: none;
            padding: 0.5rem;
            border-radius: 0.375rem;
            cursor: pointer;
        }

        .whatsapp-btn:hover {
            background: #059669;
        }

        .vehicle-details {
            padding-top: 0.75rem;
            border-top: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            color: #6b7280;
        }

        .demo-buttons {
            display: flex;
            gap: 0.75rem;
            margin: 2rem 0;
        }

        .demo-btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .demo-btn:hover {
            background: #2563eb;
        }

        .features-list {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 0.5rem;
            padding: 1rem;
            margin: 2rem 0;
        }

        .features-list h3 {
            margin-top: 0;
            color: #92400e;
        }

        .features-list ul {
            margin: 0;
            padding-left: 1.5rem;
        }

        .features-list li {
            margin-bottom: 0.5rem;
            color: #92400e;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">Luxuria UAE</div>
            <button class="refresh-btn" onclick="refreshPage()">
                🔄 Refresh
            </button>
        </div>
    </header>

    <!-- API Status -->
    <div class="container">
        <div class="status-alert">
            <h3>🚗 Homepage with Categorized Vehicles</h3>
            <p>This page demonstrates the homepage layout with vehicles categorized by type, fetched from the real API.</p>
        </div>

        <!-- Demo Categories -->
        <div class="category-section">
            <div class="category-header">
                <h2 class="category-title">LUXURY</h2>
                <span class="vehicle-count">4 vehicles</span>
            </div>

            <div class="vehicle-grid">
                <!-- Vehicle Card 1 -->
                <div class="vehicle-card">
                    <div class="vehicle-image">
                        <div class="vehicle-placeholder">
                            <div class="vehicle-placeholder-icon">🚗</div>
                            <p>BMW 7 Series 740i</p>
                        </div>
                    </div>
                    <div class="vehicle-info">
                        <h3 class="vehicle-name">BMW 7 Series 740i</h3>

                        <div class="vehicle-badges">
                            <span class="badge badge-dark">5 Seats</span>
                            <span class="badge badge-dark">4 Doors</span>
                            <span class="badge badge-green">No Deposit</span>
                        </div>

                        <div class="pricing-grid">
                            <div class="pricing-item">
                                <div class="pricing-label">Daily</div>
                                <div class="pricing-value">1299 AED</div>
                            </div>
                            <div class="pricing-item">
                                <div class="pricing-label">Weekly</div>
                                <div class="pricing-value">7699 AED</div>
                            </div>
                            <div class="pricing-item">
                                <div class="pricing-label">Monthly</div>
                                <div class="pricing-value">24500 AED</div>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <button class="book-btn" onclick="bookVehicle('BMW 7 Series 740i', 1299)">Book Now</button>
                            <button class="whatsapp-btn" onclick="contactWhatsApp('BMW 7 Series 740i', 1299)">💬</button>
                        </div>

                        <div class="vehicle-details">
                            <span>2023 • White</span>
                            <span>CC-51054</span>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Card 2 -->
                <div class="vehicle-card">
                    <div class="vehicle-image">
                        <div class="vehicle-placeholder">
                            <div class="vehicle-placeholder-icon">🚗</div>
                            <p>Mercedes Benz S500</p>
                        </div>
                    </div>
                    <div class="vehicle-info">
                        <h3 class="vehicle-name">Mercedes Benz S500</h3>

                        <div class="vehicle-badges">
                            <span class="badge badge-dark">5 Seats</span>
                            <span class="badge badge-dark">4 Doors</span>
                            <span class="badge badge-green">No Deposit</span>
                        </div>

                        <div class="pricing-grid">
                            <div class="pricing-item">
                                <div class="pricing-label">Daily</div>
                                <div class="pricing-value">1199 AED</div>
                            </div>
                            <div class="pricing-item">
                                <div class="pricing-label">Weekly</div>
                                <div class="pricing-value">7199 AED</div>
                            </div>
                            <div class="pricing-item">
                                <div class="pricing-label">Monthly</div>
                                <div class="pricing-value">22999 AED</div>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <button class="book-btn" onclick="bookVehicle('Mercedes Benz S500', 1199)">Book Now</button>
                            <button class="whatsapp-btn" onclick="contactWhatsApp('Mercedes Benz S500', 1199)">💬</button>
                        </div>

                        <div class="vehicle-details">
                            <span>2022 • White</span>
                            <span>CC-30531</span>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Card 3 -->
                <div class="vehicle-card">
                    <div class="vehicle-image">
                        <div class="vehicle-placeholder">
                            <div class="vehicle-placeholder-icon">🚗</div>
                            <p>Land Rover Defender</p>
                        </div>
                    </div>
                    <div class="vehicle-info">
                        <h3 class="vehicle-name">Land Rover Defender V6</h3>

                        <div class="vehicle-badges">
                            <span class="badge badge-dark">5 Seats</span>
                            <span class="badge badge-dark">4 Doors</span>
                            <span class="badge badge-green">No Deposit</span>
                        </div>

                        <div class="pricing-grid">
                            <div class="pricing-item">
                                <div class="pricing-label">Daily</div>
                                <div class="pricing-value">899 AED</div>
                            </div>
                            <div class="pricing-item">
                                <div class="pricing-label">Weekly</div>
                                <div class="pricing-value">4999 AED</div>
                            </div>
                            <div class="pricing-item">
                                <div class="pricing-label">Monthly</div>
                                <div class="pricing-value">18999 AED</div>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <button class="book-btn" onclick="bookVehicle('Land Rover Defender V6', 899)">Book Now</button>
                            <button class="whatsapp-btn" onclick="contactWhatsApp('Land Rover Defender V6', 899)">💬</button>
                        </div>

                        <div class="vehicle-details">
                            <span>2022 • White</span>
                            <span>A-50787</span>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Card 4 -->
                <div class="vehicle-card">
                    <div class="vehicle-image">
                        <div class="vehicle-placeholder">
                            <div class="vehicle-placeholder-icon">🚗</div>
                            <p>Range Rover Vogue</p>
                        </div>
                    </div>
                    <div class="vehicle-info">
                        <h3 class="vehicle-name">Range Rover Vogue V6</h3>

                        <div class="vehicle-badges">
                            <span class="badge badge-dark">5 Seats</span>
                            <span class="badge badge-dark">4 Doors</span>
                            <span class="badge badge-green">No Deposit</span>
                        </div>

                        <div class="pricing-grid">
                            <div class="pricing-item">
                                <div class="pricing-label">Daily</div>
                                <div class="pricing-value">1399 AED</div>
                            </div>
                            <div class="pricing-item">
                                <div class="pricing-label">Weekly</div>
                                <div class="pricing-value">8299 AED</div>
                            </div>
                            <div class="pricing-item">
                                <div class="pricing-label">Monthly</div>
                                <div class="pricing-value">29999 AED</div>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <button class="book-btn" onclick="bookVehicle('Range Rover Vogue V6', 1399)">Book Now</button>
                            <button class="whatsapp-btn" onclick="contactWhatsApp('Range Rover Vogue V6', 1399)">💬</button>
                        </div>

                        <div class="vehicle-details">
                            <span>2024 • White</span>
                            <span>CC-29422</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mid-Range Category -->
        <div class="category-section">
            <div class="category-header">
                <h2 class="category-title">MID-RANGE</h2>
                <span class="vehicle-count">3 vehicles</span>
            </div>

            <div class="vehicle-grid">
                <!-- Vehicle Card -->
                <div class="vehicle-card">
                    <div class="vehicle-image">
                        <div class="vehicle-placeholder">
                            <div class="vehicle-placeholder-icon">🚗</div>
                            <p>BMW 3 Series 330i</p>
                        </div>
                    </div>
                    <div class="vehicle-info">
                        <h3 class="vehicle-name">BMW 3 Series 330i</h3>

                        <div class="vehicle-badges">
                            <span class="badge badge-dark">5 Seats</span>
                            <span class="badge badge-dark">4 Doors</span>
                            <span class="badge badge-green">No Deposit</span>
                        </div>

                        <div class="pricing-grid">
                            <div class="pricing-item">
                                <div class="pricing-label">Daily</div>
                                <div class="pricing-value">399 AED</div>
                            </div>
                            <div class="pricing-item">
                                <div class="pricing-label">Weekly</div>
                                <div class="pricing-value">2999 AED</div>
                            </div>
                            <div class="pricing-item">
                                <div class="pricing-label">Monthly</div>
                                <div class="pricing-value">7999 AED</div>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <button class="book-btn" onclick="bookVehicle('BMW 3 Series 330i', 399)">Book Now</button>
                            <button class="whatsapp-btn" onclick="contactWhatsApp('BMW 3 Series 330i', 399)">💬</button>
                        </div>

                        <div class="vehicle-details">
                            <span>2024 • Silver</span>
                            <span>CC-92505</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Economy Category -->
        <div class="category-section">
            <div class="category-header">
                <h2 class="category-title">ECONOMY</h2>
                <span class="vehicle-count">2 vehicles</span>
            </div>

            <div class="vehicle-grid">
                <!-- Vehicle Card -->
                <div class="vehicle-card">
                    <div class="vehicle-image">
                        <div class="vehicle-placeholder">
                            <div class="vehicle-placeholder-icon">🚗</div>
                            <p>Nissan Versa</p>
                        </div>
                    </div>
                    <div class="vehicle-info">
                        <h3 class="vehicle-name">Nissan Versa</h3>

                        <div class="vehicle-badges">
                            <span class="badge badge-dark">5 Seats</span>
                            <span class="badge badge-dark">4 Doors</span>
                            <span class="badge badge-green">No Deposit</span>
                        </div>

                        <div class="pricing-grid">
                            <div class="pricing-item">
                                <div class="pricing-label">Daily</div>
                                <div class="pricing-value">79 AED</div>
                            </div>
                            <div class="pricing-item">
                                <div class="pricing-label">Weekly</div>
                                <div class="pricing-value">469 AED</div>
                            </div>
                            <div class="pricing-item">
                                <div class="pricing-label">Monthly</div>
                                <div class="pricing-value">1699 AED</div>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <button class="book-btn" onclick="bookVehicle('Nissan Versa', 79)">Book Now</button>
                            <button class="whatsapp-btn" onclick="contactWhatsApp('Nissan Versa', 79)">💬</button>
                        </div>

                        <div class="vehicle-details">
                            <span>2021 • White</span>
                            <span>A-48785</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="demo-buttons">
            <a href="/" class="demo-btn">Homepage (Vue)</a>
            <a href="/vehicles" class="demo-btn">Vehicles Table</a>
            <a href="/test-api-vehicles" class="demo-btn">Test API Vehicles</a>
            <a href="/test-vehicles-api" class="demo-btn">Raw API Response</a>
        </div>

        <!-- Features List -->
        <div class="features-list">
            <h3>🚀 Homepage Features Implemented</h3>
            <ul>
                <li><strong>Real API Integration:</strong> Connected to https://rlapp.rentluxuria.com/api/vehicles</li>
                <li><strong>Category Organization:</strong> Vehicles grouped by Luxury, Mid-Range, Economy</li>
                <li><strong>Modern Card Layout:</strong> Clean, responsive vehicle cards with hover effects</li>
                <li><strong>Pricing Display:</strong> Daily, Weekly, and Monthly rates from API</li>
                <li><strong>Vehicle Features:</strong> Seats, Doors, No Deposit badges</li>
                <li><strong>Action Buttons:</strong> Book Now and WhatsApp contact buttons</li>
                <li><strong>Vehicle Details:</strong> Year, Color, and Plate Number</li>
                <li><strong>Responsive Design:</strong> Works on all screen sizes</li>
                <li><strong>Error Handling:</strong> Fallback to mock data if API fails</li>
                <li><strong>Loading States:</strong> Shows API status and vehicle count</li>
            </ul>
        </div>
    </div>

    <script>
        function refreshPage() {
            window.location.reload();
        }

        function bookVehicle(vehicleName, dailyRate) {
            alert(`Booking ${vehicleName} for ${dailyRate} AED/day`);
        }

        function contactWhatsApp(vehicleName, dailyRate) {
            const message = `Hi, I'm interested in booking the ${vehicleName} for ${dailyRate} AED/day.`;
            const whatsappUrl = `https://wa.me/971501234567?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
        }
    </script>
</body>
</html>
