<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    public $fillable = ['pat_id', 'date', 'time', 'doc_id', 'service_id', 'status', 'reason'];

    public function patient(){
        return $this->belongsTo(Patient::class, 'pat_id');
    }

    public function doctor(){
        return $this->belongsTo(Doctor::class, 'doc_id');
    }

    public function service(){
        return $this->belongsTo(Service::class, 'service_id');
    }
}
