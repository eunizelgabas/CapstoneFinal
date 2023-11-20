<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lastname',
        'firstname',
        'middlename',
        'ext_name',
        'gender',
        'contact_no',
        'type',
        'email',
        'password',
        'status',

    ];

    protected static function boot()
    {
        parent::boot();

        // Register the "deleting" event to delete related records
        static::deleting(function ($user) {
            // Check if the user type is being changed to 'admin'
            if ($user->isDirty('user_type') && $user->user_type === 'admin') {
                // Delete related appointments
                $user->appointment()->delete();

                // Delete related health forms
                $user->form()->delete();

                // You may need to adjust the relationships and method names
                // based on your actual Eloquent relationships and model names.
            }
        });
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function appointment()
    {
        return $this->hasMany(Appointment::class);
    }

    public function form()
    {
        return $this->hasMany(Form::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
