<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    public function upload(Request $request)
    {
        // Validasi File Upload
        $validated = Validator::make($request->all(), [
            'files.*' => 'required|mimes:jpeg,jpg,png|max:5120',
        ]);

        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated)->withInput();
        }

        $files = $request->file('files');
        if (!is_array($files) || empty($files)) {
            return redirect()->back()->withErrors(['message' => 'Tidak ada file yang diunggah.']);
        }
        $pdfFiles = [];
        $title = '';

        // Proses setiap file
        foreach ($files as $index => $file) {
            $filePath = $file->store('documents');

            if (in_array($file->extension(), ['jpeg', 'jpg', 'png'])) {
                // OCR hanya untuk file utama (file pertama)
                if ($index === 0) {
                    $ocrResult = $this->runOCR(Storage::path($filePath));
                    $category = $this->classifyDocument($ocrResult['text']);
                    $title = $ocrResult['title']; // Gunakan judul hasil OCR
                }
                // Simpan path file untuk konversi PDF
                $pdfFiles[] = Storage::path($filePath);
            }
        }

        // Gabungkan file-file menjadi satu PDF
        $pdfPath = $this->convertImagesToPDF($pdfFiles, $title);

        // Simpan ke database
        Document::create([
            'title' => $title,
            'file_path' => $pdfPath,
            'category' => $category ?? 'Lainnya',
        ]);

        return redirect()->back()->with('success', 'Dokumen berhasil diupload dan diklasifikasikan.');
    }

    // Metode untuk menggabungkan beberapa gambar menjadi satu PDF
    private function convertImagesToPDF(array $imagePaths, $title)
    {
        $pdfPath = 'documents/' . $title . '.pdf';

        $pdf = new Fpdi();

        foreach ($imagePaths as $imagePath) {
            $pdf->AddPage();
            $pdf->Image($imagePath, 0, 0, 210, 297); // A4 size
        }

        $pdf->Output(Storage::path($pdfPath), 'F');

        return $pdfPath;
    }

    // Menjalankan OCR dan mendapatkan judul dari teks
    public function runOCR($filePath)
{
    try {
        $ocrText = (new TesseractOCR($filePath))->run();

        // Mencari teks setelah "Perihal"
        if (preg_match('/Perihal\s*:\s*(.*?)(?:\n|$)/i', $ocrText, $matches)) {
            $titleLine = $matches[1];  // Mengambil teks setelah "Perihal"
            $title = strtok(trim($titleLine), "\n");  // Menghentikan pada baris baru
            return ['text' => $ocrText, 'title' => trim($title)];
        }

        // Jika "Perihal" tidak ditemukan, kembalikan teks OCR
        return ['text' => $ocrText, 'title' => 'Dokumen Tanpa Perihal'];
    } catch (\Exception $e) {
        // Jika OCR gagal, log kesalahan dan kembalikan teks kosong
        Log::error('OCR Error: ' . $e->getMessage());
        return ['text' => '', 'title' => '']; // Jika OCR gagal
    }
}


    // Klasifikasi dokumen berdasarkan hasil OCR
    private function classifyDocument($ocrText)
    {
        if (preg_match('/Perihal\s*:\s*(.*)/i', $ocrText, $matches)) {
            $perihalText = strtok($matches[1], "\n");

            $keywords = [
                'Rapat' => ['rapat', 'pertemuan', 'meeting', 'kumpul'],
                'Dinas Luar' => ['perjalanan', 'dinas luar'],
                'Daftar' => ['daftar', 'list', 'urutan'],
                'Acara' => ['acara', 'perpisahan', 'perayaan', 'seminar'],
                'Permohonan' => ['pengajuan', 'permohonan', 'perizinan'],
                'Jadwal' => ['Jadwal', 'Urutan'],
                'Pelaksanaan' => ['kegiatan', 'giat', 'pelaksanaan'],
                'Pengerahan' => ['pengerahan, ']

            ];

            foreach ($keywords as $category => $words) {
                foreach ($words as $keyword) {
                    if (stripos($perihalText, $keyword) !== false) {
                        return $category;
                    }
                }
            }

            return 'Lainnya';
        }

        return 'Lainnya';
    }
        // Menampilkan dokumen
    public function show($id)
    {
        $document = Document::findOrFail($id); // Gunakan findOrFail untuk validasi ID
        return view('documents.show', compact('document'));
    }

    public function arsip()
    {
        $documents = Document::all();
        return view('arsip', compact('documents'));
    }

    public function download($id)
    {
        $document = Document::findOrFail($id);
        return response()->download(storage_path('app/' . $document->file_path), $document->title . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function index(Request $request)
    {
        // Ambil query pencarian dari input form
        $search = $request->input('search');

        // Query untuk mendapatkan dokumen berdasarkan pencarian
        $documents = Document::query()
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', '%' . $search . '%')
                             ->orWhere('category', 'like', '%' . $search . '%');
            })
            ->get();

        // Kembalikan view dengan hasil pencarian
        return view('documents.index', compact('documents'));
    }

    public function store(Request $request)
    {
        // Validasi file dan input lainnya
        $request->validate([
            'scanned_file' => 'required|mimes:pdf|max:10000',
        ]);

        // Simpan file
        if ($request->hasFile('scanned_file')) {
            $file = $request->file('scanned_file');
            $filePath = $file->store('documents'); // Simpan ke folder 'documents'

            // Simpan informasi ke database
            Document::create([
                'title' => 'Nama File', // Atau dari OCR atau input lain
                'file_path' => $filePath,
                'category' => 'kategori yang sesuai',
                'created_at' => now(),
            ]);

            return redirect()->route('documents.index')->with('success', 'File berhasil disimpan');
        }

        return back()->with('error', 'Gagal menyimpan file');
    }

    public function convertToPDF(Request $request)
    {
        // Ambil data dari request
        $scannedImages = $request->input('scannedImages');
        $fileName = $request->input('fileName');
        $category = $request->input('category');

        // Cek apakah ada gambar yang discan
        if (empty($scannedImages)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada gambar yang dipindai.']);
        }

        // Path untuk menyimpan file PDF
        $pdfPath = storage_path('app/public/documents/' . $fileName);

        // Mengonversi gambar ke PDF (gunakan library seperti Dompdf, TCPDF, atau setImage in FPDF)
        $pdf = new \FPDF(); // Misalnya menggunakan FPDF
        foreach ($scannedImages as $image) {
            $pdf->AddPage();
            $pdf->Image(storage_path('app/' . $image), 10, 10, 190); // Sesuaikan dengan ukuran halaman
        }
        $pdf->Output($pdfPath, 'F'); // Simpan file PDF

        // Simpan informasi PDF ke database
        Document::create([
            'title' => pathinfo($fileName, PATHINFO_FILENAME), // Hanya nama file tanpa extension
            'file_path' => 'documents/' . $fileName, // Relative path
            'category' => $category,
        ]);

        return response()->json(['success' => true, 'message' => 'Dokumen berhasil dikonversi dan disimpan.']);
    }

    public function scanlgsg(Request $request)
{
    // Validasi file upload
    $validated = Validator::make($request->all(), [
        'files.*' => 'required|image|max:5120',
    ]);

    if ($validated->fails()) {
        return response()->json(['success' => false, 'message' => $validated->errors()->first()]);
    }

    $files = $request->file('files');
    $pdfFiles = [];
    $title = '';

    // Proses setiap file
    foreach ($files as $index => $file) {
        // Simpan file gambar ke penyimpanan sementara
        $filePath = $file->store('scanned_images');

        // Jalankan OCR untuk file pertama
        if ($index === 0) {
            $ocrResult = $this->runOCR(Storage::path($filePath));
            $title = $ocrResult['title'];
        }

        // Simpan path file untuk konversi PDF
        $pdfFiles[] = Storage::path($filePath);
    }

    // Gabungkan file-file menjadi satu PDF
    $pdfPath = $this->convertImagesToPDF($pdfFiles, $title);

    // Simpan ke database
    Document::create([
        'title' => $title,
        'file_path' => $pdfPath,
        'category' => 'Lainnya', // Atur kategori sesuai kebutuhan
    ]);

    return response()->json(['success' => true, 'title' => $title]);
}



}
