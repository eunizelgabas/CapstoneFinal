<?php

namespace App\Http\Controllers;

use App\Events\UserLog;
use App\Models\MedCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as HttpRequest;

class MedCategoryController extends Controller
{
    public function index(){
        $categoryCount = MedCategory::count();
        return inertia('MedCategory/Sample',[
            'medcategories' => MedCategory::query()
                ->when(HttpRequest::input('search'), function ($query, $search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->withCount('medicine')
                ->orderBy('created_at','desc')
                ->paginate(8)
                ->withQueryString(),
                'filters' => HttpRequest::only(['search']),
                'categoryCount' => $categoryCount

        ]);

    }

    public function store(Request $request) {
        $fields = $request->validate([
            'name'=>'required',
        ]);

      $cat =  MedCategory::create($fields);
        $log_entry = Auth::user()->firstname . "". Auth::user()->lastname . " created a medicine category - " . $cat->name;
        event(new UserLog($log_entry));
        return redirect('/category')->with('success', 'Medicine Category successfully created');
    }

    public function update(Request $request, MedCategory $medcategory){
        $fields = $request->validate([
            'name'=>'required|string',
        ]);


        $medcategory->update($fields);
        $log_entry = Auth::user()->firstname . "". Auth::user()->lastname . " updated medicine category - " . $medcategory->name;
        event(new UserLog($log_entry));
        return redirect('/category')->with('success', "Medicine Category updated successfully");
    }

    public function destroy(MedCategory $medcategory) {


        if(!$medcategory->medicine()->exists()) {
            $medcategory->delete();
            $log_entry = Auth::user()->firstname . "". Auth::user()->lastname . " deleted medicine category - " . $medcategory->name;
        event(new UserLog($log_entry));
            return back()->with('success', 'Medicine Category deleted successfully.');
        } else {
            return back()->with('error', 'Sorry, this medicine category cannot be deleted because it contains existing medicines in the system.');
        }
    }
}
