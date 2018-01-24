<?php
namespace JSnow;

use JSnow\API\Controllers\Users;
use JSnow\Web\Controllers\WebInterface;
use Simple\Application;
use Simple\Psr4\ClassLoader;

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Путь к текущей директории
define("ROOT_PATH", dirname(__FILE__));
define("SQL_HOST", "");
define("SQL_USER", "");
define("SQL_PASS", "");
define("SQL_DB", "");

include_once((ROOT_PATH . '/Simple/main.php'));

(new ClassLoader())->Append('JSnow', ROOT_PATH . '')->Enable();

// Создание приложения, настройки и т.д.
$phonebook = new Application();

## GET api/users Возвращает всех пользователей
$phonebook->Route('users.get', "/^\/?api\/users\/?$/", 'get', Users::class, 'getUsers');

## POST api/users Добавляет нового пользователя
$phonebook->Route('users.post', "/^\/?api\/users\/?$/", 'post', Users::class, 'addUser');

## GET api/users/search/JohnDoe Пытается найти и вернуть информацию о пользователях с именами похожими на имя в запросе
$phonebook->Route('users.search', "/^\/?api\/users\/search(?:\/(?<userName>.+))?\/$/", 'get', Users::class,
    'findUsers');

## PUT api/users/1 Обновляет данные о пользователе с ID 1
$phonebook->Route('users.put', "/^\/?api\/users\/(?<userId>\-?\d+)?\/?$/", 'put', Users::class, 'updateUser');

## DELETE api/users/1 Удаляет пользователя с ID 1
//$phonebook->Route('users.delete', "/^\/?api\/users\/(?<userId>\d+)\/?$/", 'delete', Users::class, 'deleteUser');

## /
## Web интерфейс
$phonebook->Route("web-interface", "/^\/?$/", "get|post", WebInterface::class, "Phonebook");

$phonebook->Handle($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);