<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'specialization', 'status', 'lic_no'];

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointment(){
        return $this->hasMany(Appointment::class);
    }

    public function medcert(){
        return $this->hasOne(MedCert::class);
    }
}
