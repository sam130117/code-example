<?php

namespace App;

use App\Models\Cases;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $perPage = 20;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /* Relations */

    public function cases()
    {
        return $this->hasMany(Cases::getTableName(), 'user_id');
    }

    /* Getters & Setters */

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

}
