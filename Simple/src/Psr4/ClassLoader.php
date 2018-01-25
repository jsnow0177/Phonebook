<?php
namespace Simple\Psr4;

/**
 * Класс автозагрузчика классов. Поддерживает PSR-4
 *
 * @package Simple\Psr4
 */
class ClassLoader
{

    /**
     * @var null|\Simple\Psr4\ClassLoader
     */
    private static $lastInstance = null;

    /**
     * @var array
     */
    private $map = [];

    /**
     * @var bool
     */
    private $registered = false;

    /**
     * Возвращает последний созданный объект автозагрузчика классов
     *
     * @return null|\Simple\Psr4\ClassLoader
     */
    public static function getLast():?ClassLoader
    {
        return self::$lastInstance;
    }

    /**
     * ClassLoader constructor.
     */
    public function __construct()
    {
        $this->map = [];
        $this->registered = false;
        self::$lastInstance = $this;
    }

    /**
     * @param string   $prefix
     * @param string[] ...$basepath
     *
     * @return $this
     */
    public function Prepend(string $prefix, string ...$basepath): ClassLoader
    {
        $prefix = trim($prefix, '\\');
        if(!isset($this->map[ $prefix ]))
            $this->map[ $prefix ] = [];

        foreach($basepath as $path){
            if(!in_array($path, $this->map[ $prefix ], true)){
                array_unshift($this->map[ $prefix ], $path);
            }
        }

        return $this;
    }

    /**
     * @param string   $prefix
     * @param string[] ...$basepath
     *
     * @return $this
     */
    public function Append(string $prefix, string ...$basepath): ClassLoader
    {
        $prefix = trim($prefix, '\\');
        if(!isset($this->map[ $prefix ]))
            $this->map[ $prefix ] = [];

        foreach($basepath as $path){
            if(!in_array($path, $this->map[ $prefix ], true)){
                $this->map[ $prefix ][] = $path;
            }
        }

        return $this;
    }

    /**
     * Включает загрузчик классов
     *
     * @return $this
     */
    public function Enable(): ClassLoader
    {
        if(!$this->registered){
            $res = spl_autoload_register([$this, 'Load'], false);
            $this->registered = $res;
        }

        return $this;
    }

    /**
     * Отключает загрузчик классов
     *
     * @return $this
     */
    public function Disable(): ClassLoader
    {
        if($this->registered){
            $res = spl_autoload_unregister([$this, 'loadClass']);
            $this->registered = $res;
        }

        return $this;
    }

    /**
     * Выполняет загрузку класса
     *
     * @param string $classname Полное имя класса
     *
     * @return bool
     */
    public function Load(string $classname): bool
    {
        $classname = trim($classname, '\\');
        $prefix = $classname;
        while(($slashPos = strrpos($prefix, '\\')) !== false){
            $prefix = substr($prefix, 0, $slashPos);
            $relative_classname = substr($classname, $slashPos + 1);
            foreach($this->map as $_prefix => $paths){
                if($prefix === $_prefix){
                    foreach($paths as $basepath){
                        $filename = rtrim($basepath, '/') . '/' . str_replace('\\', '/', $relative_classname) . '.php';
                        if($this->tryLoadFile($filename)){
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param string $filename
     *
     * @return bool
     */
    protected function tryLoadFile(string $filename): bool
    {
        if(file_exists($filename)){
            require_once($filename);

            return true;
        }

        return false;
    }

}
