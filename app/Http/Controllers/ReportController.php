<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
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
        return inertia('Report/MedReport');
    }
}
