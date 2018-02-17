<?php

namespace App;

class Response {
    const CODE_SUCCESS = 0;
    const CODE_MISSING_ROUTE = 1;
    const CODE_MISSING_PARAMETER = 2;
    const CODE_WRONG_DATABASE_CONFIGURATION = 3;
    const CODE_WRONG_DATABASE_QUERY = 4;
    
    static $instance;
    static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function reply($message, $code = self::CODE_SUCCESS)
    {
        header('Content-Type: application/json');
        echo json_encode(['code' => $code, 'data' => $message]);
        die();
    }
}