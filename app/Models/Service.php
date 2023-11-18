<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = ['name' , 'description'];

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class);
    }

    public function appointment(){
        return $this->hasMany(Appointment::class);
    }
}
