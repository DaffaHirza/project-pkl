<?php

namespace App\Http\Controllers\Appraisal;

use App\Http\Controllers\Controller;
use App\Models\ProjectKanban;
use App\Models\ProjectAsset;
use App\Models\DocumentKanban;
use App\Models\ActivityKanban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentKanbanController extends Controller
{
    /**
     * Display a listing of documents
     */
    public function index(Request $request)
    {
        $query = DocumentKanban::with(['project.client', 'asset', 'uploader']);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('asset_id')) {
            $query->where('project_asset_id', $request->asset_id);
        }

        if ($request->filled('level')) {
            if ($request->level === 'project') {
                $query->projectLevel();
            } else {
                $query->assetLevel();
            }
        }

        if ($request->filled('search')) {
            $query->where('file_name', 'like', "%{$request->search}%");
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = DocumentKanban::CATEGORIES;
        $projects = ProjectKanban::with('client')->ongoing()->orderBy('name')->get();

        return view('appraisal.documents.index', compact('documents', 'categories', 'projects'));
    }

    /**
     * Show the form for uploading documents to PROJECT (admin/global)
     */
    public function create(ProjectKanban $project)
    {
        $categories = DocumentKanban::CATEGORIES;
        return view('appraisal.documents.create', compact('project', 'categories'));
    }

    /**
     * Store newly uploaded documents to PROJECT
     */
    public function store(Request $request, ProjectKanban $project)
    {
        $validated = $request->validate([
            'category' => 'required|in:' . implode(',', array_keys(DocumentKanban::CATEGORIES)),
            'files' => 'required|array|min:1',
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:20480',
            'description' => 'nullable|string',
        ]);

        $uploadedCount = 0;

        foreach ($request->file('files') as $file) {
            $path = $file->store("documents/{$project->id}/{$validated['category']}", 'public');

            DocumentKanban::create([
                'project_id' => $project->id,
                'project_asset_id' => null, // Project level
                'uploader_id' => $request->user()->id,
                'category' => $validated['category'],
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => $file->getSize(),
                'description' => $validated['description'],
            ]);

            $uploadedCount++;
        }

        // Log activity
        ActivityKanban::logUpload(
            $project,
            $request->user(),
            $uploadedCount . ' file ' . DocumentKanban::CATEGORIES[$validated['category']]
        );

        return redirect()
            ->route('appraisal.projects.show', $project)
            ->with('success', "{$uploadedCount} dokumen berhasil diupload.");
    }

    /**
     * Show the form for uploading documents to ASSET (technical/per-object)
     */
    public function createForAsset(ProjectAsset $asset)
    {
        $asset->load('project.client');
        $categories = DocumentKanban::CATEGORIES;
        return view('appraisal.documents.create-for-asset', compact('asset', 'categories'));
    }

    /**
     * Store newly uploaded documents to ASSET
     */
    public function storeForAsset(Request $request, ProjectAsset $asset)
    {
        $validated = $request->validate([
            'category' => 'required|in:' . implode(',', array_keys(DocumentKanban::CATEGORIES)),
            'files' => 'required|array|min:1',
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:20480',
            'description' => 'nullable|string',
        ]);

        $uploadedCount = 0;

        foreach ($request->file('files') as $file) {
            $path = $file->store("documents/{$asset->project_id}/{$asset->id}/{$validated['category']}", 'public');

            DocumentKanban::create([
                'project_id' => $asset->project_id,
                'project_asset_id' => $asset->id,
                'uploader_id' => $request->user()->id,
                'category' => $validated['category'],
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => $file->getSize(),
                'description' => $validated['description'],
            ]);

            $uploadedCount++;
        }

        // Log activity
        ActivityKanban::create([
            'project_id' => $asset->project_id,
            'project_asset_id' => $asset->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'upload',
            'stage_context' => $asset->current_stage,
            'description' => "{$uploadedCount} file " . DocumentKanban::CATEGORIES[$validated['category']] . " untuk '{$asset->name}' diupload.",
        ]);

        return redirect()
            ->route('appraisal.assets.show', $asset)
            ->with('success', "{$uploadedCount} dokumen berhasil diupload.");
    }

    /**
     * Display the specified document
     */
    public function show(DocumentKanban $document)
    {
        $document->load(['project.client', 'asset', 'uploader']);
        return view('appraisal.documents.show', compact('document'));
    }

    /**
     * Update the specified document
     */
    public function update(Request $request, DocumentKanban $document)
    {
        $validated = $request->validate([
            'category' => 'required|in:' . implode(',', array_keys(DocumentKanban::CATEGORIES)),
            'description' => 'nullable|string',
        ]);

        $document->update($validated);

        // Redirect to appropriate detail page
        if ($document->project_asset_id) {
            return redirect()
                ->route('appraisal.assets.show', $document->asset)
                ->with('success', 'Dokumen berhasil diperbarui.');
        }

        return redirect()
            ->route('appraisal.projects.show', $document->project)
            ->with('success', 'Dokumen berhasil diperbarui.');
    }

    /**
     * Remove the specified document
     */
    public function destroy(Request $request, DocumentKanban $document)
    {
        $project = $document->project;
        $asset = $document->asset;
        $fileName = $document->file_name;

        // Delete file from storage
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        // Log activity
        if ($asset) {
            ActivityKanban::create([
                'project_id' => $project->id,
                'project_asset_id' => $asset->id,
                'user_id' => $request->user()->id,
                'activity_type' => 'comment',
                'stage_context' => $asset->current_stage,
                'description' => "File {$fileName} dihapus dari '{$asset->name}'.",
            ]);

            return redirect()
                ->route('appraisal.assets.show', $asset)
                ->with('success', 'Dokumen berhasil dihapus.');
        }

        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'comment',
            'stage_context' => $project->current_stage,
            'description' => "File {$fileName} dihapus.",
        ]);

        return redirect()
            ->route('appraisal.projects.show', $project)
            ->with('success', 'Dokumen berhasil dihapus.');
    }

    /**
     * Download document
     */
    public function download(DocumentKanban $document)
    {
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $path = Storage::disk('public')->path($document->file_path);
        return response()->download($path, $document->file_name);
    }

    /**
     * Bulk delete documents
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:documents_kanban,id',
        ]);

        $documents = DocumentKanban::whereIn('id', $validated['document_ids'])->get();
        $project = $documents->first()?->project;
        $asset = $documents->first()?->asset;

        foreach ($documents as $document) {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            $document->delete();
        }

        if ($project) {
            ActivityKanban::create([
                'project_id' => $project->id,
                'project_asset_id' => $asset?->id,
                'user_id' => $request->user()->id,
                'activity_type' => 'comment',
                'stage_context' => $asset ? $asset->current_stage : $project->current_stage,
                'description' => count($validated['document_ids']) . ' dokumen dihapus.',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => count($validated['document_ids']) . ' dokumen berhasil dihapus.',
        ]);
    }

    /**
     * Get documents by category for a project (AJAX)
     */
    public function byCategory(ProjectKanban $project, string $category)
    {
        $documents = DocumentKanban::where('project_id', $project->id)
            ->whereNull('project_asset_id') // Only project-level documents
            ->where('category', $category)
            ->with('uploader')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($documents);
    }
}
