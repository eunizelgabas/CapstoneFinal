<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\MedCert;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;


class MedCertController extends Controller
{
    public function generate(){
        $isDoctor = auth()->user()->hasRole('Doctor');
        $selectedDoctor = null;
        $doctor = Doctor::whereHas('user', function ($query) {
            $query->where('status', 1);
        })->with(['user'])->get();

        if ($isDoctor) {
            // If the user is a doctor, get their information
            $selectedDoctor = Doctor::where('user_id', auth()->user()->id)->first();
        }

        return inertia('MedCert/Index', [
            'doctor' => $doctor,
            'isDoctor' => $isDoctor,
            'selectedDoctor' => $selectedDoctor
        ]);
    }

    public function store(Request $request){
        $user = Auth::user();

        $fields = $request->validate([
            'pat_id'=>'required',
            'doc_id'=>'required',
            'diagnosis' => 'required',
            'date' => 'required',
            'days' => 'required'

        ]);

        if ($user->hasRole('Doctor')) {
            $fields['doc_id'] = $user->doctor->id;
        } elseif ($user->hasRole('Admin')) {
            // If the user is an Admin, expect the doc_id from the form
            $fields['doc_id'] = $request->input('doc_id');
        }

        $medcert = MedCert::create($fields);

        return redirect('/medcert/show/' . $medcert->id)->with('success', 'Medicine Category successfully created');
    }


    public function show(MedCert $medcert){
        $medcert->load(['patient', 'doctor.user'])->find($medcert->id);


        // Check if $medcert is null before passing it to the view
        if (!$medcert) {
            // Handle the case where the $medcert is not found
            abort(404);
        }

        return inertia ('MedCert/Show',
            ['medcert'=> $medcert,
            'age' => optional($medcert->patient)->age,

        ]);
    }

    public function pdf(MedCert $medcert){
        $medcert->with(['patient', 'doctor.user'])->find($medcert->id);
        $age = Carbon::parse($medcert->patient->dob)->age;

        $pdf = PDF::loadView('pdf.certificate',
        [
            'medcert'=> $medcert,
           'age' => $age

       ]);


       return $pdf->stream();
    }


}
