<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\CardAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CardAttachmentController extends Controller
{
    /**
     * Allowed file types and their max sizes (in bytes)
     */
    protected const ALLOWED_TYPES = [
        // Images
        'image/jpeg' => 10485760,    // 10MB
        'image/png' => 10485760,     // 10MB
        'image/gif' => 10485760,     // 10MB
        'image/webp' => 10485760,    // 10MB
        'image/svg+xml' => 2097152,  // 2MB
        
        // Documents
        'application/pdf' => 52428800,  // 50MB
        'application/msword' => 52428800,
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 52428800,
        'application/vnd.ms-excel' => 52428800,
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 52428800,
        'application/vnd.ms-powerpoint' => 52428800,
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 52428800,
        
        // Archives
        'application/zip' => 104857600,      // 100MB
        'application/x-rar-compressed' => 104857600,
        'application/x-7z-compressed' => 104857600,
        
        // Text
        'text/plain' => 5242880,     // 5MB
        'text/csv' => 10485760,      // 10MB
    ];

    /**
     * Display all attachments for a card
     */
    public function index(Card $card)
    {
        $attachments = $card->attachments()
            ->orderBy('created_at', 'desc')
            ->get();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'attachments' => $attachments->map(fn($a) => [
                    'id' => $a->id,
                    'file_name' => $a->file_name,
                    'file_size' => $a->file_size,
                    'file_size_human' => $a->file_size_human,
                    'mime_type' => $a->mime_type,
                    'extension' => $a->extension,
                    'url' => $a->url,
                    'is_image' => $a->isImage(),
                    'is_pdf' => $a->isPdf(),
                    'created_at' => $a->created_at->toISOString(),
                ]),
            ]);
        }

        return view('kanban.attachments.index', compact('card', 'attachments'));
    }

    /**
     * Upload single file (for FilePond/Dropzone chunk upload)
     */
    public function store(Request $request, Card $card)
    {
        $request->validate([
            'file' => 'required|file|max:102400', // 100MB max
        ]);

        $file = $request->file('file');
        $mimeType = $file->getMimeType();

        // Validate file type
        if (!array_key_exists($mimeType, self::ALLOWED_TYPES)) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe file tidak diizinkan: ' . $mimeType,
            ], 422);
        }

        // Validate file size for specific type
        $maxSize = self::ALLOWED_TYPES[$mimeType];
        if ($file->getSize() > $maxSize) {
            return response()->json([
                'success' => false,
                'message' => 'Ukuran file melebihi batas maksimum untuk tipe ini.',
            ], 422);
        }

        // Generate unique filename
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $uniqueName = Str::uuid() . '.' . $extension;

        // Store file
        $path = $file->storeAs(
            'card-attachments/' . $card->id,
            $uniqueName,
            'public'
        );

        // Create attachment record
        $attachment = $card->attachments()->create([
            'file_name' => $originalName,
            'file_path' => $path,
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'File berhasil diupload!',
            'attachment' => [
                'id' => $attachment->id,
                'file_name' => $attachment->file_name,
                'file_size' => $attachment->file_size,
                'file_size_human' => $attachment->file_size_human,
                'mime_type' => $attachment->mime_type,
                'extension' => $attachment->extension,
                'url' => $attachment->url,
                'is_image' => $attachment->isImage(),
            ],
        ]);
    }

    /**
     * Upload multiple files at once
     */
    public function storeMultiple(Request $request, Card $card)
    {
        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'file|max:102400', // 100MB max per file
        ]);

        $uploaded = [];
        $errors = [];

        foreach ($request->file('files') as $index => $file) {
            $mimeType = $file->getMimeType();
            $originalName = $file->getClientOriginalName();

            // Validate file type
            if (!array_key_exists($mimeType, self::ALLOWED_TYPES)) {
                $errors[] = [
                    'file' => $originalName,
                    'message' => 'Tipe file tidak diizinkan',
                ];
                continue;
            }

            // Validate file size
            $maxSize = self::ALLOWED_TYPES[$mimeType];
            if ($file->getSize() > $maxSize) {
                $errors[] = [
                    'file' => $originalName,
                    'message' => 'Ukuran file melebihi batas',
                ];
                continue;
            }

            // Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $uniqueName = Str::uuid() . '.' . $extension;

            // Store file
            $path = $file->storeAs(
                'card-attachments/' . $card->id,
                $uniqueName,
                'public'
            );

            // Create attachment record
            $attachment = $card->attachments()->create([
                'file_name' => $originalName,
                'file_path' => $path,
                'mime_type' => $mimeType,
                'file_size' => $file->getSize(),
            ]);

            $uploaded[] = [
                'id' => $attachment->id,
                'file_name' => $attachment->file_name,
                'file_size_human' => $attachment->file_size_human,
                'url' => $attachment->url,
                'is_image' => $attachment->isImage(),
            ];
        }

        return response()->json([
            'success' => count($uploaded) > 0,
            'message' => count($uploaded) . ' file berhasil diupload' . 
                        (count($errors) > 0 ? ', ' . count($errors) . ' gagal' : ''),
            'uploaded' => $uploaded,
            'errors' => $errors,
        ]);
    }

    /**
     * Download an attachment
     */
    public function download(CardAttachment $attachment)
    {
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download(
            Storage::disk('public')->path($attachment->file_path),
            $attachment->file_name
        );
    }

    /**
     * Show/preview an attachment (for images and PDFs)
     */
    public function show(CardAttachment $attachment)
    {
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        // For images and PDFs, show inline
        if ($attachment->isImage() || $attachment->isPdf()) {
            return response()->file(
                Storage::disk('public')->path($attachment->file_path),
                ['Content-Type' => $attachment->mime_type]
            );
        }

        // For other files, download
        return $this->download($attachment);
    }

    /**
     * Delete an attachment
     */
    public function destroy(CardAttachment $attachment)
    {
        $fileName = $attachment->file_name;
        
        // File will be deleted from storage via model event
        $attachment->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'File "' . $fileName . '" berhasil dihapus!',
            ]);
        }

        return back()->with('success', 'File berhasil dihapus!');
    }

    /**
     * Bulk delete attachments
     */
    public function bulkDestroy(Request $request, Card $card)
    {
        $validated = $request->validate([
            'attachment_ids' => 'required|array|min:1',
            'attachment_ids.*' => 'exists:card_attachments,id',
        ]);

        $attachments = CardAttachment::whereIn('id', $validated['attachment_ids'])
            ->where('card_id', $card->id)
            ->get();

        $count = $attachments->count();

        foreach ($attachments as $attachment) {
            $attachment->delete();
        }

        return response()->json([
            'success' => true,
            'message' => $count . ' file berhasil dihapus!',
        ]);
    }

    /**
     * Get upload configuration for frontend
     */
    public function config()
    {
        return response()->json([
            'max_file_size' => 104857600, // 100MB
            'max_files' => 50,
            'allowed_types' => array_keys(self::ALLOWED_TYPES),
            'type_limits' => self::ALLOWED_TYPES,
            'chunk_size' => 5242880, // 5MB chunks
        ]);
    }
}
