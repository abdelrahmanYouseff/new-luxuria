<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'Luxuria UAE - Premium luxury car rental service in the United Arab Emirates. Rent luxury, sports, and classic vehicles with exceptional service.')" />
    <link rel="canonical" href="@yield('canonical_url', url()->current())" />

    <title>@yield('title', 'Luxuria UAE')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>

    @yield('head')
</head>
<body class="antialiased">
    @yield('content')

    @yield('scripts')
</body>
</html>
