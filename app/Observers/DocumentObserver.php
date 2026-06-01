<?php

namespace App\Observers;

use App\Models\Document;
use Illuminate\Support\Facades\DB;

class DocumentObserver
{
    /**
     * Handle the Document "created" event.
     */
    public function created(Document $document): void
    {
        $this->updateUserTokenCount($document);
    }

    /**
     * Handle the Document "updated" event.
     */
    public function updated(Document $document): void
    {
        $this->updateUserTokenCount($document);
    }

    /**
     * Handle the Document "deleted" event.
     */
    public function deleted(Document $document): void
    {
        $this->updateUserTokenCount($document);
    }

    /**
     * Handle the Document "restored" event.
     */
    public function restored(Document $document): void
    {
        $this->updateUserTokenCount($document);
    }

    /**
     * Handle the Document "force deleted" event.
     */
    public function forceDeleted(Document $document): void
    {
        $this->updateUserTokenCount($document);
    }

    /**
     * Update user's total token count based on all their documents
     */
    private function updateUserTokenCount(Document $document): void
    {
        if (!$document->user_id) {
            return;
        }

        $totalToken = $document->user
            ->documents()
            ->selectRaw('SUM(token_input + token_output) as total')
            ->first()
            ?->total ?? 0;

        $document->user->update([
            'jumlah_token' => max(0, (int) $totalToken),
        ]);
    }
}
