# PointSys API Documentation

## Overview
This documentation describes how to use the PointSys API integration in the Luxuria UAE application, based on the official API documentation.

## Configuration
The PointSys API is configured in the `.env` file:

```env
POINTSYS_API_KEY=lux_M01oRXyMzqM3tPDtN4ELFNQ50lRLY25a
POINTSYS_BASE_URL=https://pointsys.clarastars.com/api/v1
```

## API Endpoints

### Base URL
- **Production**: `https://pointsys.clarastars.com/api/v1`
- **Local**: `http://127.0.0.1:8001/api/v1`

### Authentication
All API requests require authentication using the API key in the header:
```
Authorization: Bearer lux_M01oRXyMzqM3tPDtN4ELFNQ50lRLY25a
```
or
```
X-API-Key: lux_M01oRXyMzqM3tPDtN4ELFNQ50lRLY25a
```

## Available Endpoints

### 1. Customer Management

#### Register Customer
```http
POST /api/pointsys/customers/register
Content-Type: application/json

{
    "name": "أحمد محمد",
    "email": "ahmed@example.com",
    "phone": "0501234567"
}
```

**Expected Response:**
```json
{
    "status": "success",
    "message": "تم تسجيل العميل بنجاح",
    "data": {
        "customer_id": 1,
        "name": "أحمد محمد",
        "email": "ahmed@example.com",
        "phone": "0501234567",
        "points_balance": 0
    }
}
```

#### Get Customer Balance
```http
GET /api/pointsys/customers/{customerId}/balance
```

**Expected Response:**
```json
{
    "status": "success",
    "data": {
        "customer_id": 1,
        "name": "أحمد محمد",
        "points_balance": 150,
        "total_earned": 300,
        "total_redeemed": 150
    }
}
```

### 2. Points Management

#### Add Points to Customer
```http
POST /api/pointsys/customers/points/add
Content-Type: application/json

{
    "customer_id": 1,
    "points": 100,
    "description": "شراء منتج",
    "reference_id": "ORDER_12345"
}
```

**Expected Response:**
```json
{
    "status": "success",
    "message": "تم إضافة النقاط بنجاح",
    "data": {
        "transaction_id": 123,
        "customer_id": 1,
        "points_added": 100,
        "new_balance": 250,
        "description": "شراء منتج",
        "reference_id": "ORDER_12345"
    }
}
```

### 3. Rewards

#### Get Available Rewards
```http
GET /api/pointsys/rewards
```

**Expected Response:**
```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "title": "خصم 10%",
            "description": "خصم 10% على المشتريات",
            "points_required": 100,
            "type": "discount",
            "value": 10,
            "status": "active"
        },
        {
            "id": 2,
            "title": "هدية مجانية",
            "description": "هدية مجانية مع الطلب",
            "points_required": 200,
            "type": "gift",
            "value": null,
            "status": "active"
        }
    ]
}
```

#### Redeem Reward
```http
POST /api/pointsys/rewards/redeem
Content-Type: application/json

{
    "customer_id": 1,
    "reward_id": 1
}
```

**Expected Response:**
```json
{
    "status": "success",
    "message": "تم استبدال المكافأة بنجاح",
    "data": {
        "redemption_id": 456,
        "customer_id": 1,
        "reward_id": 1,
        "reward_title": "خصم 10%",
        "points_used": 100,
        "remaining_balance": 150
    }
}
```

## Usage Examples

### Using the Service Class

```php
use App\Services\PointSysService;

class SomeController extends Controller
{
    private PointSysService $pointSysService;

    public function __construct(PointSysService $pointSysService)
    {
        $this->pointSysService = $pointSysService;
    }

    public function someMethod()
    {
        // Register a new customer
        $customer = $this->pointSysService->registerCustomer([
            'name' => 'أحمد محمد',
            'email' => 'ahmed@example.com',
            'phone' => '0501234567'
        ]);
        
        // Get customer balance
        $balance = $this->pointSysService->getCustomerBalance(1);
        
        // Add points to customer
        $result = $this->pointSysService->addPointsToCustomer(1, 100, 'Car rental bonus', 'ORDER_123');
        
        // Get available rewards
        $rewards = $this->pointSysService->getRewards();
        
        // Redeem a reward
        $redemption = $this->pointSysService->redeemReward(1, 1);
    }
}
```

### Using the Controller

```php
use App\Http\Controllers\PointSysController;

// In your routes
Route::post('/register-customer', [PointSysController::class, 'registerCustomer']);
Route::get('/customer-balance/{customerId}', [PointSysController::class, 'getCustomerBalance']);
Route::post('/add-points', [PointSysController::class, 'addPointsToCustomer']);
Route::get('/rewards', [PointSysController::class, 'getRewards']);
Route::post('/redeem-reward', [PointSysController::class, 'redeemReward']);
```

## Error Handling

The API returns appropriate HTTP status codes:

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized (Invalid API key)
- `404` - Not Found
- `500` - Internal Server Error

Error responses include:
```json
{
    "error": "Error description",
    "message": "Detailed error message"
}
```

## Testing

You can test the API endpoints using tools like:
- Postman
- cURL
- Insomnia

### Example cURL Commands

```bash
# Register a new customer
curl -X POST "http://127.0.0.1:8001/api/pointsys/customers/register" \
  -H "Authorization: Bearer lux_M01oRXyMzqM3tPDtN4ELFNQ50lRLY25a" \
  -H "Content-Type: application/json" \
  -d '{
      "name": "أحمد محمد",
      "email": "ahmed@example.com",
      "phone": "0501234567"
  }'

# Get customer balance
curl -X GET "http://127.0.0.1:8001/api/pointsys/customers/1/balance" \
  -H "Authorization: Bearer lux_M01oRXyMzqM3tPDtN4ELFNQ50lRLY25a"

# Add points to customer
curl -X POST "http://127.0.0.1:8001/api/pointsys/customers/points/add" \
  -H "Authorization: Bearer lux_M01oRXyMzqM3tPDtN4ELFNQ50lRLY25a" \
  -H "Content-Type: application/json" \
  -d '{
      "customer_id": 1,
      "points": 100,
      "description": "Car rental bonus",
      "reference_id": "ORDER_123"
  }'

# Get available rewards
curl -X GET "http://127.0.0.1:8001/api/pointsys/rewards" \
  -H "Authorization: Bearer lux_M01oRXyMzqM3tPDtN4ELFNQ50lRLY25a"

# Redeem a reward
curl -X POST "http://127.0.0.1:8001/api/pointsys/rewards/redeem" \
  -H "Authorization: Bearer lux_M01oRXyMzqM3tPDtN4ELFNQ50lRLY25a" \
  -H "Content-Type: application/json" \
  -d '{
      "customer_id": 1,
      "reward_id": 1
  }'
```

## Security Notes

1. Always use HTTPS in production
2. Keep your API key secure and never expose it in client-side code
3. The API key is validated on every request
4. All requests are logged for debugging purposes

## Troubleshooting

### Common Issues

1. **401 Unauthorized**: Check if the API key is correct and properly formatted
2. **500 Internal Server Error**: Check the Laravel logs for detailed error information
3. **Timeout**: The API has a 30-second timeout, increase if needed

### Logs

API errors are logged in `storage/logs/laravel.log` with detailed information about:
- Endpoint called
- Request data
- Response status
- Error messages 
 