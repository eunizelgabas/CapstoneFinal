<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;
    protected $fillable = ['form_id' ,'hist'];

    public function form(){
        return $this->belongsTo(Form::class);
    }
}
