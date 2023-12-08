<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Form;
use App\Models\History;
use App\Models\MedicalHistory;
use App\Models\Patient;
use App\Models\PhysicalExamination;
use App\Models\Radiologic;
use App\Models\Remark;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;

class FormController extends Controller
{
    public function create(Patient $patient){


        if ($patient->status === 0) {
            return redirect('/patient/show/'. $patient->id)->with('error', 'Cannot create a medical record for a patient with inactive status.');
        }

        $isDoctor = auth()->user()->hasRole('Doctor');
        $selectedDoctor = null;
        $doctor = Doctor::whereHas('user', function ($query) {
            $query->where('status', 1);
        })->with(['user'])->get();


        if($isDoctor){
            $doctor = Doctor::with('user') ->where('user_id', auth()->user()->id)
                    ->whereHas('user', function ($query) {
                        $query->where('status', 1);
                    })->get();
        }


        $patient->load('student', 'teacher');

        return inertia('Form/Sample', [
            'patient' => $patient,
            'doctor' => $doctor,
            'isDoctor' => $isDoctor,
            'selectedDoctor' => $selectedDoctor
        ]);
    }

    public function store(Request $request)
{
    $user = Auth::user();

    // Validate the main form data
    $validatedFormData = $request->validate([
        'doc_id' => 'required|exists:doctors,id',
        'pat_id' => 'required|exists:patients,id',
        'vaccine' => 'required|string',
        'date' => 'date|after_or_equal:today',
    ]);

    if ($user->hasRole('Doctor')) {
        $fields['doc_id'] = $user->doctor->id;
    } elseif ($user->hasRole('Admin')) {
        // If the user is an Admin, expect the doc_id from the form
        $fields['doc_id'] = $request->input('doc_id');
    }
    // Create the main form record
    $form = Form::create($validatedFormData);
    $formId = $form->id;

    // Validate and store MedicalHistory
    $medicalHistoryData = $request->validate([
        // Add validation rules for other fields
        'allergy' => 'boolean',
        'amoebiasis' => 'boolean',
        'anemia' => 'boolean',
        'asthma' => 'boolean',
        'cancer' => 'boolean',
        'chronic' => 'boolean',
        'diabetes' => 'boolean',
        'dysmenorrhea' => 'boolean',
        'fainting' => 'boolean',
        'deafness' => 'boolean',
        'endocrine' => 'boolean',
        'frequent_headache' => 'boolean',
        'neck_injury' => 'boolean',
        'heart_disease' => 'boolean',
        'hernias' => 'boolean',
        'highblood_pressure' => 'boolean',
        'insomnia' => 'boolean',
        'kidney_disease' => 'boolean',
        'skeletal_disorder' => 'boolean',
        'nervous_breakdown' => 'boolean',
        'nose_trouble' => 'boolean',
        'peptic_ulcer' => 'boolean',
        'tuberculosis' => 'boolean',
        'hospitalization' => 'boolean',
        'surgical_operation' => 'boolean',
    ]);
    $medicalHistoryData['form_id'] = $formId;
    $medicalHistory = MedicalHistory::create($medicalHistoryData);

    // Validate and store Remarks
    $remarksData = $request->validate([
        'remarks' => 'required|string',
    ]);
    $remarksData['form_id'] = $formId;
    $remark = Remark::create($remarksData);

    // Validate and store PhysicalExamination
    $physicalExamData = $request->validate([
        // Add validation rules for other fields
        'height' => 'numeric',
        'weight' => 'numeric',
        'bp' => 'string',
        'rr' => 'string',
        'pr' => 'string',
        'saturation' => 'string',
        'lmp' => 'date|nullable',
        'head_neck_scalp' => 'boolean',
        'eyes' => 'boolean',
        'ears' => 'boolean',
        'nose_sinuses' => 'boolean',
        'mouth' => 'boolean',
        'neck_thyroid' => 'boolean',
        'chest_breast_axilla' => 'boolean',
        'heart' => 'boolean',
        'lungs' => 'boolean',
        'abdomen' => 'boolean',
        'back' => 'boolean',
        'anus' => 'boolean',
        'gut' => 'boolean',
        'inguinals' => 'boolean',
        'reflexes' => 'boolean',
        'extremities' => 'boolean',
        'dental' => 'boolean',
        'right_eye' => 'string',
        'left_eye' => 'string',
        'withg_right_eye'=> 'string',
        'withg_left_eye' => 'string',
        'ishihara' => 'string',
        'colour_blind' => 'string',
        'head_neck_scalp_findings'=> 'string|nullable',
        'eyes_findings'=> 'string|nullable',
        'ears_findings'=> 'string|nullable',
        'nose_sinuses_findings'=> 'string|nullable',
        'mouth_findings'=> 'string|nullable',
        'neck_thyroid_findings'=> 'string|nullable',
        'chest_breast_axilla_findings'=> 'string|nullable',
        'heart_findings'=> 'string|nullable',
        'lungs_findings'=> 'string|nullable',
        'abdomen_findings'=> 'string|nullable',
        'back_findings'=> 'string|nullable',
        'anus_findings'=> 'string|nullable',
        'gut_findings'=> 'string|nullable',
        'inguinals_findings'=> 'string|nullable',
        'reflexes_findings'=> 'string|nullable',
        'extremities_findingd'=> 'string|nullable',
        'dental_finding'=> 'string|nullable',
    ]);
    $physicalExamData['form_id'] = $formId;
    $physicalExam = PhysicalExamination::create($physicalExamData);

    // Validate and store History
    $historyData = $request->validate([
        'hist' => 'string|nullable',
    ]);
    $historyData['form_id'] = $formId;
    $history = History::create($historyData);

    //Validate and store Radiologic
    $radiologicData = $request->validate([
        // Add validation rules for other fields
        'exam_results' => 'file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:10240|nullable',
    ]);

    $fileName = null;
    if ($request->exam_results) {
        $fileName = time() . "." . $request->exam_results->extension();
        $request->exam_results->move(public_path('results/radiologic_results'), $fileName);
        $radiologicData['exam_results'] = $fileName;
    }

    $radiologicData['form_id'] = $formId;
    $radiologic = Radiologic::create($radiologicData);

    // Redirect to a success page or return a response
    return redirect('/healthForm/show/' . $form->id)->with('success', 'Physical Examination record and Medical History saved successfully');
}

    public function show(Form $form){
        $form->load(['patient.student', 'patient.teacher', 'doctor.user', 'history', 'remark', 'medicalhistory', 'physicalexamination', 'radiologic'])->find($form->id);

        return inertia ('Form/Show',
            ['form'=> $form,

        ]);
    }

    public function getImageUrl($imageName) {
        return public_path("results/$imageName");
    }

    public function formPdf(Form $form){

        $form->with(['patient', 'doctor', 'physicalexamination'])->find($form->id);
        // $imagePath = $this->getImageUrl($form->radiologic->exam_results);
        $imagePath = public_path('results/radiologic_results/' . $form->radiologic->exam_results);
        $imageData = base64_encode(file_get_contents($imagePath));
        $medicalhistory = MedicalHistory::all()->find($form->id);

        $history = $form->history;
        $physicalexamination = $form->physicalexamination;
        $remark = Remark::all()->find($form->id);
        $age = Carbon::parse($form->patient->dob)->age;

         $pdf = PDF::loadView('pdf.form',
         [
            'form'=> $form,
            'medicalhistory' => $medicalhistory,
            'physicalexamination' => $physicalexamination,
            'remark' => $remark,
            'age' => $age,
            'history' => $history,
            // 'imagePath' => $imagePath,
            // 'imageContent' => $imageContent,
            'imageData' => $imageData
        ]);

        return $pdf->stream();

    }

    public function medCert(Form $form){

        $form = Form::with('patient.student', 'patient.teacher', 'doctor.user')->findOrFail($form->id);
        // $patient = Patient::with('student', 'teacher')->findOrFail($form->id);
        // $doctor = Doctor::with(['user'])->get()->findOrFail($form->id);

        $age = Carbon::parse($form->patient->dob)->age;
        $data = [
            'title' => 'MATER DEI COLLEGE',
            'header' => 'Medical Certificate',
            'address' => 'Tubigon, Bohol',
            'facility' => 'School Clinic',
            'date' => date('d/m/Y'),
            'form' => $form,


        ];
        $date = Date::now();

        $pdf = PDF::loadView('pdf.medCert',[
            $data,
            'age'=> $age,
            'form' => $form,
            'date'=> $date,
            // 'doctor'=> $doctor
            ] );
        return $pdf->stream();

   }
}
