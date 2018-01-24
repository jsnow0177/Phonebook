<?php
namespace JSnow\Classes;

class DB
{

    /**
     * @var null|DB
     */
    private static $db = null;

    /**
     * @var \PDO
     */
    private $_connection;

    /**
     * @return DB
     */
    public static function getInstance()
    {
        if(is_null(self::$db)){
            self::$db = new self();
        }

        return self::$db;
    }

    /**
     * @return \PDO
     */
    public static function connection()
    {
        return self::getInstance()->getConnection();
    }

    /**
     * DB constructor.
     */
    private function __construct()
    {
        $this->_connection =
            new \PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB . ';charset=UTF8', SQL_USER, SQL_PASS, [
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
            ]);
    }

    private function __clone()
    {
    }

    /**
     * @return \PDO
     */
    public function getConnection()
    {
        return $this->_connection;
    }

}