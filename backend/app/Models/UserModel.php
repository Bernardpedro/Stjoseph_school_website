<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = false;

    protected $returnType = 'array';

    protected $allowedFields = [
        'id',
        'firstName',
        'lastName',
        'email',
        'phone',
        'password',
        'roleId',
        'role',
        'status',
        'last_login',
    ];

    protected $useTimestamps = true;
}
