<?php

namespace App\Http\Repositories;


use App\Exceptions\ServiceException;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class BaseRepository
{
    protected $model = null;

    /**
     * BaseRepository constructor.
     * @throws ServiceException
     */
    public function __construct()
    {
        $modelName = $this->model();

        if (!class_exists($modelName))
            throw new ServiceException("Class {$modelName} doesn't exists.");

        $model = new $modelName();

        if (!$model instanceof BaseModel)
            throw new ServiceException("Class {$modelName} must be an instance of " . BaseModel::class . '.');
        $this->model = $model;
    }

    public function getById($id)
    {
        $instance = $this->model::where('id', $id)->first();
        if (!$instance)
            throw new ModelNotFoundException();
        return $instance;
    }

    public function getAll()
    {
        if ($this->model)
            return $this->model::paginate();
        return null;
    }

    public function deleteById($id)
    {
        if ($this->model)
            return $this->model::where('id', $id)->delete();
        return null;
    }

    public function updateById($id, array $data)
    {
        if ($this->model)
            return $this->model::where('id', $id)->update($data);
        return null;
    }

    public function create(array $data)
    {
        return $this->model::create($data);
    }

    abstract function model(): string;
}
