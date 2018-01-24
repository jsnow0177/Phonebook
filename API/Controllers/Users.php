<?php
namespace JSnow\API\Controllers;

use Simple\Http\Request;
use Simple\Http\Response;

class Users
{

    /**
     * GET api/users
     *
     * @param \Simple\Http\Request  $request
     * @param \Simple\Http\Response $response
     */
    public function getUsers(Request $request, Response $response)
    {
        $users = \JSnow\API\Models\Users::instance();

        try{
            $usersList = $users->getUsers();
            $response->Success([
                'items' => $usersList,
                'count' => count($usersList)
            ]);
        }catch(\RuntimeException $ex){
            $response->Error($ex->getCode(), $ex->getMessage());
        }
    }

    /**
     * GET api/users/search/<user_name>
     *
     * @param \Simple\Http\Request  $request
     * @param \Simple\Http\Response $response
     */
    public function findUsers(Request $request, Response $response)
    {
        if(is_null($userName = $request->getArg('userName'))){
            $response->Error(0, 'Для поиска необходимо указать имя искомого пользователя!');
        }

        $users = \JSnow\API\Models\Users::instance();

        try{
            $usersList = $users->findUsers($userName);
            $response->Success([
                'items' => $usersList,
                'count' => count($usersList)
            ]);
        }catch(\RuntimeException $ex){
            $response->Error($ex->getCode(), $ex->getMessage());
        }
    }

    /**
     * POST api/users
     *
     * Создаёт пользователя.
     *
     * @param \Simple\Http\Request  $request
     * @param \Simple\Http\Response $response
     */
    public function addUser(Request $request, Response $response)
    {
        $args = $request->getArguments();
        if(!isset($args['name']) || empty($args['name']))
            $response->Error(1, 'Необходимо указать имя пользователя!');

        if(!isset($args['phone']) || empty($args['phone']))
            $response->Error(2, 'Необходимо указать номер телефона пользователя!');

        $users = \JSnow\API\Models\Users::instance();

        try{
            $userId = $users->addUser($args['name'], $args['phone'], (isset($args['address']) ? $args['address'] : ''));
            $response->Success([
                'id' => $userId
            ]);
        }catch(\RuntimeException $ex){
            $response->Error($ex->getCode(), $ex->getMessage());
        }
    }

    /**
     * PUT api/users/<user_id>
     *
     * @param \Simple\Http\Request  $request
     * @param \Simple\Http\Response $response
     */
    public function updateUser(Request $request, Response $response)
    {
        $args = $request->getArguments();
        if(!isset($args['userId']) || empty($args['userId']))
            $response->Error(0, "Необходим ID пользователя!");

        $name = (isset($args['name']) ? $args['name'] : '');
        $phone = (isset($args['phone']) ? $args['phone'] : '');
        $address = (isset($args['address']) ? $args['address'] : '');

        $users = \JSnow\API\Models\Users::instance();

        $res = $users->updateUser((int)$args['userId'], $name, $phone, $address);
        if($res === false)
            $response->Error(1, "Не удалось обновить пользователя с ID " . $args['userId']);

        $response->Success(1);
    }

}