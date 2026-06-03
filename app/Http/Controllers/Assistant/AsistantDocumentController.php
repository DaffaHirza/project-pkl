<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use App\Models\AssistantDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\AIServices;

class AsistantDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $documentsQuery = AssistantDocument::ownedBy(Auth::id());

        if ($request->filled('q')) {
            $keyword = trim((string) $request->input('q'));

            $documentsQuery->where(function ($query) use ($keyword) {
                $query->where('judul', 'like', '%' . $keyword . '%')
                    ->orWhere('kesimpulan', 'like', '%' . $keyword . '%');
            });
        }


        $documents = $documentsQuery
            ->get();

        return view('assistant.index', compact('documents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('assistant.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, AIServices $aiservices)
    {
        $request->validate([
            'files.laporan_utama' => 'required|mimes:pdf,doc,docx,xlsx,xls,jpg,jpeg,png',
            'files.proposal'      => 'required|mimes:pdf,doc,docx,xlsx,xls,jpg,jpeg,png',
            'files.kertas_kerja'  => 'required|mimes:pdf,doc,docx,xlsx,xls,jpg,jpeg,png',
            'files.resume'        => 'required|mimes:pdf,doc,docx,xlsx,xls,jpg,jpeg,png',
            'files.sertifikat'    => 'required|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        DB::beginTransaction();
        try {
            $fileUtama = $request->file('files.laporan_utama');
            $judulOtomatis = pathinfo($fileUtama->getClientOriginalName(), PATHINFO_FILENAME);

            $document = AssistantDocument::create([
                'user_id' => Auth::id(),
                'judul'   => $judulOtomatis,
                'status'  => 'draft'

            ]);

            foreach ($request->file('files') as $kategori => $file) {
                $path = $file->store('uploads', 'public');
                $document->documentItems()->create([
                    'nama_file' => $file->getClientOriginalName(),
                    'kategori'  => $kategori,
                    'path_file' => $path,
                    'status_verifikasi' => 'pending'
                ]);
            }
            DB::commit();
            $action = $request->input('action');
            if ($action === 'analyze') {
                // Analisis AI bisa lama pada model lokal, jadi lepaskan batas eksekusi request ini.
                set_time_limit(0);
                $aiservices->prosesDokumen($document);
                $document->refresh();
                $document->load('documentItems');
                return redirect()->route('assistant.create')
                    ->with('hasil_ai', $document)
                    ->with('success', 'Analisis Selesai!');
            } elseif ($action === 'savedraft') {
                return redirect()->route('assistant.index')
                    ->with('success', 'Dokumen berhasil disimpan sebagai Draft.');
            } else {
                return redirect()->route('assistant.index')
                    ->with('error', 'Terjadi kesalahan: Aksi tidak dikenali.');
            }
        } catch (\Exception $e) {
            Log::error('Error Store Document: ' . $e->getMessage());

            // Jika document sudah terbuat tapi AI gagal, kita tetap redirect ke hasil (meski error)
            if (isset($document) && $document->id) {
                return redirect()->route('assistant.create')
                    ->with('hasil_ai', $document)
                    ->with('error', 'Analisis terhenti: ' . $e->getMessage());
            }
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan dokumen.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AssistantDocument $assistantDocument)
    {
        $this->abortIfNotOwner($assistantDocument);

        $document = $assistantDocument->load('documentItems');
        session()->now('hasil_ai', $document);

        return view('assistant.create', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssistantDocument $assistantDocument)
    {
        $this->abortIfNotOwner($assistantDocument);

        $document = $assistantDocument->load('documentItems');
        session()->now('hasil_ai', $document);

        return view('assistant.create', compact('document'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssistantDocument $assistantDocument)
    {
        $this->abortIfNotOwner($assistantDocument);

        abort(501, 'Update belum diimplementasikan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssistantDocument $assistantDocument)
    {
        $this->abortIfNotOwner($assistantDocument);

        DB::beginTransaction();

        try {
            $assistantDocument->load('documentItems');

            foreach ($assistantDocument->documentItems as $item) {
                if ($item->path_file) {
                    Storage::disk('public')->delete($item->path_file);
                }
            }

            $assistantDocument->documentItems()->delete();
            $assistantDocument->delete();

            DB::commit();

            return redirect()->route('assistant.index')
                ->with('success', 'Dokumen berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error Delete Document: ' . $e->getMessage());

            return redirect()->route('assistant.index')
                ->with('error', 'Gagal menghapus dokumen.');
        }
    }

    private function abortIfNotOwner(AssistantDocument $assistantDocument): void
    {
        if ($assistantDocument->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}
