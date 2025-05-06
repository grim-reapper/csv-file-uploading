<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>File Upload System</title>
    @vite(['resources\css\app.css', 'resources\js\app.js'])
</head>
<body class="bg-gray-100">
    <div id="app">
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h1 class="text-2xl font-bold mb-4">CSV File Upload</h1>
                <file-uploader></file-uploader>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Upload History</h2>
                <upload-list></upload-list>
            </div>
        </div>
    </div>
</body>
</html>