<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Customer Tier Display</title>
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
        .tier-card {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 1px solid #f59e0b;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .tier-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            margin: 5px;
        }
        .tier-bronze { background: #fef3c7; color: #92400e; }
        .tier-silver { background: #f3f4f6; color: #374151; }
        .tier-gold { background: #fef3c7; color: #92400e; }
        .tier-platinum { background: #dbeafe; color: #1e40af; }
        .tier-diamond { background: #f3e8ff; color: #7c3aed; }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 12px;
            color: #6b7280;
        }

        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        .success { background-color: #f0fdf4; border-color: #bbf7d0; }
        .error { background-color: #fef2f2; border-color: #fecaca; }

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
    </style>
</head>
<body>
    <div class="container">
        <h1>🏆 Test Customer Tier Display</h1>

        <div class="test-section">
            <h3>1. Test Customer Balance API (with Tier)</h3>
            <button onclick="testCustomerBalance()">Load Customer Data</button>
            <div id="apiResult" style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 5px; display: none;"></div>
        </div>

        <div class="test-section">
            <h3>2. Display Customer Tier</h3>
            <div id="tierDisplay"></div>
        </div>

        <div class="test-section">
            <h3>3. Test Different Tiers</h3>
            <button onclick="testTier('bronze')">Test Bronze Tier</button>
            <button onclick="testTier('silver')">Test Silver Tier</button>
            <button onclick="testTier('gold')">Test Gold Tier</button>
            <button onclick="testTier('platinum')">Test Platinum Tier</button>
            <button onclick="testTier('diamond')">Test Diamond Tier</button>
        </div>

        <div class="test-section">
            <h3>4. Quick Links</h3>
            <a href="/my-points" target="_blank"><button>My Points Page</button></a>
            <a href="/test-my-points" target="_blank"><button>Test My Points</button></a>
        </div>
    </div>

    <script>
        let customerData = null;

        function getTierInfo(tier) {
            const tierInfo = {
                bronze: { name: 'برونزي', color: 'tier-bronze', icon: '🥉' },
                silver: { name: 'فضي', color: 'tier-silver', icon: '🥈' },
                gold: { name: 'ذهبي', color: 'tier-gold', icon: '🥇' },
                platinum: { name: 'بلاتيني', color: 'tier-platinum', icon: '⭐' },
                diamond: { name: 'ماسي', color: 'tier-diamond', icon: '💎' }
            };
            return tierInfo[tier] || tierInfo.bronze;
        }

        async function testCustomerBalance() {
            const resultDiv = document.getElementById('apiResult');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = 'Loading customer data...';

            try {
                const response = await fetch('/api/pointsys/customers/26/balance');
                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    customerData = data.data;
                    resultDiv.innerHTML = `<div class="success">✅ Customer data loaded successfully!</div>`;
                    displayTier(customerData);
                } else {
                    resultDiv.innerHTML = `<div class="error">❌ Failed to load customer data:<br>${JSON.stringify(data, null, 2)}</div>`;
                }
            } catch (error) {
                resultDiv.innerHTML = `<div class="error">❌ Network Error:<br>${error.message}</div>`;
            }
        }

        function testTier(tier) {
            const mockData = {
                customer_id: 26,
                name: 'Test User',
                points_balance: 1000,
                tier: tier,
                total_earned: 1500,
                total_redeemed: 500
            };
            displayTier(mockData);
        }

        function displayTier(data) {
            const displayDiv = document.getElementById('tierDisplay');
            const tierInfo = getTierInfo(data.tier);

            const html = `
                <div class="tier-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <h2 style="margin: 0; color: #92400e;">Customer Information</h2>
                            <p style="margin: 5px 0; color: #6b7280;">ID: ${data.customer_id} | Name: ${data.name}</p>
                        </div>
                        <span class="tier-badge ${tierInfo.color}">
                            ${tierInfo.icon} ${tierInfo.name}
                        </span>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-value" style="color: #3b82f6;">${data.points_balance}</div>
                            <div class="stat-label">Current Points</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" style="color: #10b981;">${data.total_earned}</div>
                            <div class="stat-label">Total Earned</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" style="color: #8b5cf6;">${data.total_redeemed}</div>
                            <div class="stat-label">Total Redeemed</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" style="color: #f59e0b;">${tierInfo.name}</div>
                            <div class="stat-label">Current Tier</div>
                        </div>
                    </div>

                    <div style="background: white; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb; margin-top: 15px;">
                        <h4 style="margin: 0 0 10px 0; color: #92400e;">Tier Benefits:</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="color: #10b981;">✓</span>
                                <span style="font-size: 14px;">Access to all rewards</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="color: #10b981;">✓</span>
                                <span style="font-size: 14px;">Priority customer support</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="color: #10b981;">✓</span>
                                <span style="font-size: 14px;">Special promotions</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="color: #10b981;">✓</span>
                                <span style="font-size: 14px;">Exclusive offers</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            displayDiv.innerHTML = html;
        }

        // Load customer data on page load
        window.onload = function() {
            testCustomerBalance();
        };
    </script>
</body>
</html>
