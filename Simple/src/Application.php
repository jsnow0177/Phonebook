<?php
namespace Simple;

use Simple\Http\Http;
use Simple\Http\Request;
use Simple\Http\Response;

class Application
{

    /**
     * HTTP-методы маршрута не заданы
     */
    const ERROR_HTTP_METHODS_NOT_SPECIFIED = 1;

    /**
     * Указаны неизвестные HTTP-методы маршрута
     */
    const ERROR_UNSUPPORTED_HTTP_METHODS_SPECIFIED = 2;

    /**
     * @var array
     */
    private $routes = [];

    /**
     * @param string $name Имя маршрута
     * @param string $pathPattern
     * @param string $httpMethods
     * @param string $targetClass
     * @param string $targetMethod
     *
     * @throws \InvalidArgumentException
     * @return \Simple\Application
     */
    public function Route(string $name, string $pathPattern, string $httpMethods, string $targetClass, string $targetMethod): Application
    {
        if($httpMethods === '')
            throw new \InvalidArgumentException("Поддерживаемые HTTP-методы маршрута не заданы.",
                self::ERROR_HTTP_METHODS_NOT_SPECIFIED);

        $httpMethods = array_filter(explode("|", strtolower($httpMethods)), function(string $httpMethod){
            return ($httpMethod !== '' && in_array($httpMethod, Http::METHODS));
        });

        if(count($httpMethods) < 1)
            throw new \InvalidArgumentException("Указаны неизвестные или неподдерживаемые HTTP-методы для маршрута.",
                self::ERROR_UNSUPPORTED_HTTP_METHODS_SPECIFIED);

        if(!isset($this->routes[ $name ])){
            $this->routes[ $name ] = [
                'methods'      => [],
                'pattern'      => '',
                'targetClass'  => '',
                'targetMethod' => ''
            ];
        }

        $this->routes[ $name ]['methods'] += $httpMethods;
        $this->routes[ $name ]['pattern'] = $pathPattern;
        $this->routes[ $name ]['targetClass'] = $targetClass;
        $this->routes[ $name ]['targetMethod'] = $targetMethod;

        return $this;
    }

    /**
     * Обрабатывает запрос
     *
     * @param string $path
     * @param string $method
     *
     * @throws \RuntimeException
     */
    public function Handle(string $path, string $method)
    {
        if(($pos = mb_strpos($path, "?")) !== false){
            $path = mb_substr($path, 0, $pos);
        }
        $path = urldecode($path);

        $request = new Request();

        try{
            $request->setHttpMethod($method);
        }catch(\Exception $ex){
            throw new \RuntimeException("Ошибка при построении объекта запроса!", 0, $ex);
        }

        $request->setRequestPath($path);

        // Перебираем все маршруты и выбираем первый подходящий по поддерживаемым методам
        foreach($this->routes as $routeInfo){
            if(in_array($request->getHttpMethod(), $routeInfo['methods'])){

                // Проверяем, подходит ли шаблон маршрута нашему пути
                if(preg_match($routeInfo['pattern'], $request->getRequestPath(), $m)){
                    if($method === Http::GET || $method === Http::POST){
                        $request->setArguments(($method === Http::GET ? $_GET : $_POST));
                    }else{
                        // Если тип запроса не GET и не POST, тогда нужно несколько иначе получить тело запроса
                        $data = file_get_contents("php://input");
                        if($data === false)
                            throw new \RuntimeException("Ошибка при получении тела запроса!", 1);

                        parse_str($data, $args);
                        $request->setArguments($args);
                    }

                    $m = array_filter($m, function($k){
                        return !is_numeric($k);
                    }, ARRAY_FILTER_USE_KEY);
                    $request->setArguments($m);

                    // Вызываем контроллер
                    try{
                        $this->call($routeInfo['targetClass'], $routeInfo['targetMethod'], $request);
                    }catch(\RuntimeException $ex){
                        //TODO: Обработать ошибку
                    }

                    break;
                }
            }
        }

        throw new \RuntimeException("Не удалось найти целевой класс и метод!", 404);
    }

    /**
     * @param string               $targetClass
     * @param string               $targetMethod
     * @param \Simple\Http\Request $request
     *
     * @throws \RuntimeException
     */
    protected function call(string $targetClass, string $targetMethod, Request $request)
    {
        if(class_exists($targetClass, true)){
            $targetObj = new $targetClass();
            if(method_exists($targetObj, $targetMethod)){
                $response = new Response();
                $targetObj->{$targetMethod}($request, $response);
            }else{
                throw new \RuntimeException("Метод " . $targetMethod . " класса " . $targetClass . " не найден!", 2);
            }
        }else{
            throw new \RuntimeException("Класс " . $targetClass . " не найден!", 1);
        }
    }

}