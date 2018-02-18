<?php
namespace Database;

use App;

App\Kernel::includePackageFile("App", App\Kernel::PACKAGE_COMPONENT);

class Component extends App\Component
{
    protected $db;
    public static $types = ['Model'];
    protected static $instance;

    function __construct()
    {
        try {
            $this->db = new \PDO(App\Kernel::getConfig('DATABASE_TYPE') . ':host=' . App\Kernel::getConfig('DATABASE_HOST') . ';dbname=' . App\Kernel::getConfig('DATABASE_NAME'), App\Kernel::getConfig('DATABASE_USER'), App\Kernel::getConfig('DATABASE_PASSWORD'));
        } catch (\Exception $e) {
            throw new \Exception("Errore configurazione database", 4);
        }
        parent::__construct();
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
    function __construct($message, $code)
    {
        App\Kernel::implementComponents($this, 'Output');
        parent::__construct($message, $code);
    }
    public function __toString()
    {
        return $this->JSON->reply("Errore nella richiesta al database");
    }
}