<?php

namespace App\Modules\Todo;
use App\System\BaseModel;

class Model extends BaseModel
{
    protected string $tableName = 'todos';
    protected string $primaryKey = 'id_todo';
    public function __construct()
    {
        parent::__construct();
    }
}