<?php

class RefreshTokens extends Base
{

    public string $tableName = 'refresh_tokens';
    public array $fieldNames = ['token_hash', 'expiry'];

    public function __construct(\App\System\Database $db, \Faker\Generator $faker)
    {
        parent::__construct($db, $faker);
    }
}