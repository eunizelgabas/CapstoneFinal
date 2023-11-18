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

class FormController extends Controller
{
    public function create(Patient $patient){
        $doctor = Doctor::whereHas('user')->with([ 'user'])->get();
        $patient->load('student', 'teacher');
        return inertia('Form/Sample', [
            'patient' => $patient,
            'doctor' => $doctor
        ]);
    }

    public function store(Request $request)
{
    // Validate the main form data
    $validatedFormData = $request->validate([
        'doc_id' => 'required|exists:doctors,id',
        'pat_id' => 'required|exists:patients,id',
        'vaccine' => 'required|string',
        'date' => 'required|date',
    ]);

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
        'lmp' => 'string',
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
        'hist' => 'nullable|string',
    ]);
    $historyData['form_id'] = $formId;
    $history = History::create($historyData);

    //Validate and store Radiologic
    $radiologicData = $request->validate([
        // Add validation rules for other fields
        'exam_results' => 'sometimes|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:10240',
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
        $form->load(['patient.student', 'doctor', 'history', 'remark', 'medicalhistory', 'physicalexamination'])->find($form->id);
        $medicalhistory = MedicalHistory::all()->find($form->id);
        // $physicalexamination = PhysicalExamination::all()->find($form->id);
        // $remark = Remarks::all()->find($form->id);
        // $history = History::all()->find($form->id);
        // $serviceCount = $doctor->services->count();
        return inertia ('Form/Show',
            ['form'=> $form,
            // 'medicalhistory' => $medicalhistory,
            // 'physicalexamination' => $physicalexamination,
            // 'remark' => $remark,
            // 'history' => $history
        ]);
    }

    public function formPdf(Form $form){
        $form->load(['patient', 'doctor'])->find($form->id);
        $medicalhistory = MedicalHistory::all()->find($form->id);
        $physicalexamination = PhysicalExamination::all()->find($form->id);
        $remark = Remark::all()->find($form->id);
        $age = Carbon::parse($form->patient->dob)->age;

         $pdf = PDF::loadView('pdf.form',
         ['form'=> $form,
         'medicalhistory' => $medicalhistory,
         'physicalexamination' => $physicalexamination,
         'remark' => $remark,
         'age' => $age
        ]);

        return $pdf->stream();

    }

    public function medCert(Patient $patient){
        $patient = Patient::with('student', 'form', 'teacher')->findOrFail($patient->id);

        $age = Carbon::parse($patient->dob)->age;
        $data = [
            'title' => 'MATER DEI COLLEGE',
            'header' => 'Medical Certificate',
            'address' => 'Tubigon, Bohol',
            'facility' => 'School Clinic',
            'date' => date('d/m/Y'),
            'patient' => $patient,

        ];

        $pdf = PDF::loadView('pdf.medCert',[
            $data,
            'age'=> $age,
            'patient' => $patient
            ] );
        return $pdf->stream();

   }
}
