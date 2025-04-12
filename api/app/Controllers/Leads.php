<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\LeadsModel;

class Leads extends ResourceController
{
    protected $format    = 'json';

    public function index()
    {
        try {
            $leadsModel = new LeadsModel();
            $leads = $leadsModel->findAll();
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
        return $this->respond($leads);
    }

    public function show($id = null)
    {
        $lead = $this->model->find($id);
        if ($lead) {
            return $this->respond($lead);
        }
        return $this->failNotFound('Lead not found');
    }

    public function create()
    {
        $data = $this->request->getPost();
        if ($this->model->insert($data)) {
            return $this->respondCreated(['status' => 'success', 'message' => 'Lead created successfully']);
        }
        return $this->failValidationErrors($this->model->errors());
    }
}
