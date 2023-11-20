<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('Admin')) {
            $appointments = Appointment::with(['doctor', 'doctor.user', 'patient', 'service'])->orderBy('created_at','desc')->get();
        } elseif ($user->hasRole('Doctor')) {
            $appointments = Appointment::with(['doctor', 'doctor.user', 'patient', 'service'])
                ->whereHas('doctor', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->orderBy('id','desc')->get();
        }  else {
            // Handle other roles or unauthorized access as needed
            return abort(403);
        }
        return inertia('Appointment/Index', [
            'appointments' => $appointments,
        ]);
    }

    public function create(){
        $doctors = Doctor::whereHas('user', function ($query) {
            $query->where('status', 1);
        })->with(['services', 'user'])->get();
        $patient = Patient::with('student', 'teacher')->get();
        $services = Service::all();

        $selectedDoctor = null;
        $availableServices = [];

        return inertia('Appointment/Create', [
            'doctors' => $doctors,
            'services' => $services,
            'patient' => $patient,
            'selectedDoctor' => $selectedDoctor,
            'availableServices' => $availableServices,
        ]);
    }


    public function store(Request $request)
    {

        $pat_id = $request->input('pat_id');
        $firstname = $request->input('firstname');
        $lastname = $request->input('lastname');

        // Check if the patient with the given student_no or teacher_no exists
        $patient = Patient::whereHas('student', function ($query) use ($pat_id) {
            $query->where('student_no', $pat_id);
        })->orWhereHas('teacher', function ($query) use ($pat_id) {
            $query->where('teacher_no', $pat_id);
        })->first();

        if (!$patient) {
            return redirect()->back()->with('error', 'Student/Teacher not found');
        }

        // Check if the inputted firstname and lastname correspond to the patient
        if ($patient->firstname != $firstname || $patient->lastname != $lastname) {
            return redirect()->back()->with('error', 'Invalid patient name');
        }
        // Create the appointment
        $appointment = Appointment::create([
            'pat_id' => $patient->id,
            'date' => $request->input('date'),
            'time' => $request->input('time'),
            'doc_id' => $request->input('doc_id'),
            'service_id' => $request->input('service_id'),
            'reason' => $request->input('reason'),
            'email' => $request->input('email')

        ]);

        // dd($appointment);
        return redirect()->route('appointment.create')->with(['success' => 'Appointment submitted successfully']);
    }

    public function createForPatient(Request $request)
    {
        $services = Service::all();
        $isAdminOrDoctor = auth()->user()->hasAnyRole(['Admin', 'Doctor']);
        $isDoctor = auth()->user()->hasRole('Doctor');
        $selectedDoctor = null;
        $availableServices = [];

        $doctors = Doctor::whereHas('user', function ($query) {
            $query->where('status', 1);
        })->with(['services', 'user'])->get();



        // To search for users based on a search term provided in the request
        $searchTerm = $request->input('term');
        $patients = Patient::where('status', 1)
            ->where(function ($query) use ($searchTerm) {
                $query->where('firstname', 'like', "%$searchTerm%")
                    ->orWhere('lastname', 'like', "%$searchTerm%");
            })
            ->get();



        if ($isDoctor) {
            // If the user is a doctor, get their information
            $selectedDoctor = Doctor::where('user_id', auth()->user()->id)->first();
            $availableServices = $selectedDoctor->services;
        }

        return inertia('Appointment/CreateForPatient', [
            'doctors' => $doctors,
            'services' => $services,
            'patients' => $patients,
            'isAdminOrDoctor' => $isAdminOrDoctor,
            'isDoctor' => $isDoctor,
            'selectedDoctor' => $selectedDoctor,
            'availableServices' => $availableServices,
        ]);
    }

    public function storeForPatient(Request $request){
        $user = Auth::user();

        $fields = $request->validate([
            'pat_id' => 'required',
            'doc_id' => 'required',
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|after_or_equal:now',
            'reason' => 'required',
            'email' => 'nullable|email'
        ]);

        // If the user is a Doctor or Admin, automatically set the status to "Accepted" (status code 1)
        if ($user->hasRole('Doctor') || $user->hasRole('Admin')) {
            $fields['status'] = "Accepted";
        }

        if ($user->hasRole('Doctor')) {
            $fields['doc_id'] = $user->doctor->id;
        } elseif ($user->hasRole('Admin')) {
            // If the user is an Admin, expect the doc_id from the form
            $fields['doc_id'] = $request->input('doc_id');
        }

        Appointment::create($fields);
        return redirect()->route('appointment.index')->with('success', 'Appointment created successfully.');
    }


    public function edit(Request $request, Appointment $appointment)
    {
        $appointment = Appointment::with('patient', 'doctor.user', 'service')->findOrFail($appointment->id);
        $services = Service::all();
        $isAdminOrDoctor = auth()->user()->hasAnyRole(['Admin', 'Doctor']);
        $isDoctor = auth()->user()->hasRole('Doctor');
        $selectedDoctor = null;
        $availableServices = [];

        $doctors = Doctor::whereHas('user', function ($query) {
            $query->where('status', 1);
        })->with(['services', 'user'])->get();

        // Assuming you want to search for patients based on a search term provided in the request
        $searchTerm = $request->input('term');
        $patients = Patient::where('status', 1)
            ->where(function ($query) use ($searchTerm) {
                $query->where('firstname', 'like', "%$searchTerm%")
                    ->orWhere('lastname', 'like', "%$searchTerm%");
            })
            ->get();

        if ($isDoctor) {
            // If the user is a doctor, get their information
            $selectedDoctor = Doctor::where('user_id', auth()->user()->id)->first();
            $availableServices = $selectedDoctor->services;
        }

        return inertia('Appointment/Edit', [
            'doctors' => $doctors,
            'services' => $services,
            'patients' => $patients,
            'isAdminOrDoctor' => $isAdminOrDoctor,
            'isDoctor' => $isDoctor,
            'selectedDoctor' => $selectedDoctor,
            'availableServices' => $availableServices,
            'appointment' => $appointment,
        ]);
    }

    public function updateForPatient(Request $request, Patient $patient)
    {
        $appointment = Appointment::findOrFail($patient->id);

        $fields = $request->validate([
            'pat_id' => 'required',
            'doc_id' => 'required',
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|after_or_equal:now',
            'reason' => 'required',
        ]);

        if (Auth::user()->hasRole('Doctor')) {
            $fields['doc_id'] = Auth::user()->doctor->id;
        } elseif (Auth::user()->hasRole('Admin')) {
            // If the user is an admin, expect the doc_id from the form
            $fields['doc_id'] = $request->input('doc_id');
        }

        $appointment->update($fields);

        return redirect()->route('appointment.index')->with('success', 'Appointment updated successfully.');
    }



    public function show(Appointment $appointment){
        $appointment = Appointment::with(['patient', 'service', 'doctor'])->find($appointment->id);
        $doctor = Doctor::with('user', 'services')->find($appointment->doc_id);
        return inertia ('Appointment/Show',
            ['appointment'=> $appointment, 'doctor' => $doctor
        ]);
    }

    public function accept(Appointment $appointment){
        $appointment->update(['status' => 'Accepted']);

        $patient = $appointment->patient;
        $doctor = $appointment->doctor;
        // Send an email notification to the patient
        Mail::send('email.confirm-email', ['patient' => $patient, 'appointment'=>$appointment, 'doctor'=> $doctor ], function ($message) use ($patient, $appointment) {
            $message->to($appointment->email);
            $message->subject('Appointment Confirmation');
        });

        return redirect()->route('appointment.index')->with('success', 'Appointment approved successfully.');
    }

    public function cancel(Appointment $appointment){
        // Update the appointment status to 'canceled'
        $appointment->update(['status' => 'Cancelled']);

        $patient = $appointment->patient;

        // Send an email notification to the patient
        Mail::send('email.cancel-email', ['patient' => $patient, 'appointment'=>$appointment], function ($message) use ($patient, $appointment) {
            $message->to($appointment->email);
            $message->subject('Appointment Cancelation');
        });
        return redirect()->route('appointment.index')->with('success', 'Appointment canceled successfully.');
    }
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointment.index');
    }

}
