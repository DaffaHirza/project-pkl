<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Models\AssetDocumentKanban;
use App\Models\AssetKanban;
use App\Services\GoogleDriveService;
use App\Services\KanbanNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    private function resolveDriveFileId(AssetDocumentKanban $document): ?string
    {
        if ($document->drive_file_id) {
            return $document->drive_file_id;
        }

        $looksLikeDriveId = $document->file_path
            && !str_contains($document->file_path, '/')
            && strlen($document->file_path) >= 20;

        if ($document->storage_disk === 'google_drive' || $looksLikeDriveId) {
            return $document->file_path;
        }

        return null;
    }

    private function cleanupMissingDriveDocument(AssetDocumentKanban $document): void
    {
        $document->delete();
    }

    public function store(Request $request, AssetKanban $asset, GoogleDriveService $googleDrive)
    {
        $validated = $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'file|max:102400',
            'stage' => 'nullable|integer|min:1|max:13',
            'description' => 'nullable|string|max:500',
        ], [
            'files.required' => 'Pilih minimal 1 file untuk diupload.',
            'files.*.max' => 'Ukuran file maksimal 100MB per file.',
        ]);

        $stage = $validated['stage'] ?? $asset->current_stage;
        $description = isset($validated['description'])
            ? trim(strip_tags($validated['description']))
            : null;

        $uploadedDocs = [];
        $errors = [];

        foreach ($request->file('files') as $file) {
            $originalFileName = basename($file->getClientOriginalName());
            $extension = strtolower($file->getClientOriginalExtension());
            $fileSize = $file->getSize();

            if (!in_array($extension, AssetDocumentKanban::ALLOWED_TYPES)) {
                $errors[] = "{$originalFileName}: Tipe file tidak diizinkan";
                continue;
            }

            try {
                $safeFileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalFileName);

                $driveFileName = now()->format('Ymd_His')
                    . "_asset_{$asset->id}_"
                    . $safeFileName;

                $rootFolderId = config('services.google_drive.folder_id');

                $assetFolderName = sprintf(
                    'OP-%04d - %s',
                    $asset->id,
                    $asset->name
                );

                $assetFolderId = $googleDrive->findOrCreateFolder($assetFolderName, $rootFolderId);

                $driveFile = $googleDrive->upload($file, $driveFileName, $assetFolderId);

                $document = DB::transaction(function () use (
                    $asset,
                    $stage,
                    $originalFileName,
                    $extension,
                    $fileSize,
                    $description,
                    $driveFile
                ) {
                    return $asset->documents()->create([
                        'uploaded_by' => Auth::id(),
                        'stage' => $stage,
                        'file_name' => $originalFileName,
                        'file_path' => $driveFile['id'],
                        'file_type' => $extension,
                        'file_size' => $fileSize,
                        'description' => $description,
                        'storage_disk' => 'google_drive',
                        'drive_file_id' => $driveFile['id'],
                        'drive_web_view_link' => $driveFile['web_view_link'],
                        'drive_web_content_link' => $driveFile['web_content_link'],
                    ]);
                });

                $uploadedDocs[] = $document;
            } catch (\Throwable $e) {
                Log::error('Gagal upload dokumen ke Google Drive', [
                    'asset_id' => $asset->id,
                    'file_name' => $originalFileName,
                    'error' => $e->getMessage(),
                ]);

                $errors[] = "{$originalFileName}: Gagal upload ke Google Drive";
            }
        }

        if (count($uploadedDocs) > 0) {
            KanbanNotificationService::notifyDocumentUploaded(
                $asset,
                count($uploadedDocs) . ' file',
                Auth::user()
            );
        }

        $message = count($uploadedDocs) . ' file berhasil diupload ke Google Drive.';

        if (count($errors) > 0) {
            $message .= ' ' . count($errors) . ' file gagal: ' . implode(', ', $errors);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => count($uploadedDocs) > 0,
                'message' => $message,
                'documents' => $uploadedDocs,
                'errors' => $errors,
            ]);
        }

        return redirect()
            ->route('kanban.assets.show', $asset)
            ->with(count($uploadedDocs) > 0 ? 'success' : 'error', $message);
    }

    public function preview(AssetDocumentKanban $document, GoogleDriveService $googleDrive)
    {
        $driveFileId = $this->resolveDriveFileId($document);

        if ($driveFileId) {
            $previewUrl = $googleDrive->previewUrl($driveFileId);

            if (!$previewUrl) {
                $asset = $document->asset;

                $this->cleanupMissingDriveDocument($document);

                return redirect()
                    ->route('kanban.assets.show', $asset)
                    ->with('error', 'File sudah tidak ada di Google Drive. Data dokumen sudah dibersihkan dari sistem.');
            }

            return redirect()->away($previewUrl);
        }

        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return redirect(Storage::url($document->file_path));
    }

    public function download(AssetDocumentKanban $document, GoogleDriveService $googleDrive)
    {
        $driveFileId = $this->resolveDriveFileId($document);

        if ($driveFileId) {
            if (!$googleDrive->fileExists($driveFileId)) {
                $asset = $document->asset;

                $this->cleanupMissingDriveDocument($document);

                return redirect()
                    ->route('kanban.assets.show', $asset)
                    ->with('error', 'File sudah tidak ada di Google Drive. Data dokumen sudah dibersihkan dari sistem.');
            }

            try {
                $content = $googleDrive->download($driveFileId);

                return response($content, 200, [
                    'Content-Type' => 'application/octet-stream',
                    'Content-Disposition' => "attachment; filename*=UTF-8''" . rawurlencode($document->file_name),
                ]);
            } catch (\Throwable $e) {
                Log::error('Gagal download dokumen dari Google Drive', [
                    'document_id' => $document->id,
                    'drive_file_id' => $driveFileId,
                    'error' => $e->getMessage(),
                ]);

                abort(404, 'File Google Drive tidak ditemukan atau tidak bisa diakses.');
            }
        }

        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $filePath = Storage::disk('public')->path($document->file_path);

        return response()->download($filePath, $document->file_name);
    }

    public function destroy(AssetDocumentKanban $document, GoogleDriveService $googleDrive)
    {
        $user = Auth::user();

        $isAdmin = method_exists($user, 'hasAdminAccess')
            ? $user->hasAdminAccess()
            : (bool) ($user->is_admin ?? false);

        if (!$isAdmin && $document->uploaded_by !== $user->id) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus file ini.');
        }

        $asset = $document->asset;
        $fileName = $document->file_name;

        try {
            if ($document->storage_disk === 'google_drive' && $document->drive_file_id) {
                $googleDrive->delete($document->drive_file_id);
            } elseif ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $document->delete();

            $asset->notes()->create([
                'user_id' => Auth::id(),
                'stage' => $asset->current_stage,
                'type' => 'note',
                'content' => "Hapus file: {$fileName}",
            ]);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'File berhasil dihapus.',
                ]);
            }

            return back()->with('success', 'File berhasil dihapus.');
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus dokumen', [
                'document_id' => $document->id,
                'file_name' => $fileName,
                'error' => $e->getMessage(),
            ]);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'File gagal dihapus.',
                ], 500);
            }

            return back()->with('error', 'File gagal dihapus.');
        }
    }
}