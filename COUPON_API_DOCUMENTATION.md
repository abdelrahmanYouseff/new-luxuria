# Coupon API Documentation

## Overview
This API provides endpoints for validating coupon codes and retrieving active coupons.

## Base URL
```
http://127.0.0.1:8000/api
```

## Endpoints

### 1. Validate Coupon Code

**Endpoint:** `POST /coupons/validate`

**Description:** Validates a coupon code and returns discount information.

**Request Body:**
```json
{
    "code": "LUX50"
}
```

**Response (Success - 200):**
```json
{
    "success": true,
    "message": "Coupon is valid",
    "data": {
        "id": 2,
        "code": "LUX50",
        "discount_type": "fixed",
        "discount_value": "50.00",
        "expire_at": "2025-08-29 15:39:00",
        "is_active": true
    }
}
```

**Response (Coupon Not Found - 404):**
```json
{
    "success": false,
    "message": "Coupon code not found",
    "data": null
}
```

**Response (Coupon Expired - 400):**
```json
{
    "success": false,
    "message": "Coupon has expired",
    "data": null
}
```

### 2. Get Active Coupons

**Endpoint:** `GET /coupons/active`

**Description:** Retrieves all active (non-expired) coupons.

**Response (Success - 200):**
```json
{
    "success": true,
    "message": "Active coupons retrieved successfully",
    "data": [
        {
            "id": 4,
            "code": "LUX200",
            "discount_type": "percentage",
            "discount_value": "50.00",
            "expire_at": "2025-08-30 15:51:00",
            "is_active": true
        },
        {
            "id": 3,
            "code": "LUX100",
            "discount_type": "fixed",
            "discount_value": "500.00",
            "expire_at": "2025-08-30 15:48:00",
            "is_active": true
        },
        {
            "id": 2,
            "code": "LUX50",
            "discount_type": "fixed",
            "discount_value": "50.00",
            "expire_at": "2025-08-29 15:39:00",
            "is_active": true
        }
    ]
}
```

## Discount Types

### Fixed Amount
- **discount_type:** `"fixed"`
- **discount_value:** Amount in AED (e.g., "50.00" = 50 AED discount)

### Percentage
- **discount_type:** `"percentage"`
- **discount_value:** Percentage value (e.g., "50.00" = 50% discount)

## Usage Examples

### cURL Examples

#### Validate Coupon
```bash
curl -X POST http://127.0.0.1:8000/api/coupons/validate \
  -H "Content-Type: application/json" \
  -d '{"code": "LUX50"}'
```

#### Get Active Coupons
```bash
curl -X GET http://127.0.0.1:8000/api/coupons/active
```

### JavaScript Examples

#### Validate Coupon
```javascript
const validateCoupon = async (code) => {
    try {
        const response = await fetch('http://127.0.0.1:8000/api/coupons/validate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ code })
        });
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error:', error);
    }
};

// Usage
validateCoupon('LUX50').then(result => {
    if (result.success) {
        console.log('Coupon is valid:', result.data);
        console.log('Discount type:', result.data.discount_type);
        console.log('Discount value:', result.data.discount_value);
    } else {
        console.log('Coupon is invalid:', result.message);
    }
});
```

#### Get Active Coupons
```javascript
const getActiveCoupons = async () => {
    try {
        const response = await fetch('http://127.0.0.1:8000/api/coupons/active');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error:', error);
    }
};

// Usage
getActiveCoupons().then(result => {
    if (result.success) {
        console.log('Active coupons:', result.data);
    }
});
```

## Error Handling

The API returns appropriate HTTP status codes:

- **200:** Success
- **400:** Bad Request (e.g., coupon expired)
- **404:** Not Found (e.g., coupon code not found)
- **422:** Validation Error (e.g., missing required fields)

## Notes

- Coupon codes are case-insensitive (automatically converted to uppercase)
- Only non-expired coupons are considered active
- The API validates coupon existence and expiration date
- Fixed amount discounts are in AED currency
- Percentage discounts range from 0% to 100%
