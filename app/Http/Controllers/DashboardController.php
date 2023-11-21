<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // public function index()
    // {
    //     $user = auth()->user();
    //     if ($user->hasRole('Admin')) {
    //         $appointments = Appointment::with('patient', 'doctor.user', 'service')
    //             ->when(request('search'), function ($query, $search) {
    //                 $query->where('status', 'like', '%' . $search . '%')
    //                     ->orWhereHas('patient', function ($patientQuery) use ($search) {
    //                         $patientQuery->where('firstname', 'like', '%' . $search . '%')
    //                             ->orWhere('lastname', 'like', '%' . $search . '%');
    //                     })
    //                     ->orWhereHas('doctor.user', function ($doctorQuery) use ($search) {
    //                         $doctorQuery->where('firstname', 'like', '%' . $search . '%')
    //                             ->orWhere('lastname', 'like', '%' . $search . '%');
    //                     });
    //             })
    //             ->orderBy('created_at', 'desc')
    //             ->take(5)
    //             ->get();
    //             $currentMonth = Carbon::now()->month;
    //             $currentYear = Carbon::now()->year;

    //             $currentMonthAppointments = Appointment::whereYear('created_at', $currentYear)
    //                 ->whereMonth('created_at', $currentMonth)
    //                 ->count();

    //             // Total appointments for the last month and year
    //             $lastMonthAppointments = Appointment::whereYear('created_at', now()->subMonth()->year)
    //                 ->whereMonth('created_at', now()->subMonth()->month)
    //                 ->count();

    //             // Calculate the percentage change
    //             $percentageChange = $lastMonthAppointments != 0
    //                 ? (($currentMonthAppointments - $lastMonthAppointments) / $lastMonthAppointments) * 100
    //                 : 100;

    //             $cancelledAppointmentsByMonth = Appointment::where('status', 'Cancelled')
    //                 ->whereYear('created_at', now()->year)
    //                 ->whereMonth('created_at', now()->month)
    //                 ->count();
    //             $upcomingAppointmentsByMonth = Appointment::where('status', 'Pending')
    //                 ->whereYear('created_at', now()->year)
    //                 ->whereMonth('created_at', now()->month)
    //                 ->count();

    //     } elseif ($user->hasRole('Doctor')) {
    //         $appointments = Appointment::with(['doctor', 'doctor.user', 'patient', 'service'])
    //             ->whereHas('doctor', function ($query) use ($user) {
    //                 $query->where('user_id', $user->id);
    //             })
    //             ->when(request('search'), function ($query, $search) {
    //                 $query->where('status', 'like', '%' . $search . '%')
    //                     ->orWhereHas('patient', function ($patientQuery) use ($search) {
    //                         $patientQuery->where('firstname', 'like', '%' . $search . '%')
    //                             ->orWhere('lastname', 'like', '%' . $search . '%');
    //                     });
    //             })
    //             ->orderBy('id','desc')
    //             ->take(5)
    //             ->get();
    //           // Doctor logic
    //           $doctor = $user->doctor;

    //           $currentMonthAppointments = Appointment::whereYear('created_at', now()->year)
    //               ->whereMonth('created_at', now()->month)
    //               ->where('doc_id', $doctor->id)
    //               ->count();

    //           // Total appointments for the last month and year for the doctor
    //           $lastMonthAppointments = Appointment::whereYear('created_at', now()->subMonth()->year)
    //               ->whereMonth('created_at', now()->subMonth()->month)
    //               ->where('doc_id', $doctor->id)
    //               ->count();

    //           // Calculate the percentage change
    //           $percentageChange = $lastMonthAppointments != 0
    //               ? (($currentMonthAppointments - $lastMonthAppointments) / $lastMonthAppointments) * 100
    //               : 100;

    //           $cancelledAppointmentsByMonth = Appointment::where('status', 'Cancelled')
    //               ->where('doc_id', $doctor->id)
    //               ->whereYear('created_at', now()->year)
    //               ->whereMonth('created_at', now()->month)
    //               ->count();
    //         $upcomingAppointmentsByMonth = Appointment::where('status', 'Pending')
    //               ->where('doc_id', $doctor->id)
    //               ->whereYear('created_at', now()->year)
    //               ->whereMonth('created_at', now()->month)
    //               ->count();
    //     }  else {
    //         // Handle other roles or unauthorized access as needed
    //         return abort(403);
    //     }

    // foreach ($appointments as &$appointment) {
    //     $patientName = $appointment['patient']['firstname'] . ' ' . $appointment['patient']['lastname'];
    //     $appointment['popoverLabel'] = "{$patientName} - {$appointment['status']}";
    // }
    //     return inertia('Dashboard', [
    //         'user' => $user,
    //         'appointments' => $appointments,
    //         'totalAppointments'=> $currentMonthAppointments,
    //         'percentageChange' =>$percentageChange,
    //         'filters' => request()->only(['search']),
    //         'cancelledAppointmentsByMonth' => $cancelledAppointmentsByMonth,
    //         'upcomingAppointmentsByMonth' => $upcomingAppointmentsByMonth
    //     ]);
    // }

    public function index()
{
    $user = auth()->user();

    if ($user->hasRole('Admin')) {
        $appointments = Appointment::with('patient', 'doctor.user', 'service')
            ->when(request('search'), function ($query, $search) {
                $query->where('status', 'like', '%' . $search . '%')
                    ->orWhereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('firstname', 'like', '%' . $search . '%')
                            ->orWhere('lastname', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('doctor.user', function ($doctorQuery) use ($search) {
                        $doctorQuery->where('firstname', 'like', '%' . $search . '%')
                            ->orWhere('lastname', 'like', '%' . $search . '%');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Admin logic
        $currentMonthAppointments = Appointment::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $lastMonthAppointments = Appointment::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();

        $percentageChange = $lastMonthAppointments != 0
            ? (($currentMonthAppointments - $lastMonthAppointments) / $lastMonthAppointments) * 100
            : 100;

        $cancelledAppointmentsByMonth = Appointment::where('status', 'Cancelled')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $upcomingAppointmentsByMonth = Appointment::where('status', 'Pending')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
    } elseif ($user->hasRole('Doctor')) {
        // Doctor logic
        $doctor = $user->doctor;

        $appointments = Appointment::with(['doctor', 'doctor.user', 'patient', 'service'])
            ->whereHas('doctor', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->when(request('search'), function ($query, $search) {
                $query->where('status', 'like', '%' . $search . '%')
                    ->orWhereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('firstname', 'like', '%' . $search . '%')
                            ->orWhere('lastname', 'like', '%' . $search . '%');
                    });
            })
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $currentMonthAppointments = Appointment::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('doc_id', $doctor->id)
            ->count();

        $lastMonthAppointments = Appointment::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->where('doc_id', $doctor->id)
            ->count();

        $percentageChange = $lastMonthAppointments != 0
            ? (($currentMonthAppointments - $lastMonthAppointments) / $lastMonthAppointments) * 100
            : 100;

        $cancelledAppointmentsByMonth = Appointment::where('status', 'Cancelled')
            ->where('doc_id', $doctor->id)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $upcomingAppointmentsByMonth = Appointment::where('status', 'Pending')
            ->where('doc_id', $doctor->id)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
    } else {
        // Handle other roles or unauthorized access as needed
        return abort(403);
    }

    foreach ($appointments as &$appointment) {
        $patientName = $appointment['patient']['firstname'] . ' ' . $appointment['patient']['lastname'];
        $time = Carbon::parse($appointment['time']);

    if ($time === false) {
        // Handle parsing error
        // For example, log the error or set a default time
        $formattedTime = 'Invalid Time';
    } else {
        $formattedTime = $time->format('h:i A'); // Format to 12-hour time with AM/PM
    }

        $appointment['popoverLabel'] = "{$patientName} - {$formattedTime} - {$appointment['status']}";

        // Additional code to set other properties if needed
    }

    return inertia('Dashboard', [
        'user' => $user,
        'appointments' => $appointments,
        'totalAppointments' => $currentMonthAppointments,
        'percentageChange' => $percentageChange,
        'filters' => request()->only(['search']),
        'cancelledAppointmentsByMonth' => $cancelledAppointmentsByMonth,
        'upcomingAppointmentsByMonth' => $upcomingAppointmentsByMonth
    ]);
}


}
