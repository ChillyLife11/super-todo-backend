<?php

namespace App\Modules\Todo;

use App\System\BaseController;
use App\System\BaseModel;

class Controller extends BaseController
{
    public BaseModel $model;
    public function __construct()
    {
        $this->checkAuth();
        $this->model = new Model();
    }
}