<?php
namespace JSnow\API\Models;

use JSnow\Classes\Model;

class Users extends Model
{

    /**
     * @param array $user
     */
    protected function prepareUser(array &$user)
    {
        $user['id'] = (int)$user['id'];
    }

    /**
     * Возвращает всех пользователей
     *
     * @return array
     */
    public function getUsers(): array
    {
        $stmt = $this->db()->query("SELECT * FROM `users`");

        if($stmt !== false){
            $users = [];
            while(($user = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false){
                $this->prepareUser($user);
                $users[] = $user;
            }

            return $users;
        }

        return [];
    }

    /**
     * Возвращает пользователей, чьё имя совпадает с заданным
     *
     * @param string $name
     *
     * @return array
     */
    public function findUsers(string $name): array
    {
        $stmt = $this->db()->prepare("SELECT * FROM `users` WHERE `name` LIKE :search");

        if($stmt !== false){
            $res = $stmt->execute([':search' => '%' . $name . '%']);
            if($res !== false){

                $users = [];
                while(($user = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false){
                    $this->prepareUser($user);
                    $users[] = $user;
                }

                return $users;
            }
        }

        return [];
    }

    /**
     * Создаёт запись о пользователе. Возвращает ID новосозданного пользователя
     *
     * @param string $name Имя пользователя
     * @param string $phone Номер телефона пользователя
     * @param string $address Адрес пользователя (по-умолчанию нет)
     * @throws \RuntimeException
     * @return int
     */
    public function addUser(string $name, string $phone, string $address = ''): int
    {
        $stmt = $this->db()->prepare("INSERT INTO `users` SET `name`=?, `phone`=?, `address`=?");
        if($stmt !== false){
            $res = $stmt->execute([$name, $phone, $address]);
            if($res){
                return (int)$this->db()->lastInsertId();
            }
        }

        throw new \RuntimeException("При добавлении пользователя произошла непредвиденная ошибка!");
    }

    /**
     * @param int    $user_id
     * @param string $name
     * @param string $phone
     * @param string $address
     * @return bool
     */
    public function updateUser(int $user_id, string $name = '', string $phone = '', string $address = ''): bool
    {
        if($name === '' && $phone === '' && $address === '')
            return true;

        $args = [];
        $sql = "UPDATE `users` SET ";
        if($name !== '')
            $args[] = ['name', $name];
        if($phone !== '')
            $args[] = ['phone', $phone];
        if($address !== '')
            $args[] = ['address', $address];

        $sql .= implode(",", array_map(function($v){
            return $v[0] . '=?';
        }, $args));

        $sql .= " WHERE `id`=?";
        $args[] = ['id', $user_id];

        $stmt = $this->db()->prepare($sql);

        if($stmt !== false){
            $res = $stmt->execute(array_map(function($v){
                return $v[1];
            }, $args));

            if($res)
                return true;
        }

        return false;
    }

}