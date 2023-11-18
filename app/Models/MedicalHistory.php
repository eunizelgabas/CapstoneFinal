<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function form(){
        return $this->belongsTo(Form::class,'form_id');
    }
}
