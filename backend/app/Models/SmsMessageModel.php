<?php

namespace App\Models;

use CodeIgniter\Model;

class SmsMessageModel extends Model
{
    protected $table = 'sms_messages';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'recipient', 'sender_id', 'message', 'status',
        'provider_message_id', 'provider_response', 'error_message', 'sent_by',
    ];
}
