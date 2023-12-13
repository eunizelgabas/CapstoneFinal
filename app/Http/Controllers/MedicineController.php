<?php

namespace App\Http\Controllers;

use App\Events\UserLog;
use App\Models\Inventory;
use App\Models\MedCategory;
use App\Models\Medicine;
use App\Models\MedType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $medCount = Medicine::count();
        $types = MedType::orderBy('name')->get();
        $categories = MedCategory::orderBy('name')->get();
        $inventory = Inventory::orderBy('created_at', 'asc')->get();

        $selectedCategory = $request->input('category');
        $selectedType = $request->input('type');

        $medicineQuery = Medicine::orderBy('created_at', 'desc')
            ->with('type')
            ->with('category')
            ->with('inventory')
            ->withCount('inventory');

        if ($selectedCategory) {
            $medicineQuery->whereHas('category', function ($query) use ($selectedCategory) {
                $query->where('name', $selectedCategory);
            });
        }

        if ($selectedType) {
            $medicineQuery->whereHas('type', function ($query) use ($selectedType) {
                $query->where('name', $selectedType);
            });
        }

        $search = $request->input('search');
        if ($search) {
            $medicineQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('measurement', 'like', '%' . $search . '%');
            });
        }

        $medicines = $medicineQuery->paginate(8)->withQueryString();

        return inertia('Medicine/Sample', [
            'medicines' => $medicines,
            'types' => $types,
            'categories' => $categories,
            'selectedType' => $selectedType,
            'selectedCategory' => $selectedCategory,
            'inventory' => $inventory,
            'filters' => $request->only(['search']),
            'medCount'=> $medCount
        ]);
    }



    public function store(Request $request) {
        $fields = $request->validate([
            'name'=>'required|string',
            'cat_id'=>'required',
            'type_id' =>'required',
            'measurement' =>'required|string',
            'stock' => 'numeric'
            // 'description' =>'required|string'

        ]);

       $med = Medicine::create($fields);

        $inventory = Inventory::where('med_id', $med->id)->first();
        if($inventory){
            $inventory->stock_in += $med->stock;
            $inventory->save();
        }else{
            $inventory = new Inventory([
                'med_id' => $med->id,
                'stock_in' => $med->stock,
            ]);
            $inventory->save();
        }
        $log_entry = Auth::user()->firstname . "". Auth::user()->lastname . " created a  medicine - " . $med->name;
        event(new UserLog($log_entry));
        return redirect('/medicine')->with('success', 'Medicine successfully created');
    }

    public function update(Request $request, Medicine $medicine){
        $fields = $request->validate([
            'name'=>'required|string',
            'cat_id'=>'required',
            'type_id' =>'required',
            'measurement' =>'required|string',
            'stock' => 'required|numeric'
            // 'description' =>'required|string'
        ]);

        $medicine->update($fields);

        $log_entry = Auth::user()->firstname . "". Auth::user()->lastname . " updated a  medicine - " . $medicine->name;
        event(new UserLog($log_entry));
        return redirect('/medicine')->with('success', "Medicine successfully updated");
    }

    public function destroy(Medicine $medicine) {
        // $medicine->delete();

        // return back();
        if(!$medicine->inventory()->exists()) {
            $medicine->delete();

            return back()->with('success', 'Medicine deleted successfully.');
            $log_entry = Auth::user()->firstname . "". Auth::user()->lastname . " deleted a  medicine - " . $medicine->name;
            event(new UserLog($log_entry));
        } else {
            return back()->with('error', 'Sorry, this medicine  cannot be deleted because it contains existing medicine inventory in the system.');
        }
    }
}
