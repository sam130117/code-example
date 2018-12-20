<?php

namespace App\Models;


use App\User;

class Cases extends BaseModel
{
    protected $perPage = 15;
    protected $fillable = [
        'title',
        'client_email',
        'website',
        'country',
        'user_id',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /* Relations */

    public function cards()
    {
        return $this->hasMany(Cards::class, 'case_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
