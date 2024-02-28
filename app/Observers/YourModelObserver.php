<?php

namespace App\Observers;

use App\Models\TrashActivity;
use Illuminate\Support\Facades\Auth;

class YourModelObserver
{
    /**
     * Handle the YourModel "created" event.
     */
    public function created(TrashActivity $yourModel): void
    {
        //
    }

    /**
     * Handle the YourModel "updated" event.
     */
    public function updated(TrashActivity $yourModel): void
    {
        //
    }

    /**
     * Handle the YourModel "deleted" event.
     */
    public function deleted(TrashActivity $TrashActivity): void
    {
        $trash = new TrashActivity([
            'model_id' => $TrashActivity->id,
            'deleted_by' => Auth::id(),
        ]);

        $trash->save();
    }

    /**
     * Handle the YourModel "restored" event.
     */
    public function restored(TrashActivity $yourModel): void
    {
        //
    }

    /**
     * Handle the YourModel "force deleted" event.
     */
    public function forceDeleted(TrashActivity $yourModel): void
    {
        //
    }
}
