<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;

class ScanController extends Controller
{
    // Fungsi untuk memproses OCR dan mendeteksi pemisah
    public function processOCR(Request $request)
    {
        $imagePath = $request->input('imagePath');
        $ocrResult = $this->runTesseractOCR($imagePath);
    
        // Deteksi kata pemisah dan buat nama file dinamis
        $fileName = null;
        $isSeparator = false;
    
        // Contoh deteksi kata dan pemberian nama dinamis
        if (strpos($ocrResult, 'Multi') !== false) {
            $isSeparator = true;
            $fileName = $this->generateDynamicFileName('Rapat');
        } elseif (strpos($ocrResult, 'perjalanan') !== false) {
            $isSeparator = true;
            $fileName = $this->generateDynamicFileName('Dinas Luar');
        }
    
        return response()->json([
            'isSeparator' => $isSeparator,
            'fileName' => $fileName
        ]);
    }
    
    // Fungsi untuk membuat nama file dinamis berdasarkan jenis dokumen
    private function generateDynamicFileName($prefix)
    {
        // Hitung jumlah dokumen dengan nama yang mirip di database
        $count = \DB::table('documents')
                    ->where('title', 'LIKE', $prefix . '%')
                    ->count();
    
        // Buat nama file dengan format yang diinginkan, contoh: Rapat1, Rapat2, dst.
        return $prefix . ($count + 1);
    }
    
    // Fungsi untuk membuat PDF dari hasil scan
    public function generatePDF(Request $request)
    {
        $scannedImages = $request->input('images');
        $fileName = $request->input('fileName');  // Ambil nama file dari request
    
        // Buat nama file PDF dengan format dinamis
        $pdfPath = storage_path('app/public/' . $fileName . '.pdf');
        $pdf = new Fpdi();
    
        foreach ($scannedImages as $imagePath) {
            $pdf->AddPage();
            $pdf->Image($imagePath, 0, 0, 210, 297); // Sesuaikan ukuran gambar
        }
    
        $pdf->Output($pdfPath, 'F');
    
        // Simpan PDF ke database
        $this->storePDFsToDatabase($pdfPath, $fileName);
    
        return response()->json(['message' => 'PDF berhasil dibuat dan disimpan di database']);
    }
    
    // Fungsi untuk menyimpan PDF ke database
    private function storePDFsToDatabase($pdfPath, $fileName)
    {
        Storage::put('public/scans/' . basename($pdfPath), file_get_contents($pdfPath));
    
        \DB::table('documents')->insert([
            'folder_id' => 1,  // Sesuaikan folder
            'title' => $fileName,
            'file_path' => 'storage/scans/' . basename($pdfPath),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function home()
    {
        return view('home'); // pastikan view 'home' ada
    }
}    