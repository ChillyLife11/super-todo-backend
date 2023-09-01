<?php

namespace App\Modules\User;

use App\System\BaseController;
use App\System\BaseModel;

class Controller extends BaseController
{
    protected BaseModel $model;
    public function __construct()
    {
        $this->model = new Model();
    }
}