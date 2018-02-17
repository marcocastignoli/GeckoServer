<?php

namespace App;

class Database
{
    protected $db;
    static $instance;

    function __construct()
    {
        try {
            $this->db = new \PDO(Kernel::getConfig('DATABASE_TYPE') . ':host=' . Kernel::getConfig('DATABASE_HOST') . ';dbname=' . Kernel::getConfig('DATABASE_NAME'), Kernel::getConfig('DATABASE_USER'), Kernel::getConfig('DATABASE_PASSWORD'));
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage(), (int)$e->getCode());
        }
    }

    static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function query($query, $parameters = array())
    {
        $query = $this->db->prepare($query);
        $query->execute($parameters);
        $error = $query->errorInfo();
        if ($error[0] !== "00000") {
            throw new DatabaseException($error[2], $error[0]);
        }
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }
}

class DatabaseException extends \Exception
{
    public function __toString()
    {
        return Kernel::getResponse()->reply("Errore nella richiesta al database", Response::CODE_WRONG_DATABASE_QUERY);
    }
}