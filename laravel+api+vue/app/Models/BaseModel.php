<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $perPage = 20;

    public static function getTableName()
    {
        return (new static())->getTable();
    }

}
