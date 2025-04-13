<?php 

namespace App\Models;

use CodeIgniter\Model;

class LeadsModel extends Model
{
    protected $table = 'leads';
    protected $primaryKey = 'id';
    protected $allowedFields = ['first_name', 'last_name', 'email', 'phone', 'birthdate', 'extra', 'created_at'];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $validationRules = [
        'first_name' => 'required|min_length[2]|max_length[100]',
        'last_name'  => 'required|min_length[2]|max_length[100]',
        'email'      => 'required|valid_email|is_unique[leads.email]',
        'phone'      => 'required|min_length[10]|max_length[20]',
        'birthdate'  => 'required|valid_date[Y-m-d]',
        'extra'      => 'permit_empty|valid_json',
    ];
    protected $validationMessages = [
        'email' => [
            'is_unique' => 'There is already a lead register with this email account.',
        ],
        'extra' => [
            'valid_json' => 'The extra lead information must be a valid JSON string.',
        ],
    ];
}