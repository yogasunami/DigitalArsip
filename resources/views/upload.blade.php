<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Nota Dinas</title>

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
            flex-direction: column;
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

        .upload-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 2;
            position: relative;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%; /* Sesuaikan lebar form */
        }

        h2 {
            text-align: center;
            color: #333;
            margin-top: 10px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 2;
            margin-top: 20px;
        }

        input[type="file"] {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 70%;
            font-size: 1em;
        }

        button {
            background-color: #28a745;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2em;
            width: 200px;
        }

        button:hover {
            background-color: #218838;
        }

        .message {
            margin-top: 10px;
            text-align: center;
            font-size: 14px;
        }

        .message.success {
            color: green;
        }

        .message.error {
            color: red;
        }

        .button, .back-button {
            background-color: #002366;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2em;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-top: 20px;
            text-align: center;
            z-index: 2;
        }

        .button:hover, .back-button:hover {
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
            <div class="upload-container">
                <h2>Upload Nota Dinas</h2>

                <!-- Notifikasi Pesan -->
                @if(session('success'))
                    <p class="message success">{{ session('success') }}</p>
                @endif

                @if($errors->any())
                    <p class="message error">{{ implode('', $errors->all(':message')) }}</p>
                @endif

                <!-- Form Upload -->
                <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Input File -->
                    <label for="file">Pilih File (Gambar atau PDF):</label>
                    <input type="file" name="files[]" id="file" accept="image/*,application/pdf" multiple required>

                    <!-- Tombol Upload -->
                    <button type="submit">Upload</button>
                </form>

                <a href="javascript:history.back()" class="back-button">Kembali</a>
                <!-- Tombol Home -->
                <a href="{{ route('home') }}" class="button">Home</a>
            </div>
        </main>

        <footer>
            <p>&copy; 2024 Yoga Sunami - All rights reserved</p>
        </footer>
    </div>
</body>
</html>
