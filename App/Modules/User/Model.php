<?php

namespace App\Modules\User;

use App\System\BaseModel;

class Model extends BaseModel
{
    protected array $fields = [
        'name' => [
            'pattern' => '/^.{3,256}$/',
            'required' => true,
            'default' => '',
        ],
        'username' => [
            'pattern' => '/^.{3,256}$/',
            'required' => true,
            'default' => '',
        ],
        'password' => [
            'pattern' => '/^.{64,64}$/',
            'required' => true,
            'default' => '',
        ],
    ];
    protected string $tableName = 'users';
    protected string $primaryKey = 'id_user';
    public function __construct()
    {
        parent::__construct();
    }
}