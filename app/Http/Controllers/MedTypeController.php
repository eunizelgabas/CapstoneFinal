<?php

namespace App\Http\Controllers;

use App\Models\MedType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as HttpRequest;

class MedTypeController extends Controller
{
    public function index(){

        return inertia('MedType/Index',[
            'medtypes' => MedType::query()
            ->when(HttpRequest::input('search'), function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })->paginate(8)
            ->withQueryString(),
            'filters' => HttpRequest::only(['search'])
        ]);
    }



    public function store(Request $request) {
        $fields = $request->validate([
            'name'=>'required',
        ]);

        MedType::create($fields);

        return redirect('/type')->with('message', 'Medicine type successfully created');
    }

    public function update(Request $request, MedType $medtype){
        $fields = $request->validate([
            'name'=>'required|string',
        ]);

        $medtype->update($fields);
        return redirect('/type')->with('message', "You successfully updated the medicine category");
    }

    public function destroy(MedType $medtype) {
        $medtype->delete();

        return back();
    }
}
