<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;
    protected $fillable = ['cat_id', 'type_id', 'name' , 'stock', 'measurement'];

    public function category()
    {
        return $this->belongsTo(MedCategory::class, 'cat_id');
    }

    public function type()
    {
        return $this->belongsTo(MedType::class, 'type_id');
    }

    public function inventory(){
        return $this->hasOne(Inventory::class, 'med_id');
    }

    public function stock()
    {
        return $this->hasMany(Stock::class, 'med_id');
    }

    public function dispensing()
    {
        return $this->hasMany(Dispensing::class, 'med_id');
    }
}
