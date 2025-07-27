<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test My Points Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
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
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .result {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-family: monospace;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test My Points Page</h1>

        <div class="info test-section">
            <h3>Instructions</h3>
            <p>This page tests the My Points functionality. Click the buttons below to test different aspects:</p>
        </div>

        <div class="test-section">
            <h3>1. Test My Points Page (Inertia)</h3>
            <p>This should open the My Points page with sidebar and header:</p>
            <button onclick="testMyPointsPage()">Open My Points Page</button>
            <div id="myPointsResult" class="result" style="display: none;"></div>
        </div>

        <div class="test-section">
            <h3>2. Test API Endpoints</h3>
            <p>Test the API endpoints directly:</p>
            <button onclick="testCustomerBalance()">Test Customer Balance API</button>
            <button onclick="testRewards()">Test Rewards API</button>
            <button onclick="testRedeemReward()">Test Redeem Reward API</button>
            <div id="apiResult" class="result" style="display: none;"></div>
        </div>

        <div class="test-section">
            <h3>3. Test User Registration</h3>
            <p>Test if current user is registered in PointSys:</p>
            <button onclick="testUserRegistration()">Check User Registration</button>
            <div id="userResult" class="result" style="display: none;"></div>
        </div>

        <div class="test-section">
            <h3>4. Quick Links</h3>
            <p>Direct links to test pages:</p>
            <a href="/my-points" target="_blank"><button>My Points Page</button></a>
            <a href="/test-pointsys-real" target="_blank"><button>PointSys API Test</button></a>
            <a href="/test-registration" target="_blank"><button>Registration Test</button></a>
        </div>
    </div>

    <script>
        function showResult(elementId, content, isError = false) {
            const element = document.getElementById(elementId);
            element.style.display = 'block';
            element.className = 'result ' + (isError ? 'error' : 'success');
            element.textContent = content;
        }

        function testMyPointsPage() {
            const resultDiv = document.getElementById('myPointsResult');
            resultDiv.style.display = 'block';
            resultDiv.className = 'result info';
            resultDiv.textContent = 'Redirecting to My Points page...';

            // Try to open the page
            window.open('/my-points', '_blank');
        }

        async function testCustomerBalance() {
            try {
                const response = await fetch('/api/pointsys/customers/26/balance');
                const data = await response.json();

                if (response.ok) {
                    showResult('apiResult', '✅ Customer Balance API Working!\n\n' + JSON.stringify(data, null, 2));
                } else {
                    showResult('apiResult', '❌ Customer Balance API Failed:\n\n' + JSON.stringify(data, null, 2), true);
                }
            } catch (error) {
                showResult('apiResult', '❌ Network Error:\n\n' + error.message, true);
            }
        }

        async function testRewards() {
            try {
                const response = await fetch('/api/pointsys/rewards');
                const data = await response.json();

                if (response.ok) {
                    showResult('apiResult', '✅ Rewards API Working!\n\n' + JSON.stringify(data, null, 2));
                } else {
                    showResult('apiResult', '❌ Rewards API Failed:\n\n' + JSON.stringify(data, null, 2), true);
                }
            } catch (error) {
                showResult('apiResult', '❌ Network Error:\n\n' + error.message, true);
            }
        }

        async function testRedeemReward() {
            try {
                const response = await fetch('/api/pointsys/rewards/redeem', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        customer_id: 26,
                        reward_id: 1
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    showResult('apiResult', '✅ Redeem Reward API Working!\n\n' + JSON.stringify(data, null, 2));
                } else {
                    showResult('apiResult', '❌ Redeem Reward API Failed:\n\n' + JSON.stringify(data, null, 2), true);
                }
            } catch (error) {
                showResult('apiResult', '❌ Network Error:\n\n' + error.message, true);
            }
        }

        async function testUserRegistration() {
            try {
                const response = await fetch('/api/pointsys/customers/26/balance');
                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    showResult('userResult', '✅ User is registered in PointSys!\n\nCustomer ID: ' + data.data.customer_id + '\nName: ' + data.data.name + '\nPoints Balance: ' + data.data.points_balance);
                } else {
                    showResult('userResult', '❌ User not found or not registered:\n\n' + JSON.stringify(data, null, 2), true);
                }
            } catch (error) {
                showResult('userResult', '❌ Network Error:\n\n' + error.message, true);
            }
        }
    </script>
</body>
</html>
