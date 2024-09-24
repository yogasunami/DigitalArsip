<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Arsip Nota Dinas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
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

        h2 {
            text-align: center;
            color: #333;
        }

        .search-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-container input {
            padding: 10px;
            font-size: 16px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
        }

        .search-container button {
            background-color: #002366;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            cursor: pointer;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        th:hover {
            background-color: #ddd;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .button, .back-button {
            background-color: #002366;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-align: center;
            display: inline-block;
            cursor: pointer;
        }

        .button:hover, .back-button:hover {
            background-color: #0056b3;
        }

        .home-button, .back-button {
            background-color: #007bff;
            margin-bottom: 20px;
        }

        th.sortable {
            cursor: pointer;
        }
    </style>
    <script>
        // Fungsi untuk mengurutkan tabel
        function sortTable(n) {
            var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = document.getElementById("arsipTable");
            switching = true;
            dir = "asc"; 
            while (switching) {
                switching = false;
                rows = table.rows;
                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[n];
                    y = rows[i + 1].getElementsByTagName("TD")[n];
                    if (dir == "asc") {
                        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                    } else if (dir == "desc") {
                        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                    }
                }
                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchcount++;
                } else {
                    if (switchcount == 0 && dir == "asc") {
                        dir = "desc";
                        switching = true;
                    }
                }
            }
        }
    </script>
</head>
<body>
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
            <h2>Daftar Arsip Nota Dinas</h2>
        </main>
            <!-- Form Pencarian -->
            <div class="search-container">
                <form action="{{ route('documents.index') }}" method="GET">
                    <input type="text" name="search" placeholder="Cari judul atau kategori..." value="{{ request('search') }}">
                    <button type="submit">Cari</button>
                </form>
            </div>

        <!-- Tabel Arsip -->
        <table id="arsipTable">
            <thead>
                <tr>
                    <th class="sortable" onclick="sortTable(0)">No</th>
                    <th class="sortable" onclick="sortTable(1)">Judul</th>
                    <th class="sortable" onclick="sortTable(2)">Kategori</th>
                    <th class="sortable" onclick="sortTable(3)">Tanggal Upload</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $document)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $document->title }}</td>
                        <td>{{ $document->category }}</td>
                        <td>{{ $document->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('documents.show', $document->id) }}" class="button">Lihat Detail</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Jika tidak ada arsip -->
        @if($documents->isEmpty())
            <p style="text-align: center;">Tidak ada arsip yang tersedia.</p>
        @endif

        <a href="javascript:history.back()" class="back-button">Kembali</a>
        <a href="{{ route('home') }}" class="button home-button">Home</a>
    </div>
</body>
</html>
