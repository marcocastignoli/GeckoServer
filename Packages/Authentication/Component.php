<?php
namespace Authentication;

use App;
use \Firebase\JWT\JWT;

App\Kernel::includePackageFile('Authentication', 'lib/JWT');

class Component extends App\Model
{
    public static $types = ['Request'];

    private static $privateKey;

    private static $user;

    function __construct()
    {
        self::$privateKey = App\Kernel::getConfig("JWT_PRIVATE_KEY");
        App\Kernel::implementComponents($this, 'Output');
        parent::__construct();
    }
    public function checkToken($token = false)
    {
        try {
            $decoded = JWT::decode($token, self::$privateKey, array('HS256'));
            self::$user = $decoded->usr;
            return true;
        } catch (\Exception $e) {
            $this->JSON->reply("Not authorized");
            die();
        }
    }
    public function getToken($username, $password)
    {
        $res = $this->PDO->query("SELECT user_id FROM users WHERE username = :username AND password = :password", [
            "username" => $username,
            "password" => $password
        ]);
        if (count($res) > 0) {
            $token = array(
                "exp" => strtotime('+1 day'),
                "usr" => $res[0]['user_id']
            );
            return JWT::encode($token, self::$privateKey);
        } else {
            return false;
        }
    }
    public function user()
    {
        return self::$user;
    }
}