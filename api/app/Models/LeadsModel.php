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
        'first_name' => 'required|min_length[2]|max_length[50]',
        'last_name'  => 'required|min_length[2]|max_length[50]',
        'email'      => 'required|valid_email|is_unique[leads.email]',
        'phone'      => 'required|min_length[10]|max_length[15]',
        'birthdate'  => 'required|valid_date[Y-m-d]',
        'extra'      => 'permit_empty|json',
    ];

    public function getLeads($id = null)
    {
        if ($id) {
            return $this->where('id', $id)->first();
        }
        return $this->findAll();
    }

    public function createLead($data)
    {
        return $this->insert($data);
    }
}