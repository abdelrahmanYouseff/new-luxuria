<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test PointSys Real API</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
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
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            white-space: pre-wrap;
            font-family: monospace;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        .api-info {
            background-color: #e2e3e5;
            border: 1px solid #d6d8db;
            color: #383d41;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test PointSys Real API</h1>

        <div class="api-info">
            <strong>API Configuration:</strong><br>
            Base URL: {{ config('services.pointsys.base_url') }}<br>
            API Key: {{ substr(config('services.pointsys.api_key'), 0, 10) }}...<br>
            Status: Connected to Real PointSys API
        </div>

        <div class="info">
            <strong>Instructions:</strong><br>
            1. Fill in the form below to test customer registration<br>
            2. Use the buttons to test different API endpoints<br>
            3. Check the response in the result area below
        </div>

        <form id="registrationForm">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="Test User Real" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="testreal@example.com" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="0501234567" required>
            </div>

            <button type="submit">Register Customer</button>
        </form>

        <div style="margin-top: 30px;">
            <h3>Test Other Endpoints:</h3>
            <button onclick="testCustomerBalance()">Test Customer Balance</button>
            <button onclick="testRewards()">Test Rewards</button>
            <button onclick="testRedeemReward()">Test Redeem Reward</button>
        </div>

        <div id="result"></div>
    </div>

    <script>
        document.getElementById('registrationForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const resultDiv = document.getElementById('result');

            resultDiv.innerHTML = '<div class="info">Registering customer...</div>';

            try {
                const response = await fetch('/api/pointsys/customers/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        name: formData.get('name'),
                        email: formData.get('email'),
                        phone: formData.get('phone')
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    resultDiv.innerHTML = '<div class="success">✅ Customer registered successfully!\n\n' + JSON.stringify(data, null, 2) + '</div>';
                } else {
                    resultDiv.innerHTML = '<div class="error">❌ Registration failed:\n\n' + JSON.stringify(data, null, 2) + '</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<div class="error">❌ Error: ' + error.message + '</div>';
            }
        });

        async function testCustomerBalance() {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = '<div class="info">Testing customer balance...</div>';

            try {
                const response = await fetch('/api/pointsys/customers/26/balance');
                const data = await response.json();

                if (response.ok) {
                    resultDiv.innerHTML = '<div class="success">✅ Customer balance retrieved!\n\n' + JSON.stringify(data, null, 2) + '</div>';
                } else {
                    resultDiv.innerHTML = '<div class="error">❌ Failed to get balance:\n\n' + JSON.stringify(data, null, 2) + '</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<div class="error">❌ Error: ' + error.message + '</div>';
            }
        }

        async function testRewards() {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = '<div class="info">Testing rewards...</div>';

            try {
                const response = await fetch('/api/pointsys/rewards');
                const data = await response.json();

                if (response.ok) {
                    resultDiv.innerHTML = '<div class="success">✅ Rewards retrieved!\n\n' + JSON.stringify(data, null, 2) + '</div>';
                } else {
                    resultDiv.innerHTML = '<div class="error">❌ Failed to get rewards:\n\n' + JSON.stringify(data, null, 2) + '</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<div class="error">❌ Error: ' + error.message + '</div>';
            }
        }

        async function testRedeemReward() {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = '<div class="info">Testing reward redemption...</div>';

            try {
                const response = await fetch('/api/pointsys/rewards/redeem', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        customer_id: 26,
                        reward_id: 1
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    resultDiv.innerHTML = '<div class="success">✅ Reward redeemed successfully!\n\n' + JSON.stringify(data, null, 2) + '</div>';
                } else {
                    resultDiv.innerHTML = '<div class="error">❌ Failed to redeem reward:\n\n' + JSON.stringify(data, null, 2) + '</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<div class="error">❌ Error: ' + error.message + '</div>';
            }
        }
    </script>
</body>
</html>
