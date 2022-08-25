<?php

namespace App\Models;

use CodeIgniter\Model;

class AlertModel extends Model
{
    protected $primaryKey = "id";
    protected $table = 'alert';
    protected $allowedFields = [
        'id',
        'user_id',
        'timestamp',
        'seen',
        'subject_id',
        'type'
    ];
}