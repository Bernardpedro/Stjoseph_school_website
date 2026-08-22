<?php

namespace App\Models;

use CodeIgniter\Model;

class AdmissionRequirementModel extends Model
{
    protected $table = 'admission_requirement_items';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['item_text', 'sort_order', 'is_active'];
}
