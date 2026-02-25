<?php

namespace App\Providers;

use App\Models\AssetDocumentKanban;
use App\Models\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
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
        // Force HTTPS in production/ngrok
        if (app()->environment('production') || str_contains(config('app.url'), 'ngrok')) {
            URL::forceScheme('https');
        }

        // Explicit model binding for kanban documents
        Route::model('document', AssetDocumentKanban::class);
        
        // Explicit model binding for notifications (uses UUID)
        Route::model('notification', Notification::class);
    }
}
