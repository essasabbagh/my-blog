<!-- resources/views/layouts/app.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My Blog')</title>
    @vite('resources/css/app.css')
</head>
<body class="text-gray-900 bg-gray-100">
    <header class="p-4 text-white bg-blue-500">
        <div class="container mx-auto">
            <h1 class="text-2xl">My Blog</h1>
        </div>
    </header>

    <main class="py-8">
        @yield('content')
    </main>

    <footer class="p-4 mt-8 text-white bg-blue-500">
        <div class="container mx-auto">
            <p>&copy; {{ date('Y') }} My Blog. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>