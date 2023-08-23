<?php

namespace App\Modules\Todo;
use App\System\BaseModel;

class Model extends BaseModel
{
    protected array $fields = [
        'name' => [
            'pattern' => '/^.{3,256}$/',
            'required' => true,
            'default' => '',
            'value' => '',
        ],
        'done' => [
            'pattern' => '/^(0|1|true|false)$/',
            'required' => false,
            'default' => false,
            'value' => '',
        ],
    ];
    protected string $tableName = 'todos';
    protected string $primaryKey = 'id_todo';
    public function __construct()
    {
        parent::__construct();
    }
}