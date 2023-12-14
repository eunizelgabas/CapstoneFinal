<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(){

        $logEntries = Log::orderBy('created_at', 'desc')->paginate(8)
        ->withQueryString();
        return inertia('Logs/Index',
        ['logEntries' => $logEntries]);
    }
}
