<?php
namespace JSnow\Classes;

abstract class Model
{

    /**
     * @var Model[]
     */
    private static $instances = [];

    /**
     * @return $this
     */
    public static function instance(): Model
    {
        if(!isset(self::$instances[ static::class ]) || is_null(self::$instances[ static::class ]))
            self::$instances[ static::class ] = new static();

        return self::$instances[ static::class ];
    }

    protected function __construct()
    {
    }

    protected function __clone()
    {

    }

    /**
     * @return \PDO
     */
    protected function db(): \PDO
    {
        return DB::getInstance()->getConnection();
    }

}