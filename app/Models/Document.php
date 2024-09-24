<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;
    
    protected $table = 'documents';
    
    protected $fillable = [
        'title',         // Judul atau nama file
        'file_path',     // Lokasi file PDF
        'category',      // Kategori dokumen, misalnya Mabes TNI AU, Disinfolahta
        'created_at',
        'updated_at'
    ];
}
