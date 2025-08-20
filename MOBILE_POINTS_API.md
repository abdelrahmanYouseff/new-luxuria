# Mobile User Points API Documentation

## Overview
This API provides endpoints for retrieving user points from the PointSys system for mobile applications.

## Base URL
```
http://127.0.0.1:8000/api
```

## Authentication
**Required**: Laravel Sanctum token must be provided in the Authorization header.

```
Authorization: Bearer {token}
```

## Endpoints

### 1. Get User Points

**Endpoint:** `GET /mobile/points`

**Description:** Retrieve user points balance from PointSys system.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Response (Success - 200):**
```json
{
    "success": true,
    "message": "تم جلب النقاط بنجاح",
    "data": {
        "points": 250,
        "customer_id": "47",
        "customer_data": {
            "customer_id": "47",
            "name": "أحمد محمد",
            "email": "ahmed@example.com",
            "phone": "0501234567",
            "points_balance": 250,
            "registration_date": "2025-01-15"
        },
        "formatted_points": "250 نقطة"
    }
}
```

**Response (User Not Linked - 200):**
```json
{
    "success": false,
    "message": "المستخدم غير مرتبط بنظام PointSys",
    "data": {
        "points": 0,
        "customer_id": null
    }
}
```

**Response (Authentication Error - 401):**
```json
{
    "success": false,
    "message": "المستخدم غير مصدق عليه"
}
```

**Response (Server Error - 500):**
```json
{
    "success": false,
    "message": "حدث خطأ أثناء جلب النقاط",
    "error": "Detailed error message",
    "data": {
        "points": 0,
        "customer_id": null
    }
}
```

## Usage Examples

### cURL Example
```bash
curl -X GET "http://127.0.0.1:8000/api/mobile/points" \
     -H "Authorization: Bearer {your_token_here}" \
     -H "Content-Type: application/json"
```

### JavaScript Example
```javascript
const getUserPoints = async (token) => {
    try {
        const response = await fetch('http://127.0.0.1:8000/api/mobile/points', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
        });

        const data = await response.json();

        if (data.success) {
            console.log('Points:', data.data.points);
            console.log('Formatted Points:', data.data.formatted_points);
            console.log('Customer Data:', data.data.customer_data);
        } else {
            console.error('Failed to get points:', data.message);
        }

        return data;
    } catch (error) {
        console.error('Error:', error);
    }
};

// Usage
getUserPoints('your_jwt_token_here');
```

### React Native Example
```javascript
import AsyncStorage from '@react-native-async-storage/async-storage';

const getUserPoints = async () => {
    try {
        const token = await AsyncStorage.getItem('userToken');

        const response = await fetch('http://127.0.0.1:8000/api/mobile/points', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
        });

        const data = await response.json();

        if (data.success) {
            // Update UI with points
            setPoints(data.data.points);
            setFormattedPoints(data.data.formatted_points);
        } else {
            Alert.alert('Error', data.message);
        }

        return data;
    } catch (error) {
        Alert.alert('Network Error', 'Failed to fetch points');
        console.error('Error:', error);
    }
};
```

## Implementation Details

### Method: `getUserPoints()`

**Location:** `app/Http/Controllers/MobileReservationController.php`

**Features:**
1. **Authentication Check** - Validates user token
2. **PointSys Integration** - Connects to PointSys API
3. **Error Handling** - Comprehensive error handling with logging
4. **Data Formatting** - Returns formatted points in Arabic

### Flow:
1. **Authenticate User** - Get user from Sanctum token
2. **Check PointSys ID** - Verify user has `pointsys_customer_id`
3. **Call PointSys API** - Get customer balance using PointSysService
4. **Process Response** - Format and return data
5. **Handle Errors** - Log errors and return appropriate responses

### Data Structure:
- **points**: Raw points balance (integer)
- **customer_id**: PointSys customer ID
- **customer_data**: Complete customer data from PointSys
- **formatted_points**: Human-readable points with Arabic text

## Error Handling

### Common Errors:
1. **User Not Authenticated** - Returns 401 with message "المستخدم غير مصدق عليه"
2. **User Not Linked** - Returns 200 with points = 0
3. **PointSys API Error** - Returns 200 with fallback data
4. **Network Error** - Returns 500 with error details

### Logging:
- **Success**: Logs user ID, PointSys customer ID, and points retrieved
- **Failures**: Logs user ID, error messages, and full stack traces
- **Warnings**: Logs when PointSys API calls fail but returns fallback data

## Security Considerations

- **Authentication Required** - Only authenticated users can access
- **User-Specific Data** - Users can only see their own points
- **Input Validation** - No user input required (GET request)
- **Error Masking** - Sensitive error details are logged but not exposed to client

## Performance

- **Response Time**: Typically < 2 seconds
- **Caching**: No caching implemented (real-time data)
- **Database Queries**: Minimal (1 user lookup)
- **External API**: 1 PointSys API call per request

## Testing

### Test Cases:
1. **Valid User with Points** - Should return points balance
2. **Valid User without Points** - Should return 0 points
3. **User Not Linked to PointSys** - Should return 0 with appropriate message
4. **Invalid Token** - Should return 401
5. **PointSys API Down** - Should return fallback data

### Test Commands:
```bash
# Test with valid token
curl -X GET "http://127.0.0.1:8000/api/mobile/points" \
     -H "Authorization: Bearer valid_token_here"

# Test without token
curl -X GET "http://127.0.0.1:8000/api/mobile/points"

# Test with invalid token
curl -X GET "http://127.0.0.1:8000/api/mobile/points" \
     -H "Authorization: Bearer invalid_token"
```

## Integration

### Mobile App Integration:
1. **Store Token** - Save JWT token after login
2. **Call API** - Make GET request to `/api/mobile/points`
3. **Handle Response** - Update UI with points data
4. **Error Handling** - Show appropriate error messages
5. **Refresh Data** - Call API when points may have changed

### Backend Integration:
1. **User Model** - Requires `pointsys_customer_id` field
2. **PointSysService** - Must have `getCustomerBalance()` method
3. **Logging** - Ensure proper logging is configured
4. **Error Monitoring** - Monitor PointSys API failures

## Future Enhancements

### Potential Improvements:
1. **Caching** - Cache points for better performance
2. **WebSocket Updates** - Real-time points updates
3. **Points History** - Get points transaction history
4. **Points Transfer** - Transfer points between users
5. **Points Expiration** - Show expiring points notifications

This API provides a robust and secure way for mobile applications to retrieve user points from the PointSys system with comprehensive error handling and logging.
