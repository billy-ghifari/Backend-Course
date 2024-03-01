<?php

namespace App\Http\Controllers;

use App\Models\TrashActivity;

class C_trash extends Controller
{
    // Menampilkan semua aktivitas trash
    public function trash()
    {
        $trashActivities = TrashActivity::all();
        return view('trash-activities.trash', compact('trashActivities'));
    }

    // Mengembalikan item dari trash
    public function restore($id)
    {
        // Temukan aktivitas trash berdasarkan ID
        $trashActivity = TrashActivity::findOrFail($id);

        // Lakukan logika untuk mengembalikan item
        $restoredItem = TrashActivity::withTrashed()->findOrFail($trashActivity->model_id);
        $restoredItem->restore();

        // Kemudian hapus aktivitas trash
        $trashActivity->delete();

        return redirect()->route('trash-activities.index')
            ->with('success', 'Item berhasil dikembalikan dari trash.');
    }

    // Menghapus secara permanen item dari trash
    public function destroy($id)
    {
        $trashActivity = TrashActivity::findOrFail($id);
        // Lakukan logika untuk menghapus secara permanen item, jika diperlukan

        // Kemudian hapus aktivitas trash
        $trashActivity->forceDelete();

        return redirect()->route('trash-activities.index')
            ->with('success', 'Item berhasil dihapus secara permanen dari trash.');
    }
}
