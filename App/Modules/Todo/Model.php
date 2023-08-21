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
            'pattern' => '/^[01]$/',
            'required' => false,
            'default' => 0,
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