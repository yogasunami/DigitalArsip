<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Document</title>
    <script src="/path/to/DynamicWebTWAIN.min.js"></script> <!-- Path ke Dynamic Web TWAIN -->
</head>
<body>
    <h1>Scan Dokumen</h1>
    <div id="dwtcontrolContainer"></div> <!-- Tempat Dynamic Web TWAIN -->
    <button id="scanButton">Mulai Scan</button>
    
    <script>
        // Inisialisasi Dynamic Web TWAIN
        Dynamsoft.WebTwainEnv.Load();
        var DWObject;

        Dynamsoft.WebTwainEnv.RegisterEvent('OnWebTwainReady', function() {
            DWObject = Dynamsoft.WebTwainEnv.GetWebTwain('dwtcontrolContainer');
        });

        // Fungsi untuk memulai scan
        document.getElementById('scanButton').addEventListener('click', function() {
            if (DWObject) {
                DWObject.SelectSource(); // Pilih scanner
                DWObject.OpenSource();
                DWObject.AcquireImage(); // Mulai proses scan
            }
        });

        // Fungsi untuk menyimpan hasil scan
        DWObject.IfShowFileDialog = false;
        DWObject.OnPostTransfer = function() {
            var scannedImage = DWObject.SaveAsJPEG('scanned_image.jpg', 0); // Simpan sebagai JPEG
            console.log(scannedImage);
        };
    </script>
</body>
</html>