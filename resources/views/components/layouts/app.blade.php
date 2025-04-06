<!DOCTYPE html>
<html>

<head>
    <title>{{ $title ?? 'My App' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body>
    {{ $slot }}

    @livewireScripts
</body>

</html>
