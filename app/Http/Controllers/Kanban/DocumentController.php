<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Models\ProjectAssetKanban;
use App\Models\AssetDocumentKanban;
use App\Services\KanbanNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    // Upload multiple documents for an asset
    public function store(Request $request, ProjectAssetKanban $asset)
    {
        $validated = $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'file|max:20480', // 20MB max per file
            'stage' => 'nullable|integer|min:1|max:13',
            'description' => 'nullable|string|max:500',
        ], [
            'files.required' => 'Pilih minimal 1 file untuk diupload.',
            'files.*.max' => 'Ukuran file maksimal 20MB per file.',
        ]);

        $stage = $validated['stage'] ?? $asset->current_stage;
        $description = isset($validated['description']) ? trim(strip_tags($validated['description'])) : null;

        $uploadedDocs = [];
        $errors = [];

        foreach ($request->file('files') as $file) {
            $fileName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $fileSize = $file->getSize();

            // Sanitize filename
            $fileName = basename($fileName);
            $safeFileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);

            // Check file type
            if (!in_array($extension, AssetDocumentKanban::ALLOWED_TYPES)) {
                $errors[] = "{$fileName}: Tipe file tidak diizinkan";
                continue;
            }

            try {
                $document = DB::transaction(function () use ($file, $asset, $stage, $fileName, $extension, $fileSize, $description) {
                    // Store file
                    $path = $file->store("assets/{$asset->id}/stage-{$stage}", 'public');

                    // Create document record
                    return $asset->documents()->create([
                        'uploaded_by' => Auth::id(),
                        'stage' => $stage,
                        'file_name' => $fileName,
                        'file_path' => $path,
                        'file_type' => $extension,
                        'file_size' => $fileSize,
                        'description' => $description,
                    ]);
                });

                $uploadedDocs[] = $document;
            } catch (\Exception $e) {
                $errors[] = "{$fileName}: Gagal upload";
            }
        }

        // Notify admins if any files uploaded
        if (count($uploadedDocs) > 0) {
            $fileNames = collect($uploadedDocs)->pluck('file_name')->join(', ');
            KanbanNotificationService::notifyDocumentUploaded($asset, count($uploadedDocs) . ' file', Auth::user());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => count($uploadedDocs) > 0,
                'message' => count($uploadedDocs) . ' file berhasil diupload' . (count($errors) > 0 ? ', ' . count($errors) . ' gagal' : ''),
                'documents' => $uploadedDocs,
                'errors' => $errors,
            ]);
        }

        $message = count($uploadedDocs) . ' file berhasil diupload.';
        if (count($errors) > 0) {
            $message .= ' ' . count($errors) . ' file gagal: ' . implode(', ', $errors);
        }

        return back()->with(count($uploadedDocs) > 0 ? 'success' : 'error', $message);
    }

    // Download document
    public function download(AssetDocumentKanban $document)
    {
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $filePath = Storage::disk('public')->path($document->file_path);
        return response()->download($filePath, $document->file_name);
    }

    // Delete document
    public function destroy(AssetDocumentKanban $document)
    {
        // Check authorization - admin or uploader can delete
        $user = Auth::user();
        if (!$user->is_admin && $document->uploaded_by !== $user->id) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus file ini.');
        }

        $asset = $document->asset;
        $fileName = $document->file_name;
        
        $document->delete(); // Will also delete file from storage via model boot

        // Log activity
        $asset->notes()->create([
            'user_id' => Auth::id(),
            'stage' => $asset->current_stage,
            'type' => 'note',
            'content' => "Hapus file: {$fileName}",
        ]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'File berhasil dihapus.']);
        }

        return back()->with('success', 'File berhasil dihapus.');
    }

    // Get documents by stage
    public function byStage(ProjectAssetKanban $asset, int $stage)
    {
        $documents = $asset->documents()
            ->where('stage', $stage)
            ->with('uploader')
            ->latest()
            ->get();

        return response()->json($documents);
    }

    // Get all documents for an asset
    public function index(ProjectAssetKanban $asset)
    {
        $documents = $asset->documents()
            ->with('uploader')
            ->latest()
            ->get()
            ->groupBy('stage');

        return response()->json($documents);
    }
}
