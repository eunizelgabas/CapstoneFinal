<?php

namespace App\Http\Controllers;

use App\Events\UserLog;
use App\Models\Inventory;
use App\Models\Medicine;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $purchaseCount = Stock::count();
        $medicine = Medicine::orderBy('name')->get();

        $stockQuery = Stock::orderBy('id')->with('medicine');

        $search = $request->input('search');
        if ($search) {
            $stockQuery->whereHas('medicine', function ($medicineQuery) use ($search) {
                $medicineQuery->where('name', 'like', '%' . $search . '%');
            });
        }

        $stocks = $stockQuery->paginate(8)->withQueryString();

        return inertia('Stock/Sample', [
            'medicine' => $medicine,
            'stocks'   => $stocks,
            'filters'  => $request->only(['search']),
            'purchaseCount' => $purchaseCount
        ]);
    }


    public function create(){
        $medicine = Medicine::orderBy('name')->get();
        return inertia('Inventory/Create',[
            'inventories' => Inventory::orderBy('created_at','asc'),
            'stocks' => Stock::orderBy('created_at','asc')
            ->with('medicine')->get(),
            'medicine' => $medicine,
        ]);
    }

    public function store(Request $request) {
        $fields = $request->validate([
            'med_id'=>'required|numeric',
            'purchase_date' => 'required|date',
            'qty'=>'required',
        ]);

        $stock = Stock::create($fields);

        $medicine = Medicine::findOrFail($fields['med_id']);
        $medicine->increment('stock', $fields['qty']);

        $inventory = Inventory::where('med_id', $stock->med_id)->first();
        if($inventory){
            $inventory->stock_in += $stock->qty;
            $inventory->save();
        }else{
            $inventory = new Inventory([
                'med_id' => $medicine->id,
                'stock_in' => $stock->qty,

            ]);
            $inventory->save();
        }
        $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " created a medicine stock - " . $stock->name;
        event(new UserLog($log_entry));


        return redirect()->route('stock.index')->with('success', 'Stock successfully added');
    }

    // public function update(Request $request, Stock $stock){
    //     $fields = $request->validate([
    //         'med_id'=>'required|numeric',
    //         'qty'=>'required',
    //         'purchase_date' => 'required|date',
    //     ]);

    //     $stock->update($fields);
    //     // $stock = Stock::update($fields);

    //     $medicine = Inventory::where('med_id', $stock->med_id)->firstOrFail();
    //     $medicine->stocks+= $stock->qty;
    //     $medicine->save();


    //     return redirect('/stock.index')->with('success', "Purchase Medicine successfully updated");
    // }
    public function update(Request $request,Stock $stock) {
        $fields = $request->validate([
            'med_id' => 'required|numeric',
            'purchase_date' => 'required|date',
            'qty' => 'required|numeric',
        ]);

        // Find the stock record to update
        $stock = Stock::findOrFail($stock->id);

        // Calculate the difference in quantity
        $qtyDifference = $fields['qty'] - $stock->qty;

        // Update the stock record
        $stock->update($fields);

        // Update the medicine stock
        $medicine = Medicine::findOrFail($fields['med_id']);
        $medicine->increment('stock', $qtyDifference);

        // Update the inventory
        $inventory = Inventory::where('med_id', $stock->med_id)->first();
        if ($inventory) {
            $inventory->stock_in += $qtyDifference;
            $inventory->save();
        } else {
            $inventory = new Inventory([
                'med_id' => $medicine->id,
                'stock_in' => $qtyDifference,
            ]);
            $inventory->save();
        }
        $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " updated a medicine stock - " . $stock->name;
        event(new UserLog($log_entry));

        return redirect()->route('stock.index')->with('success', 'Stock successfully updated');
    }


    public function destroy(Stock $stock) {
        // Find the stock record to delete
        $stock = Stock::findOrFail($stock->id);

        // Decrement the medicine stock
        $medicine = Medicine::findOrFail($stock->med_id);
        $medicine->decrement('stock', $stock->qty);

        // Update the inventory
        $inventory = Inventory::where('med_id', $stock->med_id)->first();
        if ($inventory) {
            $inventory->stock_in -= $stock->qty;
            $inventory->save();
        }

        // Delete the stock record
        $stock->delete();

        return redirect()->route('stock.index')->with('success', 'Stock successfully deleted');
    }
}
