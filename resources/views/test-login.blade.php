<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Test login page for Luxuria UAE luxury car rental service." />
    <link rel="canonical" href="{{ url()->current() }}" />
    <title>Test Login</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .error { color: red; margin-top: 10px; }
        .success { color: green; margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Test Login</h1>

    @if($errors->any())
        <div class="error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="admin@rentluxuria.com" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" value="password123" required>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="remember" value="1"> Remember me
            </label>
        </div>

        <button type="submit">Login</button>
    </form>

    <div style="margin-top: 30px;">
        <h3>Debug Info:</h3>
        <p>CSRF Token: {{ csrf_token() }}</p>
        <p>Session ID: {{ session()->getId() }}</p>
        <p>Route: {{ route('login') }}</p>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            console.log('Form submitted');
            console.log('Email:', document.getElementById('email').value);
            console.log('Password:', document.getElementById('password').value);
            console.log('CSRF Token:', document.querySelector('input[name="_token"]').value);
        });
    </script>
</body>
</html>
