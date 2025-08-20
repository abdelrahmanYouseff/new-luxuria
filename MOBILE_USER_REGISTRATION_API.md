# Mobile User Registration API Documentation

## Overview
This API provides endpoints for user registration from mobile applications with automatic integration to PointSys and External APIs.

## Base URL
```
http://127.0.0.1:8000/api
```

## Authentication
No authentication required for registration. Returns JWT token upon successful registration.

## Endpoints

### 1. User Registration

**Endpoint:** `POST /mobile/users/register`

**Description:** Register a new user from mobile app with automatic PointSys and External API integration.

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
    "name": "أحمد محمد",
    "email": "ahmed@example.com",
    "phone": "0501234567",
    "password": "password123",
    "emirate": "Dubai",
    "address": "شارع الشيخ زايد"
}
```

**Required Fields:**
- `name`: User full name (required)
- `email`: User email address (required, unique)
- `password`: User password (required, min 6 characters)
- `phone`: User phone number (optional)
- `emirate`: User emirate (optional, defaults to "Dubai")
- `address`: User address (optional, defaults to "Mobile App User")

**Response (Success - 201):**
```json
{
    "success": true,
    "message": "تم تسجيل المستخدم بنجاح",
    "data": {
        "user": {
            "id": 55,
            "name": "أحمد محمد",
            "email": "ahmed@example.com",
            "phone": "0501234567",
            "emirate": "Dubai",
            "address": "شارع الشيخ زايد",
            "role": "user",
            "pointsys_customer_id": "47",
            "external_customer_id": "0198c6ce-0ae6-7383-a2af-316ca7c5e4e7"
        },
        "token": "1|laravel_sanctum_token_here",
        "pointsys_registration": {
            "status": "success",
            "message": "تم ربط العميل بالعميل الموجود في PointSys",
            "data": {
                "customer_id": "47",
                "name": "أحمد محمد",
                "email": "ahmed@example.com",
                "phone": "0501234567",
                "points_balance": 0
            }
        },
        "external_registration": {
            "success": true,
            "external_customer_id": "0198c6ce-0ae6-7383-a2af-316ca7c5e4e7",
            "message": "Customer created successfully"
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
        "email": ["The email has already been taken."],
        "password": ["The password must be at least 6 characters."]
    }
}
```

**Response (Server Error - 500):**
```json
{
    "success": false,
    "message": "فشل في تسجيل المستخدم",
    "error": "Detailed error message"
}
```

## Registration Process

### Step 1: Local Database
1. Validate input data
2. Create user record in local database
3. Set default values for optional fields

### Step 2: PointSys Integration
1. **First Attempt:** Try to register with original email and phone
2. **If Email Exists:** Search for existing customer and link to it
3. **If Search Fails:** Create new customer with modified email
4. **Timeout Protection:** Maximum 12 seconds execution time

### Step 3: External API Integration
1. Register user in RLAPP system
2. Store external customer ID
3. Continue even if external registration fails

### Step 4: Token Generation
1. Generate Laravel Sanctum token
2. Return token for mobile app authentication

## Features

### ✅ Automatic PointSys Registration
- Uses original user data when possible
- Links to existing customers automatically
- Fallback to modified email if needed
- Timeout protection prevents hanging

### ✅ External API Integration
- Automatic registration in RLAPP
- Error handling for external failures
- Continues registration process even if external API fails

### ✅ Data Validation
- Email uniqueness validation
- Password strength requirements
- Phone number format validation
- Required field validation

### ✅ Error Handling
- Detailed error messages
- Validation error responses
- Server error logging
- Graceful degradation

### ✅ Security Features
- Password hashing
- JWT token generation
- Input sanitization
- CSRF protection disabled for API

## Usage Examples

### cURL Example
```bash
curl -X POST "http://127.0.0.1:8000/api/mobile/users/register" \
     -H "Content-Type: application/json" \
     -d '{
         "name": "أحمد محمد",
         "email": "ahmed@example.com",
         "phone": "0501234567",
         "password": "password123",
         "emirate": "Dubai",
         "address": "شارع الشيخ زايد"
     }'
```

### JavaScript Example
```javascript
const registerUser = async (userData) => {
    try {
        const response = await fetch('http://127.0.0.1:8000/api/mobile/users/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(userData)
        });

        const data = await response.json();

        if (data.success) {
            // Store token
            localStorage.setItem('token', data.data.token);

            // User data
            console.log('User registered:', data.data.user);
            console.log('PointSys status:', data.data.pointsys_registration);
            console.log('External API status:', data.data.external_registration);
        } else {
            console.error('Registration failed:', data.message);
            if (data.errors) {
                console.error('Validation errors:', data.errors);
            }
        }

        return data;
    } catch (error) {
        console.error('Error:', error);
    }
};

// Usage
registerUser({
    name: "أحمد محمد",
    email: "ahmed@example.com",
    phone: "0501234567",
    password: "password123",
    emirate: "Dubai",
    address: "شارع الشيخ زايد"
});
```

### React Native Example
```javascript
import AsyncStorage from '@react-native-async-storage/async-storage';

const registerUser = async (userData) => {
    try {
        const response = await fetch('http://127.0.0.1:8000/api/mobile/users/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(userData)
        });

        const data = await response.json();

        if (data.success) {
            // Store token securely
            await AsyncStorage.setItem('userToken', data.data.token);
            await AsyncStorage.setItem('userData', JSON.stringify(data.data.user));

            // Navigate to main app
            navigation.replace('MainApp');
        } else {
            Alert.alert('Registration Failed', data.message);
        }

        return data;
    } catch (error) {
        Alert.alert('Error', 'Network error occurred');
        console.error('Registration error:', error);
    }
};
```

## Testing

### Test Registration
```bash
# Test with new user
curl -X POST "http://127.0.0.1:8000/api/mobile/users/register" \
     -H "Content-Type: application/json" \
     -d '{
         "name": "Test User",
         "email": "test'$(date +%s)'@example.com",
         "phone": "0501234567",
         "password": "password123"
     }'
```

### Check Logs
```bash
# Monitor registration logs
tail -f storage/logs/laravel.log | grep -E "(Mobile:|pointsys_customer_id|external_customer_id)"
```

## Error Scenarios

### 1. Email Already Exists
```json
{
    "success": false,
    "message": "بيانات غير صحيحة",
    "errors": {
        "email": ["The email has already been taken."]
    }
}
```

### 2. PointSys Timeout
```json
{
    "success": true,
    "message": "تم تسجيل المستخدم بنجاح",
    "data": {
        "user": { ... },
        "token": "...",
        "pointsys_registration": {
            "status": "success",
            "message": "تم تسجيل العميل بنجاح (Mock Mode - PointSys غير متاح)"
        }
    }
}
```

### 3. External API Failure
```json
{
    "success": true,
    "message": "تم تسجيل المستخدم بنجاح",
    "data": {
        "user": { ... },
        "token": "...",
        "external_registration": {
            "success": false,
            "message": "Failed to create customer"
        }
    }
}
```

## Security Considerations

- Passwords are hashed using Laravel's default hashing
- JWT tokens are generated using Laravel Sanctum
- Input validation prevents SQL injection
- Rate limiting should be implemented at server level
- API endpoints should be protected with CORS policies

## Performance

- Registration typically completes in under 10 seconds
- Timeout protection prevents hanging requests
- Database transactions ensure data consistency
- API calls are made in parallel when possible

## Monitoring

### Key Metrics to Monitor:
1. Registration success rate
2. Average registration time
3. PointSys integration success rate
4. External API integration success rate
5. Error rates and types

### Alerting:
- High error rates (>5%)
- Long registration times (>15 seconds)
- PointSys integration failures
- External API failures
