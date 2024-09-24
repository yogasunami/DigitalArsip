<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Dokumen</title>
    <script src="/path/to/DynamicWebTWAIN.min.js"></script> <!-- Sesuaikan path ke file Dynamic Web TWAIN -->
</head>
<body>
    <h1>Scan Dokumen</h1>
    <div id="dwtcontrolContainer"></div> <!-- Tempat untuk meletakkan kontrol Dynamic Web TWAIN -->
    <button id="scanButton">Mulai Scan</button>

    <script>
        // Inisialisasi Dynamic Web TWAIN
        Dynamsoft.WebTwainEnv.Load();
        var DWObject;

        Dynamsoft.WebTwainEnv.RegisterEvent('OnWebTwainReady', function() {
            DWObject = Dynamsoft.WebTwainEnv.GetWebTwain('dwtcontrolContainer');
        });

        // Mulai proses scan
        document.getElementById('scanButton').addEventListener('click', function() {
            if (DWObject) {
                DWObject.SelectSource(); // Pilih scanner
                DWObject.OpenSource(); // Buka koneksi dengan scanner
                DWObject.AcquireImage(); // Mulai scan
            }
        });

        // Simpan hasil scan
        DWObject.IfShowFileDialog = false;
        DWObject.OnPostTransfer = function() {
            var scannedImage = DWObject.SaveAsJPEG('scanned_image.jpg', 0); // Simpan sebagai JPEG
            console.log(scannedImage);
        };
    </script>
</body>
</html>
