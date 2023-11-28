<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as HttpRequest;

class ServiceController extends Controller
{
    public function index()
    {
        $serviceCount = Service::count();
        // $services = Service::with('doctors')->get();
        $services = Service::query()
        ->when(HttpRequest::input('search'), function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        })
        ->with('doctors')
        ->withCount('doctors')
        ->paginate(8)
        ->withQueryString();
        return inertia('Service/Sample', [
            'services' => $services,
            'serviceCount' => $serviceCount,
            'filters' => HttpRequest::only(['search']),
        ]);
    }

    public function store(Request $request) {
        $fields = $request->validate([
            'name'=>'required',
            // 'doc_id'=>'required',
            'description'=>'required',
        ]);

        Service::create($fields);

        return redirect('/service')->with('success', 'Medicine Service successfully created');
    }

    public function update(Request $request, Service $service){
        $fields = $request->validate([
            'name'=>'required',
            // 'doc_id'=>'required',
            'description'=>'required',
        ]);

        $service->update($fields);
        return redirect('/service')->with('success', "Medical Service successfully updated");
    }

    public function destroy(Service $service) {
        $service->delete();

        return back()->with('success', "Medical Service successfully deleted");
    }
}
