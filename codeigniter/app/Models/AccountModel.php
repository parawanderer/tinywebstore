<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountModel extends Model
{
    private const ACCOUNT_SELECT = "SELECT a.id, a.username, a.first_name, a.last_name, 
        a.address, a.created, a.password_hash, (s.id is not null) as has_shop,
        s.name as shop_name, s.id as shop_id
        FROM account a 
        LEFT JOIN shop s on a.id = s.user_id
        WHERE a.username = ?";

    private const ACCOUNT_SELECT_BY_ID = "SELECT a.id, a.username, a.first_name, a.last_name, 
        a.address, a.created, a.password_hash, (s.id is not null) as has_shop,
        s.name as shop_name, s.id as shop_id
        FROM account a 
        LEFT JOIN shop s on a.id = s.user_id
        WHERE a.id = ?";

    protected $primaryKey = "id";
    protected $table = 'account';
    protected $allowedFields = [
        'username',
        'password_hash',
        'first_name',
        'last_name',
        'address',
        'created'
    ];

    public function login(string $username, string $password) {
        $query = $this->db->query(AccountModel::ACCOUNT_SELECT, [ $username ]);
        $user = $query->getRowArray();
        
        if (password_verify($password, $user['password_hash'])) {
            unset($user['password_hash']);
        }
        AccountModel::convertTypes($user);
        
        return $user;
    }

    public function register(string $username, string $firstName, string $lastName, string $address, string $password) {
        $registrationTime = date('Y-m-d H:i:s');
        $passHash = AccountModel::hash($password);

        $this->save([
            'username' => $username,
            'password_hash' => $passHash,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'address' => $address,
            'created' => $registrationTime
        ]);

        $id = $this->getInsertID();

        return [
            'id' => $id,
            'username' => $username,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'address' => $address,
            'created' => $registrationTime,
            'has_shop' => false,
        ];
    }

    public function getUser(string $username) {
        $query = $this->db->query(AccountModel::ACCOUNT_SELECT, [ $username ]);
        $user = $query->getRowArray();

        unset($user['password_hash']);
        AccountModel::convertTypes($user);

        return $user;
    }

    public function getUserById(int $userId) {
        $query = $this->db->query(AccountModel::ACCOUNT_SELECT_BY_ID, [ $userId ]);
        $user = $query->getRowArray();

        unset($user['password_hash']);
        AccountModel::convertTypes($user);

        return $user;
    }

    public function accountExists(string $username) {
        return !!$this->where(['username' => $username])->first();
    }

    private static function hash(string $password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    private static function convertTypes(array &$user) {
        $user['id'] = intval($user['id']);
        $user['has_shop'] = intval($user['has_shop']) == 1;
        $user['created'] = strtotime($user['created']);
        $user['shop_id'] = intval($user['shop_id']);
    }
}