<?php

namespace App\Models;

use Carbon\Carbon;

class Cards extends BaseModel
{
    const TYPE_CREDIT = 'credit';
    const TYPE_DEBIT = 'debit';
    const TYPES = [self::TYPE_DEBIT, self::TYPE_CREDIT];

    protected $table = 'cards';

    protected $fillable = [
        'name',
        'last_number',
        'total_value',
        'card_type',
        'close_date',
        'case_id',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'close_date',
    ];

    /* Getters & Setters */

    public function getCloseDateAttribute($value)
    {
        return (new Carbon($value))->format('Y-m-d');
    }

    /* Relations */

    public function case()
    {
        return $this->belongsTo(Cases::class, 'case_id');
    }
}
