<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') ?? 'Laravel'}}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="w-screen min-h-screen bg-gray-100 overflow-x-hidden">

    @vite('resources/js/app.js')
    @livewireScripts
</body>
</html>