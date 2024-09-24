<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ScanController extends Controller
{
    // Fungsi untuk memproses OCR dan mendeteksi pemisah
    public function processOCR(Request $request)
    {
        // Simpan gambar yang discan dan jalankan OCR
        $image = $request->file('scannedImage');
        $path = $image->store('documents');
    
        $ocrResult = $this->runOCR(Storage::path($path));
    
        return response()->json([
            'success' => true,
            'title' => $ocrResult['title'],
            'category' => $this->classifyDocument($ocrResult['text']),
            'imagePath' => $path
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
    $imagePath = $request->input('scannedImagePath');
    $title = $request->input('fileName');
    $category = $request->input('category');

    // Proses konversi gambar menjadi PDF
    $pdfPath = $this->convertImagesToPDF([Storage::path($imagePath)], $title);

    // Simpan dokumen ke database
    Document::create([
        'title' => $title,
        'file_path' => $pdfPath,
        'category' => $category
    ]);

    return response()->json(['success' => true]);
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

    public function saveScannedImages(Request $request)
    {
        // Pastikan ada file yang diunggah
        if (!$request->hasFile('scannedImages')) {
            return response()->json(['success' => false, 'message' => 'Tidak ada gambar yang diunggah.']);
        }
    
        $scannedImages = $request->file('scannedImages');
    
        foreach ($scannedImages as $image) {
            // Validasi setiap file gambar jika diperlukan
            if ($image->isValid()) {
                // Simpan gambar ke direktori yang diinginkan, misalnya 'public/scanned_images'
                $fileName = 'scanned_image_' . time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/scanned_images', $fileName);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal mengunggah gambar.']);
            }
        }
    
        return response()->json(['success' => true, 'message' => 'Semua gambar berhasil disimpan.']);
    }
            
}    