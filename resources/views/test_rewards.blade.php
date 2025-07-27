<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Rewards Display</title>
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
        .reward-card {
            background: #faf5ff;
            border: 1px solid #e9d5ff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
        }
        .reward-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
        }
        .reward-description {
            color: #6b7280;
            margin-bottom: 15px;
        }
        .reward-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .points-badge {
            background: #f3e8ff;
            color: #7c3aed;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .status-badge {
            background: #dcfce7;
            color: #166534;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .redeem-button {
            background: #7c3aed;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        .redeem-button:hover {
            background: #6d28d9;
        }
        .redeem-button:disabled {
            background: #d1d5db;
            cursor: not-allowed;
        }
        .api-info {
            background: #e0f2fe;
            border: 1px solid #0284c7;
            color: #0c4a6e;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        .success {
            background-color: #f0fdf4;
            border-color: #bbf7d0;
        }
        .error {
            background-color: #fef2f2;
            border-color: #fecaca;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎁 Test Rewards Display</h1>

        <div class="api-info">
            <strong>PointSys API Configuration:</strong><br>
            Base URL: {{ config('services.pointsys.base_url') }}<br>
            API Key: {{ substr(config('services.pointsys.api_key'), 0, 10) }}...<br>
            Status: Connected to Real PointSys API
        </div>

        <div class="test-section">
            <h3>1. Test Rewards API</h3>
            <button onclick="testRewardsAPI()" class="redeem-button">Load Rewards from API</button>
            <div id="apiResult" style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 5px; display: none;"></div>
        </div>

        <div class="test-section">
            <h3>2. Display Rewards (Mock Data)</h3>
            <p>This shows how rewards will appear in the My Points page:</p>
            <div id="rewardsDisplay"></div>
        </div>

        <div class="test-section">
            <h3>3. Test Different Scenarios</h3>
            <button onclick="testWithPoints(0)" class="redeem-button">Test with 0 points</button>
            <button onclick="testWithPoints(300)" class="redeem-button">Test with 300 points</button>
            <button onclick="testWithPoints(600)" class="redeem-button">Test with 600 points</button>
            <button onclick="testWithPoints(1200)" class="redeem-button">Test with 1200 points</button>
        </div>

        <div class="test-section">
            <h3>4. Quick Links</h3>
            <a href="/my-points" target="_blank"><button class="redeem-button">My Points Page</button></a>
            <a href="/test-my-points" target="_blank"><button class="redeem-button">Test My Points</button></a>
        </div>
    </div>

    <script>
        let currentPoints = 0;
        let rewardsData = [];

        async function testRewardsAPI() {
            const resultDiv = document.getElementById('apiResult');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = 'Loading rewards from API...';

            try {
                const response = await fetch('/api/pointsys/rewards');
                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    rewardsData = data.data;
                    resultDiv.innerHTML = `<div class="success">✅ Rewards loaded successfully!<br>Found ${rewardsData.length} rewards</div>`;
                    displayRewards(rewardsData, currentPoints);
                } else {
                    resultDiv.innerHTML = `<div class="error">❌ Failed to load rewards:<br>${JSON.stringify(data, null, 2)}</div>`;
                }
            } catch (error) {
                resultDiv.innerHTML = `<div class="error">❌ Network Error:<br>${error.message}</div>`;
            }
        }

        function testWithPoints(points) {
            currentPoints = points;
            if (rewardsData.length > 0) {
                displayRewards(rewardsData, points);
            } else {
                // Use mock data if API data not loaded
                const mockRewards = [
                    {
                        id: 1,
                        title: "خصم 10% على الإيجار",
                        description: "خصم 10% على إيجار أي سيارة لمدة يوم واحد",
                        points_required: 500,
                        status: "active"
                    },
                    {
                        id: 2,
                        title: "إيجار مجاني ليوم واحد",
                        description: "إيجار مجاني لسيارة اقتصادية ليوم واحد",
                        points_required: 1000,
                        status: "active"
                    },
                    {
                        id: 3,
                        title: "ترقية مجانية",
                        description: "ترقية مجانية من سيارة اقتصادية إلى فاخرة",
                        points_required: 750,
                        status: "active"
                    }
                ];
                displayRewards(mockRewards, points);
            }
        }

        function displayRewards(rewards, points) {
            const displayDiv = document.getElementById('rewardsDisplay');

            if (rewards.length === 0) {
                displayDiv.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #6b7280;">
                        <div style="font-size: 48px; margin-bottom: 16px;">🎁</div>
                        <h3>No rewards available</h3>
                        <p>Check back later for new rewards!</p>
                    </div>
                `;
                return;
            }

            let html = `<div style="margin-bottom: 20px; padding: 15px; background: #f0f9ff; border-radius: 8px;">
                <strong>Current Points Balance: ${points}</strong>
            </div>`;

            rewards.forEach(reward => {
                const canRedeem = points >= reward.points_required && reward.status === 'active';
                const pointsNeeded = reward.points_required - points;

                html += `
                    <div class="reward-card">
                        <div class="reward-title">${reward.title}</div>
                        <div class="reward-description">${reward.description}</div>
                        <div class="reward-meta">
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <span class="points-badge">${reward.points_required} pts</span>
                                <span class="status-badge">${reward.status}</span>
                            </div>
                            <div style="text-align: right;">
                                ${canRedeem ?
                                    `<button class="redeem-button" onclick="redeemReward(${reward.id})">Redeem</button>` :
                                    points < reward.points_required ?
                                        `<span style="color: #6b7280; font-size: 12px;">Need ${pointsNeeded} more pts</span>` :
                                        `<span style="color: #6b7280; font-size: 12px;">Not available</span>`
                                }
                            </div>
                        </div>
                    </div>
                `;
            });

            displayDiv.innerHTML = html;
        }

        function redeemReward(rewardId) {
            alert(`Redeeming reward ${rewardId}...\nThis would call the redeem API in the real application.`);
        }

        // Load rewards on page load
        window.onload = function() {
            testRewardsAPI();
        };
    </script>
</body>
</html>
