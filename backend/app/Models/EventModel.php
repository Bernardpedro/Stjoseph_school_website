<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'title', 'description', 'date', 'time', 'location', 'type',
        'status', 'organizer', 'youtubeLink', 'images',
    ];
}
