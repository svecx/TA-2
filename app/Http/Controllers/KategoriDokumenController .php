<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriDokumen; // Sesuaikan dengan model yang Anda gunakan untuk tabel kategori_dokumen

class KategoriDokumenController extends Controller
{
    public function getOptions()
    {
        // Ambil semua data nama_dokumen dari tabel kategori_dokumen
        $kategoriDokumens = KategoriDokumen::pluck('nama_dokumen', 'nama_dokumen');

        // Format data untuk option HTML
        $options = '<option value="">Pilih Kategori Dokumen</option>';
        foreach ($kategoriDokumens as $nama_dokumen) {
            $options .= "<option value='$nama_dokumen'>$nama_dokumen</option>";
        }

        return $options;
    }
}
