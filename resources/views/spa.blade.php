<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    @vite(['resources/js/app.js'])

    @php($appConfig = [
        'fields' => config('fields'),
        'users' => [
            'max' => config('users.max'),
        ],
    ])
    <script>
        window.AppConfig = @json($appConfig);
    </script>
</head>
<body class="bg-gray-100">
    <div id="app"></div>
</body>
</html>
