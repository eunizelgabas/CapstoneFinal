<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(){
        $inventories = Inventory::orderBy('id','desc')
        ->with('medicine')->get();

        $inventories->each(function ($inventory) {
            $inventory->Available = $inventory->stock_out ? ($inventory->stock_in - $inventory->stock_out) : $inventory->stock_in;

            // Check if the medicine relationship is loaded
            if ($inventory->medicine) {
                $inventory->medicine_name = $inventory->medicine->name;
            } else {
                $inventory->medicine_name = null; // or any default value you want
            }
        });

        // Calculate the total available stock by summing the stockAvail attribute
        // $totalAvail =$inventories->diff('Available');
        return inertia('Inventory/Sample',[
            'inventories' => $inventories,


        ]);
    }

    public function getData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Fetch inventory data based on the date range
        $inventory = Inventory::whereBetween('created_at', [$startDate, $endDate])->get();

        return response()->json(['inventory' => $inventory]);
    }

    public function getInventoryByDateRange($startDate, $endDate)
    {
        $quantities = DB::table('medicines')
        ->select(
            'medicines.id',
            'medicines.name',
            DB::raw('SUM(stocks.qty) as stock_qty'),
            DB::raw('SUM(dispensings.qty) as dispense_qty')
        )
        ->leftJoin('stocks', 'medicines.id', '=', 'stocks.med_id')
        ->leftJoin('dispensings', 'medicines.id', '=', 'dispensings.med_id')
        ->whereBetween('stocks.purchase_date', [$startDate, $endDate])
        ->orWhereBetween('dispensings.created_at', [$startDate, $endDate])
        ->groupBy('medicines.id', 'medicines.name')
        ->get();

        dd($quantities);
    return inertia('Inventory/Index', ['quantities' => $quantities]);
    }
}
