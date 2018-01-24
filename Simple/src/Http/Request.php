<?php
namespace Simple\Http;

/**
 * @package Simple\Http
 */
class Request
{

    /**
     * @var string
     */
    private $httpMethod = Http::GET;

    /**
     * @var string
     */
    private $requestPath = '/';

    /**
     * @var array
     */
    private $arguments = [];

    /**
     * @return string
     */
    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    /**
     * @param string $method
     * @throws \InvalidArgumentException
     */
    public function setHttpMethod(string $method)
    {
        $method = strtolower($method);
        if(!in_array($method, Http::METHODS))
            throw new \InvalidArgumentException("Неподдерживаемый HTTP-метод задан объекту запроса!");

        $this->httpMethod = $method;
    }

    /**
     * @return string
     */
    public function getRequestPath(): string
    {
        return $this->requestPath;
    }

    /**
     * @param string $requestPath
     */
    public function setRequestPath(string $requestPath)
    {
        $this->requestPath = preg_replace("/\/{2,}/", '/', '/' . $requestPath . '/');
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = array_replace($this->arguments, $arguments);
    }

    /**
     * @param string     $name
     * @param mixed|null $default
     * @return mixed|null
     */
    public function getArg(string $name, $default = null)
    {
        if(isset($this->arguments[ $name ]))
            return $this->arguments[ $name ];

        return $default;
    }

}