<?php

namespace App\Providers;

use App\Models\AssetDocumentKanban;
use App\Models\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Explicit model binding for kanban documents
        Route::model('document', AssetDocumentKanban::class);
        
        // Explicit model binding for notifications (uses UUID)
        Route::model('notification', Notification::class);
    }
}
