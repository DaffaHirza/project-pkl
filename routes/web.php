<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\Appraisal\DashboardController;
use App\Http\Controllers\Appraisal\KanbanClientController;
use App\Http\Controllers\Appraisal\ProjectKanbanController;
use App\Http\Controllers\Appraisal\ProjectAssetController;
use App\Http\Controllers\Appraisal\ProposalKanbanController;
use App\Http\Controllers\Appraisal\ContractKanbanController;
use App\Http\Controllers\Appraisal\InspectionKanbanController;
use App\Http\Controllers\Appraisal\WorkingPaperKanbanController;
use App\Http\Controllers\Appraisal\ReportKanbanController;
use App\Http\Controllers\Appraisal\ApprovalKanbanController;
use App\Http\Controllers\Appraisal\InvoiceKanbanController;
use App\Http\Controllers\Appraisal\DocumentKanbanController;
use App\Http\Controllers\Appraisal\ActivityKanbanController;
use App\Http\Controllers\CardAttachmentController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Kanban Routes
    Route::get('/kanban', [KanbanController::class, 'index'])->name('kanban.index');
    Route::post('/kanban', [KanbanController::class, 'store'])->name('kanban.store');
    Route::get('/kanban/{board}', [KanbanController::class, 'show'])->name('kanban.show');
    Route::patch('/kanban/{board}', [KanbanController::class, 'update'])->name('kanban.update');
    Route::delete('/kanban/{board}', [KanbanController::class, 'destroy'])->name('kanban.destroy');

    // Column Routes
    Route::post('/kanban/{board}/columns', [ColumnController::class, 'store'])->name('columns.store');
    Route::post('/kanban/{board}/columns/reorder', [ColumnController::class, 'reorder'])->name('columns.reorder');
    Route::patch('/columns/{column}', [ColumnController::class, 'update'])->name('columns.update');
    Route::post('/columns/{column}/move', [ColumnController::class, 'move'])->name('columns.move');
    Route::delete('/columns/{column}', [ColumnController::class, 'destroy'])->name('columns.destroy');
    Route::delete('/columns/{column}/force', [ColumnController::class, 'forceDestroy'])->name('columns.forceDestroy');

    // Card Routes
    Route::post('/columns/{column}/cards', [CardController::class, 'store'])->name('cards.store');
    Route::post('/kanban/{board}/cards', [CardController::class, 'storeFromBoard'])->name('cards.storeFromBoard');
    Route::patch('/cards/{card}', [CardController::class, 'update'])->name('cards.update');
    Route::post('/cards/{card}/move', [CardController::class, 'move'])->name('cards.move');
    Route::post('/cards/{card}/assign', [CardController::class, 'assignUsers'])->name('cards.assign');
    Route::delete('/card-assignments/{assignment}', [CardController::class, 'removeUser'])->name('card-assignments.remove');
    Route::delete('/cards/{card}', [CardController::class, 'destroy'])->name('cards.destroy');

    // Card Attachment Routes
    Route::get('/cards/{card}/attachments', [CardAttachmentController::class, 'index'])->name('attachments.index');
    Route::post('/cards/{card}/attachments', [CardAttachmentController::class, 'store'])->name('attachments.store');
    Route::post('/cards/{card}/attachments/multiple', [CardAttachmentController::class, 'storeMultiple'])->name('attachments.store-multiple');
    Route::get('/attachments/{attachment}', [CardAttachmentController::class, 'show'])->name('attachments.show');
    Route::get('/attachments/{attachment}/download', [CardAttachmentController::class, 'download'])->name('attachments.download');
    Route::delete('/attachments/{attachment}', [CardAttachmentController::class, 'destroy'])->name('attachments.destroy');
    Route::post('/cards/{card}/attachments/bulk-delete', [CardAttachmentController::class, 'bulkDestroy'])->name('attachments.bulk-destroy');
    Route::get('/attachments/config', [CardAttachmentController::class, 'config'])->name('attachments.config');

    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/{notification}/unread', [NotificationController::class, 'markAsUnread'])->name('notifications.mark-unread');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications/read/all', [NotificationController::class, 'destroyAllRead'])->name('notifications.destroy-all-read');
    Route::delete('/notifications/all', [NotificationController::class, 'destroyAll'])->name('notifications.destroy-all');
    Route::get('/notifications/settings', [NotificationController::class, 'settings'])->name('notifications.settings');
    Route::post('/notifications/settings', [NotificationController::class, 'updateSettings'])->name('notifications.update-settings');

    Route::get('/assistant', [AssistantController::class, 'index'])->name('assistant.index');

    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');

    // ============================================
    // APPRAISAL KANBAN ROUTES
    // ============================================
    Route::prefix('appraisal')->name('appraisal.')->group(function () {
        
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
        Route::get('/dashboard/needs-attention', [DashboardController::class, 'needsAttention'])->name('dashboard.needs-attention');
        Route::get('/dashboard/workflow-summary', [DashboardController::class, 'workflowSummary'])->name('dashboard.workflow-summary');

        // Clients
        Route::get('/clients/search', [KanbanClientController::class, 'search'])->name('clients.search');
        Route::resource('clients', KanbanClientController::class);

        // Projects (Admin/Contract Level)
        Route::get('/projects/list', [ProjectKanbanController::class, 'list'])->name('projects.list');
        Route::get('/projects/statistics', [ProjectKanbanController::class, 'statistics'])->name('projects.statistics');
        Route::post('/projects/{project}/move-stage', [ProjectKanbanController::class, 'moveStage'])->name('projects.move-stage');
        Route::post('/projects/{project}/update-priority', [ProjectKanbanController::class, 'updatePriority'])->name('projects.update-priority');
        Route::post('/projects/{project}/update-global-status', [ProjectKanbanController::class, 'updateGlobalStatus'])->name('projects.update-global-status');
        Route::post('/projects/{project}/restore', [ProjectKanbanController::class, 'restore'])->name('projects.restore');
        Route::resource('projects', ProjectKanbanController::class);

        // Project Assets (Technical/Per-Object Level) - NEW!
        Route::get('/assets/list', [ProjectAssetController::class, 'list'])->name('assets.list');
        Route::get('/assets/statistics', [ProjectAssetController::class, 'statistics'])->name('assets.statistics');
        Route::post('/assets/bulk', [ProjectAssetController::class, 'bulkStore'])->name('assets.bulk-store');
        Route::post('/assets/{asset}/move-stage', [ProjectAssetController::class, 'moveStage'])->name('assets.move-stage');
        Route::post('/assets/{asset}/update-priority', [ProjectAssetController::class, 'updatePriority'])->name('assets.update-priority');
        Route::post('/assets/{asset}/restore', [ProjectAssetController::class, 'restore'])->name('assets.restore');
        Route::resource('assets', ProjectAssetController::class);

        // Inspections (NOW nested under ASSETS, not projects)
        Route::get('/inspections/today', [InspectionKanbanController::class, 'today'])->name('inspections.today');
        Route::get('/assets/{asset}/inspections/create', [InspectionKanbanController::class, 'create'])->name('inspections.create');
        Route::post('/assets/{asset}/inspections', [InspectionKanbanController::class, 'store'])->name('inspections.store');
        Route::get('/inspections', [InspectionKanbanController::class, 'index'])->name('inspections.index');
        Route::get('/inspections/{inspection}', [InspectionKanbanController::class, 'show'])->name('inspections.show');
        Route::put('/inspections/{inspection}', [InspectionKanbanController::class, 'update'])->name('inspections.update');
        Route::post('/inspections/{inspection}/complete', [InspectionKanbanController::class, 'complete'])->name('inspections.complete');
        Route::patch('/inspections/{inspection}/location', [InspectionKanbanController::class, 'updateLocation'])->name('inspections.update-location');
        Route::delete('/inspections/{inspection}', [InspectionKanbanController::class, 'destroy'])->name('inspections.destroy');

        // Working Papers (NOW nested under ASSETS, not projects)
        Route::get('/assets/{asset}/working-papers/create', [WorkingPaperKanbanController::class, 'create'])->name('working-papers.create');
        Route::post('/assets/{asset}/working-papers', [WorkingPaperKanbanController::class, 'store'])->name('working-papers.store');
        Route::get('/working-papers', [WorkingPaperKanbanController::class, 'index'])->name('working-papers.index');
        Route::put('/working-papers/{workingPaper}', [WorkingPaperKanbanController::class, 'update'])->name('working-papers.update');
        Route::post('/working-papers/{workingPaper}/complete', [WorkingPaperKanbanController::class, 'complete'])->name('working-papers.complete');
        Route::delete('/working-papers/{workingPaper}', [WorkingPaperKanbanController::class, 'destroy'])->name('working-papers.destroy');

        // Reports (NOW nested under ASSETS, not projects)
        Route::get('/assets/{asset}/reports/create', [ReportKanbanController::class, 'create'])->name('reports.create');
        Route::post('/assets/{asset}/reports', [ReportKanbanController::class, 'store'])->name('reports.store');
        Route::get('/reports', [ReportKanbanController::class, 'index'])->name('reports.index');
        Route::get('/reports/{report}', [ReportKanbanController::class, 'show'])->name('reports.show');
        Route::post('/reports/{report}/upload-version', [ReportKanbanController::class, 'uploadVersion'])->name('reports.upload-version');
        Route::post('/reports/{report}/approve', [ReportKanbanController::class, 'approve'])->name('reports.approve');
        Route::post('/reports/{report}/request-revision', [ReportKanbanController::class, 'requestRevision'])->name('reports.request-revision');
        Route::delete('/reports/{report}', [ReportKanbanController::class, 'destroy'])->name('reports.destroy');
        Route::get('/reports/{report}/download', [ReportKanbanController::class, 'download'])->name('reports.download');

        // Proposals (nested under PROJECTS - Global/Administrative)
        Route::get('/projects/{project}/proposals/create', [ProposalKanbanController::class, 'create'])->name('proposals.create');
        Route::post('/projects/{project}/proposals', [ProposalKanbanController::class, 'store'])->name('proposals.store');
        Route::get('/proposals', [ProposalKanbanController::class, 'index'])->name('proposals.index');
        Route::get('/proposals/{proposal}', [ProposalKanbanController::class, 'show'])->name('proposals.show');
        Route::put('/proposals/{proposal}', [ProposalKanbanController::class, 'update'])->name('proposals.update');
        Route::patch('/proposals/{proposal}/status', [ProposalKanbanController::class, 'updateStatus'])->name('proposals.update-status');
        Route::delete('/proposals/{proposal}', [ProposalKanbanController::class, 'destroy'])->name('proposals.destroy');

        // Contracts (nested under PROJECTS - Global/Administrative)
        Route::get('/projects/{project}/contracts/create', [ContractKanbanController::class, 'create'])->name('contracts.create');
        Route::post('/projects/{project}/contracts', [ContractKanbanController::class, 'store'])->name('contracts.store');
        Route::get('/contracts', [ContractKanbanController::class, 'index'])->name('contracts.index');
        Route::get('/contracts/{contract}', [ContractKanbanController::class, 'show'])->name('contracts.show');
        Route::put('/contracts/{contract}', [ContractKanbanController::class, 'update'])->name('contracts.update');
        Route::delete('/contracts/{contract}', [ContractKanbanController::class, 'destroy'])->name('contracts.destroy');
        Route::get('/contracts/{contract}/download', [ContractKanbanController::class, 'download'])->name('contracts.download');

        // Approvals - PROJECT LEVEL (Administrative workflow)
        Route::get('/approvals', [ApprovalKanbanController::class, 'index'])->name('approvals.index');
        Route::get('/approvals/{approval}', [ApprovalKanbanController::class, 'show'])->name('approvals.show');
        Route::get('/approvals/pending/count', [ApprovalKanbanController::class, 'pendingCount'])->name('approvals.pending-count');
        Route::post('/projects/{project}/approvals/proposal', [ApprovalKanbanController::class, 'storeProposalApproval'])->name('approvals.proposal');
        Route::post('/projects/{project}/approvals/contract', [ApprovalKanbanController::class, 'storeContractApproval'])->name('approvals.contract');
        Route::post('/projects/{project}/approvals/invoice', [ApprovalKanbanController::class, 'storeInvoiceApproval'])->name('approvals.invoice');

        // Approvals - ASSET LEVEL (Technical workflow)
        Route::post('/assets/{asset}/approvals/internal-review', [ApprovalKanbanController::class, 'storeAssetInternalReview'])->name('approvals.asset-internal-review');
        Route::post('/assets/{asset}/approvals/client-approval', [ApprovalKanbanController::class, 'storeAssetClientApproval'])->name('approvals.asset-client-approval');

        // Invoices
        Route::get('/invoices/overdue', [InvoiceKanbanController::class, 'overdue'])->name('invoices.overdue');
        Route::get('/projects/{project}/invoices/create', [InvoiceKanbanController::class, 'create'])->name('invoices.create');
        Route::post('/projects/{project}/invoices', [InvoiceKanbanController::class, 'store'])->name('invoices.store');
        Route::get('/invoices', [InvoiceKanbanController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [InvoiceKanbanController::class, 'show'])->name('invoices.show');
        Route::put('/invoices/{invoice}', [InvoiceKanbanController::class, 'update'])->name('invoices.update');
        Route::post('/invoices/{invoice}/mark-paid', [InvoiceKanbanController::class, 'markAsPaid'])->name('invoices.mark-paid');
        Route::post('/invoices/{invoice}/cancel', [InvoiceKanbanController::class, 'cancel'])->name('invoices.cancel');
        Route::delete('/invoices/{invoice}', [InvoiceKanbanController::class, 'destroy'])->name('invoices.destroy');

        // Documents - Support both PROJECT and ASSET level
        Route::get('/projects/{project}/documents/create', [DocumentKanbanController::class, 'create'])->name('documents.create');
        Route::post('/projects/{project}/documents', [DocumentKanbanController::class, 'store'])->name('documents.store');
        Route::get('/projects/{project}/documents/category/{category}', [DocumentKanbanController::class, 'byCategory'])->name('documents.by-category');
        Route::get('/assets/{asset}/documents/create', [DocumentKanbanController::class, 'createForAsset'])->name('documents.create-for-asset');
        Route::post('/assets/{asset}/documents', [DocumentKanbanController::class, 'storeForAsset'])->name('documents.store-for-asset');
        Route::get('/documents', [DocumentKanbanController::class, 'index'])->name('documents.index');
        Route::get('/documents/{document}', [DocumentKanbanController::class, 'show'])->name('documents.show');
        Route::put('/documents/{document}', [DocumentKanbanController::class, 'update'])->name('documents.update');
        Route::delete('/documents/{document}', [DocumentKanbanController::class, 'destroy'])->name('documents.destroy');
        Route::get('/documents/{document}/download', [DocumentKanbanController::class, 'download'])->name('documents.download');
        Route::post('/documents/bulk-delete', [DocumentKanbanController::class, 'bulkDelete'])->name('documents.bulk-delete');

        // Activities - Support both PROJECT and ASSET level
        Route::get('/activities', [ActivityKanbanController::class, 'index'])->name('activities.index');
        Route::get('/activities/recent', [ActivityKanbanController::class, 'recent'])->name('activities.recent');
        Route::get('/activities/statistics', [ActivityKanbanController::class, 'statistics'])->name('activities.statistics');
        Route::post('/projects/{project}/activities/comment', [ActivityKanbanController::class, 'storeComment'])->name('activities.comment');
        Route::post('/projects/{project}/activities/obstacle', [ActivityKanbanController::class, 'storeObstacle'])->name('activities.obstacle');
        Route::post('/projects/{project}/activities/resolve-obstacle', [ActivityKanbanController::class, 'resolveObstacle'])->name('activities.resolve-obstacle');
        Route::get('/projects/{project}/activities', [ActivityKanbanController::class, 'projectActivities'])->name('activities.project');
        Route::post('/assets/{asset}/activities/comment', [ActivityKanbanController::class, 'storeAssetComment'])->name('activities.asset-comment');
        Route::get('/assets/{asset}/activities', [ActivityKanbanController::class, 'assetActivities'])->name('activities.asset');
        Route::delete('/activities/{activity}', [ActivityKanbanController::class, 'destroy'])->name('activities.destroy');
    });
});

require __DIR__.'/auth.php';
