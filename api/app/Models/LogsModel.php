<?php 

namespace App\Models;

use CodeIgniter\Model;

class LogsModel extends Model
{
    protected $table = 'logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['ip', 'uri', 'method', 'headers', 'body', 'response', 'status', 'date'];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $validationRules = [
        'ip' => 'required|valid_ip',
        'uri' => 'required',
        'method' => 'required',
        'headers' => 'required',
        'body' => 'valid_json',
        'response' => 'permit_empty|valid_json',
        'status' => 'permit_empty|integer',
    ];
}