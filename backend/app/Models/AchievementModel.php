<?php

namespace App\Models;

use CodeIgniter\Model;

class AchievementModel extends Model
{
    protected $table = 'achievements';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'title', 'category', 'academic_year', 'score', 'rank', 'is_published', 'is_featured',
    ];
}
