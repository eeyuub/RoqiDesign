<?php

namespace App\Observers;

use App\Models\Supplier;
use Filament\Notifications\Notification;

class SupplierObserver
{
    /**
     * Handle the Supplier "created" event.
     */
    public function created(Supplier $supplier): void
    {
        //
    }

    /**
     * Handle the Supplier "updated" event.
     */
    public function updated(Supplier $supplier): void
    {
        //
    }

    /**
     * Handle the Supplier "deleted" event.
     */
    public function deleted(Supplier $supplier): void
    {
        /*  if ($supplier->Products()->withTrashed()->count() > 0) {
           return true;
            // abort(403, trans('Can not delete Supplier with associated products.'));
        }
        return false; */
    }



    /**
     * Handle the Supplier "restored" event.
     */
    public function restored(Supplier $supplier): void
    {
        //
    }

    /**
     * Handle the Supplier "force deleted" event.
     */
    public function forceDeleted(Supplier $supplier): void
    {

    }
}
