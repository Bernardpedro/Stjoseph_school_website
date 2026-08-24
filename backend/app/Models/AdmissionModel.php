<?php

namespace App\Models;

use CodeIgniter\Model;

class AdmissionModel extends Model
{
    protected $table = 'admissions';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'registration_number', 'student_name', 'date_of_birth', 'gender', 'level', 'program',
        'previous_school', 'parent_name', 'parent_phone', 'parent_email',
        'province', 'district', 'address', 'message', 'documents', 'status',
    ];
}
