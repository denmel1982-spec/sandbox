<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Components Platform') }}</title>
    <meta name="description" content="Веб-платформа для поиска электронных компонентов и радиодеталей">
</head>
<body>
    <div id="root"></div>
    @viteReactRefresh
    @vite(['resources/js/main.jsx', 'resources/css/app.css'])
</body>
</html>
