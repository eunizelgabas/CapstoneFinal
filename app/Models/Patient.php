<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = ['lastname', 'firstname', 'middlename', 'ext_name', 'address', 'contact_no', 'emergency_contact', 'type', 'status', 'dob', 'email', 'sex'];

    public function student(){
        return $this->hasOne(Student::class);
    }

    public function teacher(){
        return $this->hasOne(Teacher::class);
    }

    public function appointment(){
        return $this->hasMany(Appointment::class);
    }


    protected static function boot()
    {
        parent::boot();

        // Register the "deleting" event to delete related records
        static::deleting(function ($patient) {
            if ($patient->student) {
                $patient->student->delete();
            }

            if ($patient->teacher) {
                $patient->teacher->delete();
            }
        });
    }
}

