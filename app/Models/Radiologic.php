<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Radiologic extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function form(){
        return $this->belongsTo(Form::class);
    }
    public function getPicUrlAttribute()
{
    $filename = $this->exam_results;

    // Check if the file exists in the storage directory
    $filePath = public_path("results/radiologic_results/{$filename}");
    if (file_exists($filePath)) {
        // Construct and return the asset URL
        return asset("results/radiologic_results/{$filename}");
    }

    // If the file doesn't exist, return an empty string
    return "";
}
}
