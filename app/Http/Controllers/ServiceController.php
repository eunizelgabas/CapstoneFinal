<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('doctors')->get();
        return inertia('Service/Index', ['services' => $services]);
    }

    public function store(Request $request) {
        $fields = $request->validate([
            'name'=>'required',
            // 'doc_id'=>'required',
            'description'=>'required',
        ]);

        Service::create($fields);

        return redirect('/service')->with('message', 'Medicine type successfully created');
    }

    public function update(Request $request, Service $service){
        $fields = $request->validate([
            'name'=>'required',
            // 'doc_id'=>'required',
            'description'=>'required',
        ]);

        $service->update($fields);
        return redirect('/service')->with('message', "You successfully updated the medicine category");
    }

    public function destroy(Service $service) {
        $service->delete();

        return back();
    }
}
