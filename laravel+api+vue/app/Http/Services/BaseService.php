<?php

namespace App\Http\Services;


use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class BaseService
{
    const MODEL_NAME = null;

    public function getById($id)
    {
        if (static::MODEL_NAME) {
            $instance = (static::MODEL_NAME)::where('id', $id)->first();
            if (!$instance)
                throw new ModelNotFoundException();
            return $instance;
        }
        return null;
    }

    public function getAll()
    {
        if (static::MODEL_NAME)
            return (static::MODEL_NAME)::paginate();
        return null;
    }

    public function deleteById($id)
    {
        if (static::MODEL_NAME)
            return (static::MODEL_NAME)::where('id', $id)->delete();
        return null;
    }

    public function updateById($id, array $data)
    {
        if (static::MODEL_NAME)
            return (static::MODEL_NAME)::where('id', $id)->update($data);
        return null;
    }
}
