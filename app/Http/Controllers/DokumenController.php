<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dokumen;
use App\Models\Draft;
use App\Models\History;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DokumenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Memastikan pengguna harus login
    }

    public function input()
    {
        return view('input_dokumen');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'judul_dokumen' => 'required|string|max:255',
            'deskripsi_dokumen' => 'required|string',
            'kategori_dokumen' => 'required|string',
            'validasi_dokumen' => 'required|string',
            'tahun_dokumen' => 'required|int',
            'dokumen_file' => 'required|file|mimes:pdf,docx,jpeg,png,jpg|max:2048',
            'tags' => 'nullable|string',
            'created_by' => 'nullable|string',
        ]);

        $fileName = $request->dokumen_file->getClientOriginalName();
        $request->dokumen_file->storeAs('public/documents', $fileName);
        
        // Ambil pengguna yang sedang login
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Buat dokumen dengan nilai created_by yang sesuai
        $dokumen = Dokumen::create([
            'judul_dokumen' => $validatedData['judul_dokumen'],
            'deskripsi_dokumen' => $validatedData['deskripsi_dokumen'],
            'kategori_dokumen' => $validatedData['kategori_dokumen'],
            'validasi_dokumen' => $validatedData['validasi_dokumen'],
            'tahun_dokumen' => $validatedData['tahun_dokumen'],
            'dokumen_file' => $fileName,
            'tags' => $validatedData['tags'] ?? null,
            'status' => 'active',
            'created_by' => $validatedData['created_by'] ?? $user->name, // Menggunakan nama pengguna yang sedang login jika tidak ada created_by
        ]);

        return redirect()->route('list-dokumen')->with('success', 'Dokumen berhasil ditambahkan!');
    }

    public function listDokumen()
    {
        $documents = Dokumen::where('status', '!=', 'draft')->get();
        return view('list-dokumen', ['documents' => $documents]);
    }

    public function processList(Request $request)
    {
        return redirect()->route('list-dokumen')->with('success', 'Data berhasil diproses.');
    }

    public function edit($id)
    {
        $document = Dokumen::findOrFail($id);
        return view('edit', compact('document'));
    }

    public function update(Request $request, $id)
    {
        $document = Dokumen::findOrFail($id);
        History::create([
            'dokumen_id' => $document->id,
            'judul_dokumen' => $document->judul_dokumen,
            'deskripsi_dokumen' => $document->deskripsi_dokumen,
            'kategori_dokumen' => $document->kategori_dokumen,
            'validasi_dokumen' => $document->validasi_dokumen,
            'tahun_dokumen' => $document->tahun_dokumen,
            'dokumen_file' => $document->dokumen_file,
            'tags' => $document->tags,
        ]);

        $validatedData = $request->validate([
            'judul_dokumen' => 'required|string|max:255',
            'deskripsi_dokumen' => 'required|string',
            'kategori_dokumen' => 'required|string',
            'validasi_dokumen' => 'required|string',
            'tahun_dokumen' => 'required|int',
            'dokumen_file' => 'nullable|file|mimes:pdf,docx,jpeg,png,jpg|max:2048',
            'tags' => 'nullable|string',
            'created_by' => 'nullable|string',
        ]);

        if ($request->hasFile('dokumen_file')) {
            if ($document->dokumen_file) {
                Storage::disk('public')->delete('documents/' . $document->dokumen_file);
            }
            $fileName = $request->dokumen_file->getClientOriginalName();
            $request->dokumen_file->storeAs('public/documents', $fileName);
            $document->dokumen_file = $fileName;
        }

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $document->judul_dokumen = $validatedData['judul_dokumen'];
        $document->deskripsi_dokumen = $validatedData['deskripsi_dokumen'];
        $document->kategori_dokumen = $validatedData['kategori_dokumen'];
        $document->validasi_dokumen = $validatedData['validasi_dokumen'];
        $document->tahun_dokumen = $validatedData['tahun_dokumen'];
        $document->tags = $validatedData['tags'] ?? null;
        $document->created_by = $validatedData['created_by'] ?? $user->name;
        $document->save();

        return redirect()->route('list-dokumen')->with('success', 'Details dokumen berhasil diperbarui.');
    }

    public function moveToDraft($id)
    {
        $document = Dokumen::findOrFail($id);

        Log::info('Menghapus dokumen dengan ID: ' . $id, ['document' => $document]);

        $user = Auth::user();
        Log::info('User info:', ['user' => $user]);

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $draft = Draft::create([
            'judul_dokumen' => $document->judul_dokumen,
            'deskripsi_dokumen' => $document->deskripsi_dokumen,
            'kategori_dokumen' => $document->kategori_dokumen,
            'validasi_dokumen' => $document->validasi_dokumen,
            'tahun_dokumen' => $document->tahun_dokumen,
            'dokumen_file' => $document->dokumen_file,
            'tags' => $document->tags,
            'status' => 'draft',
            'created_by' => $document->created_by,
        ]);

        Log::info('Dokumen dipindahkan ke draft', ['draft' => $draft]);

        $document->delete();

        Log::info('Dokumen dihapus dari tabel dokumens', ['document' => $document]);

        return redirect()->route('list-dokumen')->with('success', 'Dokumen berhasil dihapus');
    }

    public function getUserName()
    {
        $user = Auth::user();
        return response()->json(['name' => $user->name]);
    }

    public function history($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        $histories = $dokumen->histories()->orderBy('created_at', 'desc')->get();

        return view('history', compact('dokumen', 'histories'));
    }

}
