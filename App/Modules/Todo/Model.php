<?php

namespace App\Modules\Todo;

use App\System\BaseModel;

class Model extends BaseModel
{
    protected array $fields = [
        'name' => [
            'pattern' => '/^.{3,256}$/',
            'required' => true,
        ],
        'done' => [
            'pattern' => '/^(0|1|true|false)$/',
            'required' => false,
            'is_bool' => true
        ],
        'id_user' => [
            'pattern' => '/^\\d+$/',
            'required' => false,
        ],
    ];
    protected string $tableName = 'todos';
    protected string $primaryKey = 'id_todo';
    public function __construct()
    {
        parent::__construct();
    }
}