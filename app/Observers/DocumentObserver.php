<?php

namespace App\Observers;

use Illuminate\Support\Facades\DB;

class DocumentObserver
{
    /**
     * Handle the Document "created" event.
     */
    public function created($document): void
    {
        $this->updateUserTokenCount($document);
    }

    /**
     * Handle the Document "updated" event.
     */
    public function updated($document): void
    {
        $this->updateUserTokenCount($document);
    }

    /**
     * Handle the Document "deleted" event.
     */
    public function deleted($document): void
    {
        $this->updateUserTokenCount($document);
    }

    /**
     * Handle the Document "restored" event.
     */
    public function restored($document): void
    {
        $this->updateUserTokenCount($document);
    }

    /**
     * Handle the Document "force deleted" event.
     */
    public function forceDeleted($document): void
    {
        $this->updateUserTokenCount($document);
    }

    /**
     * Update user's total token count based on all their documents
     */
    private function updateUserTokenCount($document): void
    {
        if (empty($document->user_id)) {
            return;
        }

        try {
            // Sum token_input + token_output for all documents belonging to this user (Postgres compatible)
            $totalToken = DB::table('documents')
                ->where('user_id', $document->user_id)
                ->selectRaw('SUM(COALESCE(token_input, 0) + COALESCE(token_output, 0)) as total')
                ->value('total') ?? 0;

            // Update the user's total token usage in users table
            DB::table('users')
                ->where('id', $document->user_id)
                ->update([
                    'jumlah_token' => max(0, (int) $totalToken),
                ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Observer token update failed: ' . $e->getMessage());
        }
    }
}
