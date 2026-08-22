<?php

namespace App\Models;

use CodeIgniter\Model;

class RequirementLevelModel extends Model
{
    protected $table = 'requirement_levels';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['code', 'name', 'description', 'sort_order', 'urls'];
}
