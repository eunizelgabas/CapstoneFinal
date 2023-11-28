<?php

namespace App\Http\Controllers;

use App\Models\MedType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as HttpRequest;

class MedTypeController extends Controller
{
    public function index(){
        $typeCount = MedType::count();
        return inertia('MedType/Sample',[
            'medtypes' => MedType::query()
            ->when(HttpRequest::input('search'), function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })->withCount('medicine')
            ->paginate(8)
            ->withQueryString(),
            'filters' => HttpRequest::only(['search']),
            'typeCount'=> $typeCount
        ]);
    }



    public function store(Request $request) {
        $fields = $request->validate([
            'name'=>'required',
        ]);

        MedType::create($fields);

        return redirect('/type')->with('success', 'Medicine type successfully created');
    }

    public function update(Request $request, MedType $medtype){
        $fields = $request->validate([
            'name'=>'required|string',
        ]);

        $medtype->update($fields);
        return redirect('/type')->with('success', "Medicine type successfully updated");
    }

    public function destroy(MedType $medtype) {
        if(!$medtype->medicine()->exists()) {
            $medtype->delete();

            return back()->with('success', 'Medicine Type deleted successfully.');
        } else {
            return back()->with('error', 'Sorry, this medicine type cannot be deleted because it contains existing medicines in the system.');
        }
    }
}
