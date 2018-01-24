<?php
namespace Simple\Http;

/**
 * @package Simple
 */
final class Http
{

    const HEAD    = 'head';
    const GET     = 'get';
    const POST    = 'post';
    const PUT     = 'put';
    const DELETE  = 'delete';
    const OPTIONS = 'options';
    const METHODS = [self::HEAD, self::GET, self::POST, self::PUT, self::DELETE, self::OPTIONS];

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

}