<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Form;
use App\Models\Patient;
use App\Models\Radiologic;
use Illuminate\Http\Request;

class RadiologicController extends Controller
{
    public function create(Patient $patient){
        $doctor = Doctor::whereHas('user')->with([ 'user'])->get();
        $patient->load('student', 'teacher');
        return inertia('Radiologic/Sample', [
            'patient' => $patient,
            'doctor' => $doctor
        ]);
    }

    public function store(Request $request){
        $validatedFormData = $request->validate([
            'doc_id' => 'required|exists:doctors,id',
            'pat_id' => 'required|exists:patients,id',
            'vaccine' => 'required|string',
            'date' => 'date|after_or_equal:today',
        ]);
        $date = now();
        $validatedFormData['date'] = $date;

        // Create the main form record
        $form = Form::create($validatedFormData);
        $formId = $form->id;
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
}
