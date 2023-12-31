<?php

namespace App\Http\Controllers;

use App\Events\UserLog;
use App\Models\Appointment;
use App\Models\Form;
use App\Models\Patient;
use App\Models\Radiologic;
use App\Models\Student;
use App\Models\Teacher;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as HttpRequest;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    public function index(){

        $user = auth()->user();

        if ($user->hasRole('Admin')) {
            $patient = Patient::with('student', 'teacher')->when(HttpRequest::input('search'), function ($query, $search) {
                $query->where('firstname', 'like', '%' . $search . '%')
                    ->orWhere('lastname', 'like', '%' . $search . '%')
                    ->orWhereHas('student', function ($studentQuery) use ($search) {
                        $studentQuery->where('student_no', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('teacher', function ($teacherQuery) use ($search) {
                        $teacherQuery->where('id', 'like', '%' . $search . '%');
                    });
            })
            ->where('status', 1)
            ->orderBy('created_at','desc')
            ->paginate(8)
            ->withQueryString();
        }elseif ($user->hasRole('Doctor')) {
            $patient = Patient::with('student', 'teacher')->when(HttpRequest::input('search'), function ($query, $search) {
                $query->where('firstname', 'like', '%' . $search . '%')
                    ->orWhere('lastname', 'like', '%' . $search . '%')
                    ->orWhereHas('student', function ($studentQuery) use ($search) {
                        $studentQuery->where('student_no', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('teacher', function ($teacherQuery) use ($search) {
                        $teacherQuery->where('teacher_no', 'like', '%' . $search . '%');
                    });
            })
            ->where('status', 1)
            ->orderBy('created_at','desc')
            ->paginate(8)
            ->withQueryString();
        }

        $activePatientsCount = Patient::where('status', 1)->count();
        return inertia('Patient/Index', [
            'patient' => $patient,
            'activePatientsCount' => $activePatientsCount,
            'filters' => HttpRequest::only(['search']),
        ]);
    }

    public function create(){
        return inertia('Patient/Create');
    }

    public function store(Request $request){

        $fields = $request->validate([
            'lastname' => 'required|string',
            'firstname' => 'required|string',
            'middlename' => 'nullable|string',
            'ext_name' => 'nullable|string',
            'sex' => 'required|string',
            'type' => 'required|string',
            // 'email' => 'required|email|unique:patients,email',
            'contact_no' => 'required',
            // 'emergency_contact' => 'required',
            'dob' => 'required|date',
            'address' => 'required',

        ]);

        $type = $fields['type'];
        $patient = Patient::create($fields);

        if ($type === 'Student') {
          $stud =   $request->validate([
                'student_no' => 'required|unique:students,student_no',
                // Add other validation rules for Student
            ]);

            // Create a new student associated with the patient
            $student = new Student([
                'student_no' => $stud['student_no'],
            ]);

            // Save the student first to get an ID.
            $patient->student()->save($student);
        } elseif ($type === 'Teacher') {
           $teach = $request->validate([
                'teacher_no' => 'required|unique:teachers,teacher_no',
                // Add other validation rules for Teacher
            ]);

            // Create a new teacher associated with the patient
            $teacher = new Teacher([
                'teacher_no' => $teach['teacher_no'],
            ]);

            // Save the teacher first to get an ID.
            $patient->teacher()->save($teacher);
        }

        $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " created the account of " . $patient->firstname . " ". $patient->lastname;
        event(new UserLog($log_entry));
        // $patient->update(['type' => $type]);
        return redirect()->route('patient.index')->with('success', 'Patient successfully created');
    }

    public function edit(Patient $patient){
        // $patient = Patient::with('student', 'teacher')->find($patient->id);
        $patient->load('student', 'teacher');
        return inertia('Patient/Edit', [
            'patient' => $patient,
        ]);
    }

    public function update(Request $request, Patient $patient){
        $fields = $request->validate([
            'lastname' => 'required|string',
            'firstname' => 'required|string',
            'middlename' => 'nullable|string',
            'ext_name' => 'nullable|string',
            'sex' => 'required|string',
            'type' => 'required|string',
            // 'email' => 'required|email|unique:patients,email,' . $patient->id,
            'contact_no' => 'required',
            'dob' => 'required|date',
            'address' => 'required',
        ]);

        $type = $fields['type'];



    if ($type === 'Student') {
        $student_no = $request->input('student_no');

        // Update the student type here.
        $patient->student()->updateOrCreate(
            [],
            [
                'student_no' => $student_no,

            ]
        );

        // Remove the teacher record if it exists
        if ($patient->teacher) {
            $patient->teacher->delete();
        }
    } elseif ($type === 'Teacher') {
        // Generate a UUID for the teacher's ID
        $id = Str::uuid();

        // Update the teacher type here.
        $patient->teacher()->updateOrCreate(
            [],
            ['id' => $id]
        );

        // Remove the student record if it exists
        if ($patient->student) {
            $patient->student->delete();
        }
    }
    // Update the patient details
    $patient->update($fields);
    $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " updated the details of " . $patient->firstname . " ". $patient->lastname;
        event(new UserLog($log_entry));
    return redirect('/patient/show/' . $patient->id)->with('success', 'Patient successfully updated');
    }


     public function search(Request $request)
    {
        try {
            $term = $request->query('term');

            $patients = Patient::with('student', 'teacher')
                ->when($term, function ($query) use ($term) {
                    $query->where('firstname', 'like', '%' . $term . '%')
                        ->orWhere('lastname', 'like', '%' . $term . '%')
                        ->orWhereHas('student', function ($studentQuery) use ($term) {
                            $studentQuery->where('student_no', 'like', '%' . $term . '%');
                        })
                        ->orWhereHas('teacher', function ($teacherQuery) use ($term) {
                            $teacherQuery->where('id', 'like', '%' . $term . '%');
                        });
                })
                ->where('status', 1) // Filter active patients
                ->get();

            return response()->json(['patients' => $patients]);
        } catch (\Exception $e) {
            // Handle the exception and return an error response if needed
            return response()->json(['error' => 'An error occurred while searching for patients.'], 500);
        }

    }




    public function show(Patient $patient)
    {
        // Find patient by ID with eager loaded relationships
        $patient = Patient::with('student', 'teacher')->find($patient->id);

        $appCount = Appointment::where('pat_id', $patient->id)->count();
        $medCount = Form::where('pat_id', $patient->id)->count();

        // Fetch patient appointments with eager loaded relationships
        $patientAppointments = Appointment::with(['doctor.user', 'service', 'patient'])
            ->where('pat_id', $patient->id)
            ->orderByDesc('created_at')
            ->paginate(4);

        // Fetch medical history with eager loaded relationships
        $medicalHistory = Form::with(
            ['patient', 'doctor.user', 'history', 'remark', 'medicalhistory', 'physicalexamination', 'radiologic']
        )
            ->where('pat_id', $patient->id)
            ->orderByDesc('created_at')
            ->paginate(4);


        $radiologic = Form::with(['patient', 'doctor.user','radiologic'])
        ->where('pat_id', $patient->id)
            ->orderByDesc('created_at')
            ->get();
        return inertia('Patient/Show', [
            'patient' => $patient,
            'patientAppointments' => $patientAppointments,
            'medicalHistory' => $medicalHistory,
            'radiologic' => $radiologic,
            'appCount' => $appCount,
            'medCount' => $medCount
        ]);
    }



    public function destroy(Patient $patient) {
        $patient->delete();
        $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " deleted the account of " . $patient->firstname . " ". $patient->lastname;
        event(new UserLog($log_entry));
        return back();
    }

    public function getPatientInfo($patId)
    {
        $patient = Patient::find($patId);

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        // Get the patient Firstname and Lastname
        return response()->json([
            'id' => $patient->id,
            'name' => $patient->firstname . ' ' . $patient->lastname,

        ]);
    }

    public function patientPdf(){
        $patient = Patient::with('student', 'teacher')->orderBy('type', 'asc')->where('status', 1)->get();
        $pdf = PDF::loadView('pdf.patientPdf', [
            'patient' => $patient
        ]);
        $log_entry = Auth::user()->firstname . "". Auth::user()->lastname . " exported a pdf file of all patient  ";
        event(new UserLog($log_entry));
        return $pdf->stream();
    }

    public function deactivatePatient(Patient $patient){
        // Assuming there is a 'user' relationship in your patient model
        $patient->update(['status' => 0]);
        $log_entry = Auth::user()->firstname . "". Auth::user()->lastname . " deactivated the account of " . $patient->firstname . " ". $patient->lastname;
        event(new UserLog($log_entry));
        return redirect('/patient')->with('success', 'Patient deactivated successfully');
    }


    public function activatePatient(Patient $patient){
        $patient->update(['status' => 1]);
        $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " activated the account of " . $patient->firstname . " ". $patient->lastname;
        event(new UserLog($log_entry));
        return redirect('/patient')->with('success','Patient activated successfully');
    }

    public function inactive(){
        $patient = Patient::with('student', 'teacher')->when(HttpRequest::input('search'), function ($query, $search) {
            $query->where('firstname', 'like', '%' . $search . '%')
                ->orWhere('lastname', 'like', '%' . $search . '%')
                ->orWhereHas('student', function ($studentQuery) use ($search) {
                    $studentQuery->where('student_no', 'like', '%' . $search . '%');
                })
                ->orWhereHas('teacher', function ($teacherQuery) use ($search) {
                    $teacherQuery->where('teacher_no', 'like', '%' . $search . '%');
                });
        })
        ->where('status', 0)
        ->orderBy('created_at','desc')
        ->paginate(8)
        ->withQueryString();
        $inactivePatientsCount = Patient::where('status', 0)->count();
        return inertia('Patient/Inactive', [
            'patient' => $patient,
            'inactivePatientsCount' => $inactivePatientsCount,
            'filters' => HttpRequest::only(['search']),
        ]);
    }
}

