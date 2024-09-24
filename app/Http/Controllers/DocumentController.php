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
    private function runOCR($filePath)
    {
        try {
            $ocrText = (new TesseractOCR($filePath))->run();
            if (preg_match('/Perihal\s*:\s*(.*)/i', $ocrText, $matches)) {
                $titleLine = $matches[1];  // Mengambil teks setelah "Perihal"
                $title = strtok($titleLine, "\n");  // Menghentikan pada baris baru
                return ['text' => $ocrText, 'title' => trim($title)];
            }

            // Jika "Perihal" tidak ditemukan, kembalikan teks OCR
            return ['text' => $ocrText, 'title' => 'Dokumen Tanpa Perihal'];
        } catch (\Exception $e) {
            return ['text' => '', 'title' => '']; // Jika OCR gagal
        }
    }

    // Klasifikasi dokumen berdasarkan hasil OCR
    private function classifyDocument($ocrText)
    {
        if (preg_match('/Perihal\s*:\s*(.*)/i', $ocrText, $matches)) {
            $perihalText = strtok($matches[1], "\n");  // Mengambil teks setelah "Perihal"

            $keywordsRapat = ['rapat', 'pertemuan', 'meeting', 'kumpul'];
            $keywordsDinasLuar = ['perjalanan', 'dinas luar'];
            $keywordsDaftar = ['daftar', 'list', 'urutan'];

            foreach ($keywordsRapat as $keyword) {
                if (stripos($perihalText, $keyword) !== false) {
                    return 'Rapat';
                }
            }

            foreach ($keywordsDinasLuar as $keyword) {
                if (stripos($perihalText, $keyword) !== false) {
                    return 'Dinas Luar';
                }
            }

            foreach ($keywordsDaftar as $keyword) {
                if (stripos($perihalText, $keyword) !== false) {
                    return 'Daftar';
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

}
