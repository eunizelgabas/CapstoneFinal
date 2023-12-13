<?php

namespace App\Http\Controllers;

use App\Events\UserLog;
use App\Models\Dispensing;
use App\Models\Inventory;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DispensingController extends Controller
{
    public function index(Request $request){
        $dispenseCount = Dispensing::count();
        $medicine = Medicine::orderBy('name')->get();

        $dispensesQuery = Dispensing::orderBy('id')
            ->with('medicine'); // Removed the ->get() at the end

        $search = $request->input('search');
        if ($search) {
            $dispensesQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhereHas('medicine', function ($medicineQuery) use ($search) {
                          $medicineQuery->where('name', 'like', '%' . $search . '%');
                      });
            });
        }

        $dispenses = $dispensesQuery->paginate(8)->withQueryString();

        return inertia('Dispense/Sample', [
            'medicine' => $medicine,
            'dispenses' => $dispenses,
            'filters' => $request->only(['search']),
            'dispenseCount' => $dispenseCount
        ]);
    }


    public function store(Request $request)
{
    $fields = $request->validate([
        'name' => 'required',
        'qty' => 'required',
        'med_id' => 'required'
    ]);

    // Find the Medicine record
    $medicine = Medicine::findOrFail($fields['med_id']);

    // Check if stock is sufficient
    if ($medicine->stock < $fields['qty']) {
        // If stock is low, redirect back to the form with an error message
        return redirect('/dispense')->with('error', 'Stock is low. Cannot create Dispensing record.');
    }

    // Stock is sufficient, create Dispensing record
    $des = Dispensing::create($fields);

    // Decrement the stock
    $medicine->decrement('stock', $fields['qty']);

    // Update Inventory record
    $inventory = Inventory::where('med_id', $des->med_id)->first();
    if ($inventory) {
        $inventory->stock_out += $des->qty;
        $inventory->save();
    }

    $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " dispense a ". $des->medicine->name. " with the id# ". $des->id;
    event(new UserLog($log_entry));

    return redirect('/dispense')->with('success', 'Medicine type successfully created');
}

}
