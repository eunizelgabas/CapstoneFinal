<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Dispensing;
use App\Models\Form;
use App\Models\MedicalHistory;
use App\Models\Patient;
use App\Models\PhysicalExamination;
use App\Models\Stock;
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
    //     $yearlyAppointment = DB::table('appointments')
    //     ->select(DB::raw('YEAR(created_at) as year'), DB::raw('COUNT(*) as total_appointments'),
    //     'status'
    // )
    // ->whereIn('status', ['Pending', 'Cancelled', 'Accepted'])
    // ->whereYear('date', now()->year)
    // ->groupBy('year', 'status')
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
            'currentMonth' => $currentMonth,

        ]);
    }

    public function annual(){
        $currentYear = now()->year;
        // $yearlyReport = Stock::select(
        //     DB::raw('YEAR(created_at) as year'),
        //     DB::raw('MONTH(created_at) as month'),
        //     'med_id',
        //     DB::raw('COUNT(*) as total_purchase')
        // )
        // ->with('medicine:id,name')
        // ->groupBy('year', 'month', 'med_id')
        // ->orderByDesc('total_purchase')
        // ->get();
        $yearlyReport = Stock::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            'med_id',
            DB::raw('COUNT(*) as total_purchase'),
            DB::raw('COUNT(DISTINCT med_id) as medicine_count'),
            DB::raw('SUM(qty) as total_qty') // Add this line
        )
        ->with(['medicine:id,name']) // Assuming you have a 'medicine' relationship in your Stock model
        ->groupBy('year', 'month', 'med_id')
        ->orderByDesc('total_purchase')
        ->get();

        return inertia('Report/MedReport', [
            'currentYear' => $currentYear,
            'yearlyReport' => $yearlyReport
        ]);
    }

    public function dispense(){
        $currentYear = now()->year;
        $yearlyReport = Dispensing::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            'med_id',
            DB::raw('COUNT(*) as total_dispense'),
            DB::raw('COUNT(DISTINCT med_id) as medicine_count'),
            DB::raw('SUM(qty) as total_qty')
        )
        ->with('medicine:id,name')
        ->groupBy('year', 'month', 'med_id')
        ->orderByDesc('total_dispense')
        ->get();
        return inertia('Report/MedicineReport', [
            'currentYear' => $currentYear,
            'yearlyReport' => $yearlyReport
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
        ->groupBy('year', 'month', 'highblood_pressure', 'diabetes')
        ->get();
        $yearlyReport2 = MedicalHistory::select(

            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as total_diabetes'),
            'diabetes'
        )
        ->whereIn('diabetes', ['1'])
        ->groupBy('year', 'month', 'highblood_pressure', 'diabetes')
        ->get();

        return inertia('Report/MedChart', [
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth,
            'yearlyReport' => $yearlyReport,
            'totalPatient' => $totalPatient,
            'yearlyReport2' => $yearlyReport2
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
