<?php

namespace App\Providers;

use App\Models\AssetDocumentKanban;
use App\Models\AssistantDocument;
use App\Models\Document;
use App\Models\Notification;
use App\Observers\DocumentObserver;
use Illuminate\Support\Facades\Auth;
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

        // Register model observers
        Document::observe(DocumentObserver::class);

        // Explicit model binding for kanban documents
        Route::model('document', AssetDocumentKanban::class);

        // Restrict assistant document binding to authenticated owner only.
        Route::bind('assistantDocument', function ($value) {
            return AssistantDocument::ownedBy(Auth::id())->findOrFail($value);
        });

        // Explicit model binding for notifications (uses UUID)
        Route::model('notification', Notification::class);
    }
}
