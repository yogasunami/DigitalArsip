<!DOCTYPE html>
<html>
<head>
    <title>Document Management</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <nav>
        <a href="{{ route('documents.index') }}">Home</a>
        <a href="{{ route('documents.create') }}">Upload Document</a>
    </nav>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>
