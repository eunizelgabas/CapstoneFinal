<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Form;
use App\Models\MedicalHistory;
use App\Models\Patient;
use App\Models\PhysicalExamination;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(){

        $currentYear = now()->year; // Get the current year
        $currentMonth = Carbon::now()->month;
        // $monthlyAppointment = Appointment::whereYear('created_at', now()->year)
        // ->whereMonth('created_at', now()->month)
        // ->get();
        $monthlyAppointment = Appointment::select(
            DB::raw('MONTH(date) as month'),
            DB::raw('YEAR(date) as year'),
            DB::raw('COUNT(*) as total_appointments'),
            'status'
        )
        ->whereIn('status', ['Pending', 'Cancelled', 'Accepted'])
        ->whereYear('date', now()->year)
        ->whereMonth('date', now()->month)
        ->groupBy('year', 'month', 'status')
        ->get();


        return inertia('Report/Chart', [
            'monthlyAppointment' => $monthlyAppointment,
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth
        ]);
    }

    public function medreport(){
        $currentYear = now()->year;
        return inertia('Report/Calendar', [
            'currentYear' => $currentYear
        ]);
    }

    public function medchart(){
        $currentYear = now()->year; // Get the current year
        $currentMonth = Carbon::now()->month;
        $totalPatient = Patient::with('student', 'teacher')->count();
        $yearlyReport = MedicalHistory::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as total_highblood'),
            'highblood_pressure'
        )
        ->whereIn('highblood_pressure', ['1'])
        ->groupBy('year', 'month', 'highblood_pressure')
        ->get();

        return inertia('Report/MedChart', [
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth,
            'yearlyReport' => $yearlyReport,
            'totalPatient' => $totalPatient
        ]);
    }

    public function calendar(){

        $user = auth()->user();

        // $appointments = Appointment::with('patient', 'doctor.user', 'service')
        // ->orderBy('created_at', 'asc')
        // ->get();

        if ($user->hasRole('Admin')) {
            $appointments = Appointment::with('patient', 'doctor.user', 'service')
                ->orderBy('created_at', 'asc')
                ->get();

            // Admin logic
            $currentMonthAppointments = Appointment::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();

        } elseif ($user->hasRole('Doctor')) {
            // Doctor logic
            $doctor = $user->doctor;

            $appointments = Appointment::with(['doctor', 'doctor.user', 'patient', 'service'])
                ->whereHas('doctor', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->orderBy('created_at', 'desc')

                ->get();

            $currentMonthAppointments = Appointment::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->where('doc_id', $doctor->id)
                ->count();

        } else {
            // Handle other roles or unauthorized access as needed
            return abort(403);
        }
        return inertia('Report/Calendar', [
            'appointments' => $appointments,
            'currentMonthAppointments' => $currentMonthAppointments
        ]);
    }
}
