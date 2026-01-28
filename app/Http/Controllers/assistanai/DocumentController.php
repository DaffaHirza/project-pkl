<?php

namespace App\Http\Controllers\assistanai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Document;
use App\Models\DocumentItems;
use App\Services\GeminiServices;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documents = Document::all();
        return view('assistantai.pages.index', compact('documents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('assistantai.pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, GeminiServices $geminiServices)
    {
        set_time_limit(300);
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

            $document = Document::create([
                'user_id' => auth()->id(),
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
                $geminiServices->prosesDokumen($document);
                $document->refresh();
                $document->load('documentItems');
                return redirect()->route('assistantai.pages.create')
                    ->with('hasil_ai', $document)
                    ->with('success', 'Analisis Selesai!');
            } elseif ($action === 'savedraft') {
                return redirect()->route('assistantai.pages.index')
                    ->with('success', 'Dokumen berhasil disimpan sebagai Draft.');
            } else {
                return redirect()->route('assistantai.pages.index')
                    ->with('error', 'Terjadi kesalahan: Aksi tidak dikenali.');
            }
        } catch (\Exception $e) {
            \Log::error('Error Store Document: ' . $e->getMessage());

            // Jika document sudah terbuat tapi AI gagal, kita tetap redirect ke hasil (meski error)
            if (isset($document) && $document->id) {
                return redirect()->route('assistantai.pages.create')
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
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $document = Document::with('documentItems')->findOrFail($id);
        session()->now('hasil_ai', $document);
        return view('assistantai.pages.create', compact('document'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
