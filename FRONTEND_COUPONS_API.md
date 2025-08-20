# Frontend Coupons API Documentation

## Overview
This API provides endpoints for retrieving coupons from the PointSys system for the frontend website.

## Base URL
```
http://127.0.0.1:8000/api
```

## Authentication
**Not Required** - This is a public API for the frontend website.

## Endpoints

### 1. Get Available Coupons

**Endpoint:** `GET /api/coupons`

**Description:** Retrieve all available coupons from PointSys system for frontend display.

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Response (Success - 200):**
```json
{
    "success": true,
    "message": "تم جلب الكوبونات بنجاح",
    "data": {
        "coupons": [
            {
                "id": "1",
                "code": "SAVE20",
                "title": "خصم 20%",
                "description": "خصم 20% على جميع الخدمات",
                "discount_type": "percentage",
                "discount_value": 20,
                "min_order_value": 100,
                "max_discount": null,
                "usage_limit": 50,
                "used_count": 15,
                "is_active": true,
                "start_date": "2025-01-01",
                "end_date": "2025-12-31",
                "applicable_categories": ["electronics", "books"],
                "applicable_products": [],
                "formatted_discount": "20%",
                "is_expired": false,
                "days_remaining": 180
            }
        ],
        "total_count": 1,
        "active_count": 1
    }
}
```

**Response (No Coupons - 200):**
```json
{
    "success": false,
    "message": "لا توجد كوبونات متاحة حالياً",
    "data": {
        "coupons": [],
        "total_count": 0,
        "active_count": 0
    }
}
```

## Implementation Details

### Method: `getCouponsApi()`

**Location:** `app/Http/Controllers/CouponController.php`

**Features:**
1. **PointSys Integration** - Connects to PointSys API via PointSysService
2. **Data Formatting** - Converts PointSys data to frontend-friendly format
3. **Fallback System** - Returns sample data if PointSys is unavailable
4. **Filtering** - Only shows active and non-expired coupons
5. **Error Handling** - Comprehensive error handling with logging

### Data Structure:

#### Coupon Object:
```json
{
    "id": "string",
    "code": "string",
    "title": "string",
    "description": "string",
    "discount_type": "fixed|percentage",
    "discount_value": "number",
    "min_order_value": "number",
    "max_discount": "number|null",
    "usage_limit": "number|null",
    "used_count": "number",
    "is_active": "boolean",
    "start_date": "date|null",
    "end_date": "date|null",
    "applicable_categories": "array",
    "applicable_products": "array",
    "formatted_discount": "string",
    "is_expired": "boolean",
    "days_remaining": "number|null"
}
```

## Frontend Integration

### JavaScript Example:
```javascript
// Fetch coupons for frontend display
async function loadCoupons() {
    try {
        const response = await fetch('/api/coupons', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            displayCoupons(data.data.coupons);
            updateStats(data.data.total_count, data.data.active_count);
        } else {
            console.error('Failed to load coupons:', data.message);
        }
    } catch (error) {
        console.error('Error loading coupons:', error);
        // Show fallback message or retry
    }
}

// Display coupons in HTML
function displayCoupons(coupons) {
    const container = document.querySelector('.coupons-container');
    container.innerHTML = '';

    coupons.forEach(coupon => {
        const couponElement = createCouponElement(coupon);
        container.appendChild(couponElement);
    });
}

function createCouponElement(coupon) {
    const div = document.createElement('div');
    div.className = 'coupon-card';
    div.innerHTML = `
        <div class="coupon-header">
            <h3>${coupon.title}</h3>
            <span class="coupon-code">${coupon.code}</span>
        </div>
        <div class="coupon-body">
            <p class="description">${coupon.description}</p>
            <div class="discount-info">
                <span class="discount-value">${coupon.formatted_discount}</span>
                <span class="discount-type">${coupon.discount_type === 'percentage' ? 'خصم نسبي' : 'خصم ثابت'}</span>
            </div>
            ${coupon.min_order_value > 0 ? `<p class="min-order">الحد الأدنى: ${coupon.min_order_value} درهم</p>` : ''}
            ${coupon.days_remaining !== null ? `<p class="expiry">ينتهي خلال: ${coupon.days_remaining} يوم</p>` : ''}
            <button class="purchase-btn" data-coupon-id="${coupon.id}">
                شراء الكوبون - ${coupon.points_required || 0} نقطة
            </button>
        </div>
    `;
    return div;
}
```

### Vue.js Example:
```javascript
<template>
    <div class="coupons-section">
        <div v-if="loading" class="loading">
            جاري تحميل الكوبونات...
        </div>
        <div v-else-if="error" class="error">
            {{ error }}
        </div>
        <div v-else>
            <div class="coupons-grid">
                <div v-for="coupon in coupons" :key="coupon.id" class="coupon-card">
                    <div class="coupon-header">
                        <h3>{{ coupon.title }}</h3>
                        <span class="coupon-code">{{ coupon.code }}</span>
                    </div>
                    <div class="coupon-body">
                        <p class="description">{{ coupon.description }}</p>
                        <div class="discount-info">
                            <span class="discount-value">{{ coupon.formatted_discount }}</span>
                            <span class="discount-type">
                                {{ coupon.discount_type === 'percentage' ? 'خصم نسبي' : 'خصم ثابت' }}
                            </span>
                        </div>
                        <p v-if="coupon.min_order_value > 0" class="min-order">
                            الحد الأدنى: {{ coupon.min_order_value }} درهم
                        </p>
                        <p v-if="coupon.days_remaining !== null" class="expiry">
                            ينتهي خلال: {{ coupon.days_remaining }} يوم
                        </p>
                        <button @click="purchaseCoupon(coupon)" class="purchase-btn">
                            شراء الكوبون - {{ coupon.points_required || 0 }} نقطة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            coupons: [],
            loading: true,
            error: null
        }
    },

    mounted() {
        this.loadCoupons();
    },

    methods: {
        async loadCoupons() {
            try {
                const response = await fetch('/api/coupons');
                const data = await response.json();

                if (data.success) {
                    this.coupons = data.data.coupons;
                } else {
                    this.error = data.message;
                }
            } catch (error) {
                this.error = 'فشل في تحميل الكوبونات';
                console.error('Error loading coupons:', error);
            } finally {
                this.loading = false;
            }
        },

        async purchaseCoupon(coupon) {
            // Handle coupon purchase logic
            console.log('Purchasing coupon:', coupon);
        }
    }
}
</script>
```

## Error Handling

### Common Error Scenarios:
1. **Network Errors** - Connection issues with PointSys API
2. **API Failures** - PointSys service unavailable
3. **Invalid Data** - Malformed response from PointSys
4. **Server Errors** - Internal server issues

### Error Handling:
- **No Fallback Data** - Returns empty array when PointSys is unavailable
- **Clear Error Messages** - Shows specific error messages to users
- **Proper HTTP Status** - Returns appropriate HTTP status codes

### Logging:
- **Success Logs** - Number of coupons retrieved and filtered
- **Error Logs** - Detailed error information for debugging
- **API Failure Logs** - When PointSys API is unavailable

## Security Considerations

- **Public Access** - No authentication required for viewing coupons
- **Data Validation** - All PointSys data is validated before display
- **XSS Protection** - Coupon data is properly escaped
- **Rate Limiting** - Should be implemented at server level

## Performance

- **Response Time**: Typically < 2 seconds
- **Caching**: Consider implementing caching for better performance
- **Database Queries**: Minimal (no database queries)
- **External API**: 1 PointSys API call per request

## Testing

### Test Cases:
1. **Successful API Response** - Valid coupons from PointSys
2. **API Failure** - Return empty array with error message
3. **Empty Response** - Handle when no coupons available
4. **Invalid Data** - Handle malformed PointSys response
5. **Network Timeout** - Handle slow or unreachable API

### Test Commands:
```bash
# Test successful response
curl -X GET "http://127.0.0.1:8000/api/coupons"

# Test with PointSys down (should return sample data)
# Stop PointSys service and test
curl -X GET "http://127.0.0.1:8000/api/coupons"
```

## Integration

### Website Integration:
1. **AJAX Calls** - Use fetch() or axios to call the API
2. **Dynamic Display** - Update coupon grid with real-time data
3. **Error Handling** - Show appropriate messages for different states
4. **Loading States** - Show loading indicators during API calls
5. **Offline Support** - Consider caching coupons locally

### Backend Integration:
1. **PointSys Service** - Ensure proper configuration and error handling
2. **Logging** - Implement comprehensive logging for debugging
3. **Monitoring** - Monitor API response times and error rates
4. **Caching** - Implement caching strategy for better performance

This API provides a robust and scalable solution for displaying coupons from PointSys on the frontend website with comprehensive error handling and fallback mechanisms.
