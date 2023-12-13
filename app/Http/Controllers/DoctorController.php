<?php

namespace App\Http\Controllers;

use App\Events\UserLog;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\User;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as HttpRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class DoctorController extends Controller
{
    public function index(){
        $doctors = Doctor::with(['user', 'services'])
        ->whereHas('user', function ($userQuery) {
            $userQuery->where('status', 1);
        })
        ->when(HttpRequest::input('search'), function ($query, $search) {
            $query->where('specialization', 'like', '%' . $search . '%')
                ->orWhere('lic_no', 'like', '%' . $search . '%')
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('firstname', 'like', '%' . $search . '%')
                    ->orWhere('lastname', 'like', '%' . $search . '%');

                })
                ->orWhereHas('services', function ($serviceQuery) use ($search) {
                    $serviceQuery->where('name', 'like', '%' . $search . '%');
                });
        })

        ->orderBy('created_at','desc')
        ->paginate(8)
        ->withQueryString();
        $activeDoctorsCount = User::whereHas('doctor', function ($query) {
            $query->where('status', 1);
        })->count();

        return inertia('Doctor/Index',[
            'doctors' =>$doctors,
            'activeDoctorsCount' => $activeDoctorsCount,
            'filters' => HttpRequest::only(['search']),
        ]);
    }

    public function create(){
        $services = Service::all();
        return inertia('Doctor/Create', ['services' => $services]);
    }

    public function store(Request $request)
    {
        $fields= $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'ext_name' => 'nullable|string|max:255',
                'middlename' => 'nullable|string|max:255',
                'gender' => 'required',
                'lic_no' => 'required',
                'contact_no' => 'required|max:11',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'specialization' => 'required|string|max:255',
                'selectedServiceIds' => 'required|array', // Assuming 'service' is an array of selected service IDs.
            ]);

            // Create a new user
            $user = User::create([
                'firstname' => $request->input('firstname'),
                'lastname' => $request->input('lastname'),
                'ext_name' => $request->input('ext_name'),
                'middlename' => $request->input('middlename'),
                'gender' => $request->input('gender'),
                'contact_no' => $request->input('contact_no'),
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password')),
                'type' => 'doctor'
            ]);


            // Create a new doctor associated with the user
            $doctor = new Doctor([
                'specialization' => $request->input('specialization'),
                'lic_no' => $request->input('lic_no'),

            ]);


        // Save the doctor first to get an ID.

            $selectedServiceIds = $request->input('selectedServiceIds');
            // Attach selected services to the doctor
            $user->doctor()->save($doctor);
            $doctor->services()->attach($selectedServiceIds);

            $doctorRole = Role::where('name', 'doctor')->first();
            $user->assignRole($doctorRole);

            $log_entry = Auth::user()->firstname . "". Auth::user()->lastname . " created a Dr. ". $doctor->user->firstname. " " . $doctor->user->lastname. " with the id# ". $appointment->id;
            event(new UserLog($log_entry));
            return redirect('/doctor')->with('success', 'Doctor successfully created');
    }


    public function edit(Doctor $doctor){
        $doctor = Doctor::with('user', 'services')->find($doctor->id);
        $services = Service::all();

        return inertia('Doctor/Edit', ['doctor' => $doctor, 'services' => $services]);
    }

    public function update(Request $request, Doctor $doctor)
    {

        $user = $doctor->user;


        $user->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'ext_name' => $request->ext_name,
            'middlename' => $request->middlename,
            'gender' => $request->gender,
            'contact_no' => $request->contact_no,
            'lic_no' => 'required',
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'type' => 'doctor'
        ]);

        // Update doctor details
        $doctor->update([
            'specialization' => $request->specialization,
            'lic_no' => $request->lic_no
        ]);

        // dd($doctor) and dd($user);
        // Sync the associated services
        $doctor->services()->sync($request->input('selectedServiceIds'));

        $log_entry = Auth::user()->firstname . "". Auth::user()->lastname . " updated  Dr. ". $doctor->user->firstname. " " . $doctor->user->lastname. "'s details with doctor's id# ". $doctor->id;
        event(new UserLog($log_entry));
        return redirect()->route('doctor.index');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();
        return redirect()->route('doctor.index');
    }

    public function show(Doctor $doctor){
        $doctor = Doctor::with(['user', 'services'])->find($doctor->id);
        $serviceCount = $doctor->services->count();
        $recentAppointments = Appointment::with('patient', 'service', 'doctor.user')
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->where('doc_id', $doctor->id)
        ->orderBy('date', 'desc') // Assuming you have an 'appointment_date' column
        ->take(10) // Adjust this number based on the number of recent appointments you want to retrieve
        ->get();

        return inertia ('Doctor/Show', [
            'doctor'=> $doctor,
            'serviceCount'=> $serviceCount,
            'recentAppointments' => $recentAppointments
        ]);
    }

    public function getServices(Doctor $doctor)
{
    // Retrieve the services offered by the specified doctor
    $services = $doctor->services;

    // Return the services as JSON
    return response()->json(['services' => $services]);
}

public function getDoctorServices()
    {
        $user = auth()->user();

        if ($user->hasRole('Doctor')) {
            $doctor = Doctor::where('user_id', $user->id)->with('services')->first();

            if ($doctor) {
                $doctorName = $user->firstname . ' ' . $user->lastname;
                $services = $doctor->services;

                return response()->json([
                    'isDoctor' => true,
                    'doctorName' => $doctorName,
                    'services' => $services,
                ]);
            }
        }

        return response()->json([
            'isDoctor' => false,
            'doctorName' => '',
            'services' => [],
        ]);
    }

    public function doctorPdf(){
        $doctor = Doctor::with('user', 'services')
            ->orderBy('id', 'asc')
            ->whereHas('user', function ($query) {
                $query->where('status', 1);
            })
            ->get();  // Execute the query and get the results

        $pdf = PDF::loadView('pdf.doctorPdf', [
            'doctor' => $doctor
        ]);

        return $pdf->stream();
    }

    public function deactivateDoctor(Doctor $doctor){
        // Assuming there is a 'user' relationship in your Doctor model
        $doctor->user->update(['status' => 0]);
        $log_entry = Auth::user()->firstname . "". Auth::user()->lastname . " deactivated  Dr. ". $doctor->user->firstname. " " . $doctor->user->lastname. "'s account";
        event(new UserLog($log_entry));
        return redirect('/doctor')->with('success', 'Doctor deactivated successfully');
    }


    public function activateDoctor(Doctor $doctor){
        $doctor->user->update(['status' => 1]);
        $log_entry = Auth::user()->firstname . "". Auth::user()->lastname . " activated  Dr. ". $doctor->user->firstname. " " . $doctor->user->lastname. "'s account";
        event(new UserLog($log_entry));
        return redirect('/doctor')->with('success','Doctor activated successfully');
    }
    public function inactive(){
        $doctors = Doctor::with(['user', 'services'])
        ->whereHas('user', function ($userQuery) {
            $userQuery->where('status', 0);
        })
        ->when(HttpRequest::input('search'), function ($query, $search) {
            $query->where('specialization', 'like', '%' . $search . '%')
                ->orWhere('lic_no', 'like', '%' . $search . '%')
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('firstname', 'like', '%' . $search . '%')
                    ->orWhere('lastname', 'like', '%' . $search . '%');
                })
                ->orWhereHas('services', function ($serviceQuery) use ($search) {
                    $serviceQuery->where('name', 'like', '%' . $search . '%');
                });
        })

        ->orderBy('created_at','desc')
        ->paginate(8)
        ->withQueryString();
        $activeDoctorsCount = User::whereHas('doctor', function ($query) {
            $query->where('status', 0);
        })->count();

        return inertia('Doctor/Inactive',[
            'doctors' =>$doctors,
            'activeDoctorsCount' => $activeDoctorsCount,
            'filters' => HttpRequest::only(['search']),
        ]);
    }
}
