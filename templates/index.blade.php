<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cloud Run Functions Template</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md max-w-md w-full">
        <h1 class="text-2xl font-bold mb-4 text-blue-600">Template App</h1>
        <p class="text-gray-700 mb-6">
            {{ $message }}
        </p>
        <div class="border-t pt-4">
            <p class="text-sm text-gray-500">
                Environment: <span class="font-mono">{{ \App\AppConfig::getEnvironment() }}</span>
            </p>
        </div>
    </div>
</body>
</html>
