<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Kanban\DashboardController;
use App\Http\Controllers\Kanban\ClientController;
use App\Http\Controllers\Kanban\ProjectController;
use App\Http\Controllers\Kanban\AssetController;
use App\Http\Controllers\Kanban\DocumentController;
use App\Http\Controllers\Kanban\NoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'));

Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    Route::get('/assistant', [AssistantController::class, 'index'])->name('assistant.index');
    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');

    // ============================================
    // NOTIFICATIONS
    // ============================================
    Route::controller(NotificationController::class)->prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/recent', 'recent')->name('recent');
        Route::get('/unread-count', 'unreadCount')->name('unread-count');
        Route::get('/settings', 'settings')->name('settings');
        Route::post('/settings', 'updateSettings')->name('update-settings');
        Route::post('/mark-all-read', 'markAllAsRead')->name('mark-all-read');
        Route::get('/{notification}/view', 'view')->name('view'); // GET to view & mark read
        Route::post('/{notification}/mark-read', 'markAsRead')->name('mark-read');
        Route::post('/{notification}/mark-unread', 'markAsUnread')->name('mark-unread');
        Route::delete('/{notification}', 'destroy')->name('destroy');
        Route::delete('/bulk/read', 'destroyAllRead')->name('destroy-all-read');
        Route::delete('/bulk/all', 'destroyAll')->name('destroy-all');
    });

    // ============================================
    // KANBAN ROUTES (WITH ROLE-BASED ACCESS)
    // ============================================
    Route::prefix('kanban')->name('kanban.')->group(function () {
        
        // Dashboard - All authenticated users
        Route::controller(DashboardController::class)->group(function () {
            Route::get('/', 'index')->name('dashboard');
            Route::get('/dashboard/data', 'data')->name('dashboard.data');
            Route::get('/activity-log', 'activityLog')->name('activity-log');
        });

        // ----------------------------------------
        // CLIENTS - Admin only for CUD operations
        // ----------------------------------------
        Route::controller(ClientController::class)->prefix('clients')->name('clients.')->group(function () {
            // Read - All users
            Route::get('/', 'index')->name('index');
            Route::get('/search', 'search')->name('search');
            Route::get('/{client}', 'show')->name('show')->whereNumber('client');
            
            // Create/Update/Delete - Admin only
            Route::middleware('admin')->group(function () {
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{client}/edit', 'edit')->name('edit')->whereNumber('client');
                Route::put('/{client}', 'update')->name('update')->whereNumber('client');
                Route::delete('/{client}', 'destroy')->name('destroy')->whereNumber('client');
            });
        });

        // ----------------------------------------
        // PROJECTS - Admin for delete, users for rest
        // ----------------------------------------
        Route::controller(ProjectController::class)->prefix('projects')->name('projects.')->group(function () {
            // Read & Stats - All users
            Route::get('/', 'index')->name('index');
            Route::get('/statistics', 'statistics')->name('statistics');
            Route::get('/{project}', 'show')->name('show')->whereNumber('project');
            
            // Create/Update - All users (can manage their assigned projects)
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{project}/edit', 'edit')->name('edit')->whereNumber('project');
            Route::put('/{project}', 'update')->name('update')->whereNumber('project');
            
            // Delete - Admin only
            Route::delete('/{project}', 'destroy')->name('destroy')
                ->whereNumber('project')
                ->middleware('admin');
        });

        // ----------------------------------------
        // ASSETS - Users can manage, admin for delete
        // ----------------------------------------
        Route::controller(AssetController::class)->prefix('assets')->name('assets.')->group(function () {
            // Read - All users
            Route::get('/', 'index')->name('index');
            Route::get('/board', 'board')->name('board'); // Kanban board view
            Route::get('/{asset}', 'show')->name('show')->whereNumber('asset');
            
            // Create/Update/Operations - All users
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::post('/bulk', 'bulkStore')->name('bulk-store');
            Route::get('/{asset}/edit', 'edit')->name('edit')->whereNumber('asset');
            Route::put('/{asset}', 'update')->name('update')->whereNumber('asset');
            Route::post('/{asset}/move-stage', 'moveStage')->name('move-stage')->whereNumber('asset');
            Route::post('/{asset}/update-position', 'updatePosition')->name('update-position')->whereNumber('asset');
            Route::post('/{asset}/update-priority', 'updatePriority')->name('update-priority')->whereNumber('asset');
            
            // Delete - Admin only
            Route::delete('/{asset}', 'destroy')->name('destroy')
                ->whereNumber('asset')
                ->middleware('admin');
        });

        // ----------------------------------------
        // DOCUMENTS - Users can manage own, admin for all
        // ----------------------------------------
        Route::controller(DocumentController::class)->group(function () {
            Route::prefix('assets/{asset}')->name('documents.')->whereNumber('asset')->group(function () {
                Route::get('/documents', 'index')->name('index');
                Route::get('/documents/stage/{stage}', 'byStage')->name('by-stage')->whereNumber('stage');
                Route::post('/documents', 'store')->name('store');
            });
            Route::get('/documents/{document}/download', 'download')->name('documents.download')->whereNumber('document');
            Route::delete('/documents/{document}', 'destroy')->name('documents.destroy')->whereNumber('document');
        });

        // ----------------------------------------
        // NOTES - Users can manage own
        // ----------------------------------------
        Route::controller(NoteController::class)->group(function () {
            Route::prefix('assets/{asset}')->name('notes.')->whereNumber('asset')->group(function () {
                Route::get('/notes', 'index')->name('index');
                Route::get('/notes/stage/{stage}', 'byStage')->name('by-stage')->whereNumber('stage');
                Route::get('/notes/activity-log', 'activityLog')->name('activity-log');
                Route::post('/notes', 'store')->name('store');
            });
            Route::delete('/notes/{note}', 'destroy')->name('notes.destroy')->whereNumber('note');
        });
    });

    // ============================================
    // ADMIN ONLY ROUTES
    // ============================================
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        // User management - Superuser only
        Route::middleware('role:superuser')->group(function () {
            // Future: user management routes
        });
        
        // Reports & Stats - Admin+
        Route::get('/reports', fn() => view('admin.reports'))->name('reports');
    });
});

require __DIR__.'/auth.php';
