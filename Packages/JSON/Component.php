<?php
namespace JSON;

use App;

class Component
{
    public static $types = ['Output'];
    
    const CODE_SUCCESS = 0;
    const CODE_MISSING_ROUTE = 1;
    const CODE_MISSING_PARAMETER = 2;
    const CODE_WRONG_DATABASE_CONFIGURATION = 3;
    const CODE_WRONG_DATABASE_QUERY = 4;

    public function reply($message, $code = self::CODE_SUCCESS)
    {
        header('Content-Type: application/json');
        echo json_encode(['code' => $code, 'data' => $message]);
    }
}