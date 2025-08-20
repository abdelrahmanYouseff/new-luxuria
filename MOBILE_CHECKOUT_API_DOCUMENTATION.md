# Mobile Checkout API Documentation

## Overview
This API provides endpoints for creating Stripe checkout sessions for mobile app users.

## Base URL
```
http://127.0.0.1:8000/api
```

## Authentication
All endpoints require authentication using Bearer token.

## Endpoints

### 1. Create Checkout Session

**Endpoint:** `POST /mobile/reservations/checkout`

**Description:** Creates a Stripe checkout session for a specific reservation with custom total amount.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "reservation_id": 123,
    "total_amount": 500.00,
    "success_url": "https://yourapp.com/success",
    "cancel_url": "https://yourapp.com/cancel"
}
```

**Required Fields:**
- `reservation_id`: ID of the existing reservation
- `total_amount`: The amount the user will pay (numeric, minimum 0)

**Optional Fields:**
- `success_url`: Custom success URL (defaults to Luxuria UAE success page)
- `cancel_url`: Custom cancel URL (defaults to Luxuria UAE cancel page)

**Response (Success - 200):**
```json
{
    "success": true,
    "message": "تم إنشاء رابط الدفع بنجاح",
    "data": {
        "checkout_url": "https://checkout.stripe.com/pay/cs_test_...",
        "session_id": "cs_test_...",
        "expires_at": "2025-08-19 12:30:00",
        "reservation": {
            "id": 123,
            "vehicle": "BMW X5",
            "total_amount": 500.00,
            "currency": "AED",
            "dates": {
                "start_date": "2025-08-20",
                "end_date": "2025-08-25",
                "total_days": 5
            }
        }
    }
}
```

**Response (Validation Error - 422):**
```json
{
    "success": false,
    "message": "بيانات غير صحيحة",
    "errors": {
        "reservation_id": ["The reservation id field is required."],
        "total_amount": ["The total amount field is required."]
    }
}
```

**Response (Unauthorized - 401):**
```json
{
    "success": false,
    "message": "يجب تسجيل الدخول أولاً"
}
```

**Response (Reservation Not Found - 404):**
```json
{
    "success": false,
    "message": "الحجز غير موجود أو لا يخصك"
}
```

**Response (Already Paid - 400):**
```json
{
    "success": false,
    "message": "الحجز مؤكد ومدفوع مسبقاً"
}
```

**Response (Cancelled - 400):**
```json
{
    "success": false,
    "message": "الحجز ملغي ولا يمكن الدفع له"
}
```

## Usage Examples

### cURL Example
```bash
curl -X POST http://127.0.0.1:8000/api/mobile/reservations/checkout \
  -H "Authorization: Bearer {your_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "reservation_id": 123,
    "total_amount": 500.00,
    "success_url": "https://yourapp.com/success",
    "cancel_url": "https://yourapp.com/cancel"
  }'
```

### JavaScript Example
```javascript
const createCheckoutSession = async (reservationId, totalAmount) => {
    try {
        const response = await fetch('http://127.0.0.1:8000/api/mobile/reservations/checkout', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                reservation_id: reservationId,
                total_amount: totalAmount,
                success_url: 'https://yourapp.com/success',
                cancel_url: 'https://yourapp.com/cancel'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Redirect to Stripe checkout
            window.location.href = data.data.checkout_url;
        } else {
            console.error('Checkout creation failed:', data.message);
        }
        
        return data;
    } catch (error) {
        console.error('Error:', error);
    }
};

// Usage
createCheckoutSession(123, 500.00);
```

## Stripe Integration Details

### Session Configuration:
- **Payment Methods:** Card payments only
- **Currency:** AED (UAE Dirham)
- **Mode:** Payment (one-time payment)
- **Expiration:** 30 minutes
- **Billing Address:** Auto-collection
- **Shipping Address:** UAE only

### Metadata Included:
- `reservation_id`: Local booking ID
- `user_id`: User ID
- `vehicle_id`: Vehicle ID
- `external_reservation_id`: External system ID
- `external_reservation_uid`: External system UID
- `start_date`: Reservation start date
- `end_date`: Reservation end date
- `total_amount`: Payment amount (from request)
- `emirate`: Emirate location

### Line Items:
- **Product Name:** Vehicle make + model + year
- **Description:** Rental period and duration
- **Images:** Vehicle image URL
- **Unit Amount:** Total amount in fils (cents)
- **Quantity:** 1

## Process Flow

1. **Validation:** Check reservation exists and belongs to user
2. **Status Check:** Ensure reservation is not already paid or cancelled
3. **Stripe Session:** Create checkout session with custom amount
4. **Response:** Return checkout URL and session details

## Error Handling

### Common Errors:
- **401:** User not authenticated
- **404:** Reservation not found or doesn't belong to user
- **400:** Reservation already paid or cancelled
- **422:** Validation errors (missing required fields)
- **500:** Stripe API errors or server errors

### Logging:
All operations are logged with detailed information:
- Checkout session creation attempts
- Stripe API responses
- Error details and stack traces

## Notes

- The `total_amount` parameter allows flexible pricing (e.g., with discounts, coupons)
- Session expires after 30 minutes
- Success/cancel URLs can be customized per request
- All amounts are in AED currency
- Stripe handles the actual payment processing
