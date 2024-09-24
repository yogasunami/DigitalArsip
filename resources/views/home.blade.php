<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Arsip</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6ff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100vh;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            height: 100vh;
        }

        header {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #002366;
            width: 100%;
            padding: 20px;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
        }

        .emblem {
            position: absolute;
            left: 50px;
        }

        .emblem img {
            width: 120px;
        }

        .title {
            text-align: center;
        }

        .title h1 {
            font-size: 3em;
            margin-bottom: 10px;
        }

        .title p {
            font-size: 1.2em;
        }

        main {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            margin-top: 150px; /* Push content below the fixed header */
            position: relative;
        }

        .background-image {
            background: url('{{asset('Logo/logo disinfo.png') }}') no-repeat center;
            background-size: contain;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.2;
            z-index: 1;

        }

        .buttons {
            display: flex;
            gap: 20px;
            position: relative;
            z-index: 2;
        }

        .button {
            background-color: #002366;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2em;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #0044cc;
        }

        footer {
            text-align: center;
            font-size: 0.9em;
            color: #555;
            padding: 20px;
            background-color: #f4f6ff;
        }
    </style>
</head>
<body class="bg-gray-100">
<div class="container">
        <header>
            <div class="emblem">
                <img src="{{ asset('Logo/logo disinfo.png') }}" alt="TNI AU Emblem">
            </div>
            <div class="title">
                <h1>DIANODIN</h1>
                <p>Digitalisasi Arsip Nota Dinas Disinfolahta TNI AU</p>
            </div>
        </header>

        <main>
            <div class="background-image"></div>
            <div class="buttons">
                <a href="{{ route('scan') }}" class="button">SCAN</a>
                <a href="{{ route('upload') }}" class="button">UPLOAD</a>
                <a href="{{ route('arsip') }}" class="button">ARSIP</a>
            </div>
        </main>

        <footer>
            <p>&copy; 2024 Yoga Sunami - All rights reserved</p>
        </footer>
    </div>
</body>
</html>
