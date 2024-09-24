<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $document->title }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6ff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100vh;
            position: relative;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            height: 100vh;
            padding-top: 150px; /* Adjust for header */
            position: relative;
            z-index: 1;
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
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-grow: 1;
            position: relative;
            width: 80%; /* Adjust width */
            z-index: 2; /* Make sure main content is above the background */
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

        .document-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 3; /* Ensure it's above other elements */
            position: relative;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        p {
            color: #555;
            margin-top: 10px;
        }

        embed {
            margin-top: 20px;
            border: 1px solid #ccc;
            width: 100%;
            height: 600px;
            z-index: 2;
            position: relative;
        }

        .btn {
            background-color: #28a745;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2em;
            margin-top: 20px;
            text-decoration: none;
            text-align: center;
            z-index: 4;
        }

        .btn:hover {
            background-color: #218838;
        }

        .home-button, .back-button {
            background-color: #002366;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2em;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-top: 20px;
            z-index: 4;
            position: relative;
        }

        .home-button:hover, .back-button:hover {
            background-color: #0044cc;
        }

        footer {
            text-align: center;
            font-size: 0.9em;
            color: #555;
            padding: 20px;
            background-color: #f4f6ff;
            z-index: 5;
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
            <div class="document-container">
                <!-- Title dan Kategori -->
                <h1>{{ $document->title }}</h1>
                <p>Kategori: {{ $document->category }}</p>

                <!-- Preview PDF -->
                <embed src="{{ Storage::url($document->file_path) }}" type="application/pdf"/>

                <!-- Tombol Download PDF -->
                <a href="{{ route('document.download', $document->id) }}" class="btn">Download PDF</a>
            </div>

            <!-- Tombol Back dan Home -->
            <a href="javascript:history.back()" class="back-button">Kembali</a>
            <a href="{{ route('home') }}" class="home-button">Home</a>
        </main>

        <footer>
            <p>&copy; 2024 Yoga Sunami - All rights reserved</p>
        </footer>
    </div>
</body>
</html>
