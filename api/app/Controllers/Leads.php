<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\LeadsModel;

class Leads extends ResourceController
{
    protected $format    = 'json';
    protected $cacheKey = 'leads_list';

    public function __construct()
    {
        $this->model = new LeadsModel();
    }

    public function index()
    {
        $cache = service('cache');
        // retrieve cached leads if exist
        $cachedLeads = $cache->get($this->cacheKey);
        if ($cachedLeads) {
            return $this->respond($cachedLeads);
        }

        try {
            // Get the list of leads from the database with essential fields only
            $builder = $this->model->builder();
            $builder->select('id, first_name, last_name, email, phone, birthdate, created_at, extra');
            $leads = $this->model->findAll();

            // Add the leads to the cache for 1 hour
            $cache->save($this->cacheKey, $leads, 3600);
        } catch (\Exception $e) {
            return $this->fail('An unexpected error happened! Please try again or contact us.', 500);
        }

        return $this->respond($leads);
    }

    public function show($id = null)
    {
        try {
            $lead = $this->model->find($id);
            if ($lead) {
                $lead['extra'] = $lead['extra'] ? json_decode($lead['extra'], true) : null;
                return $this->respond($lead);
            }
        } catch (\Exception $e) {
            return $this->fail('An unexpected error happened! Please try again or contact us.', 500);
        }
        
        return $this->failNotFound('Lead not found');
    }

    public function create()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!empty($data['extra'])) {
            $data['extra'] = json_encode($data['extra']);
        } else {
            $data['extra'] = null;
        }
        
        try {
            if ($this->model->insert($data)) {
                // Clear the cache after creating a new lead
                $cache = service('cache');
                $cachedLeads = $cache->get($this->cacheKey);
                if ($cachedLeads) {
                    $cache->delete($this->cacheKey);
                }

                return $this->respondCreated(['status' => 'success', 'message' => 'Lead created successfully']);
            }
        } catch (\Exception $e) {
            return $this->fail('An unexpected error happened! Please try again or contact us.', 500);
        }

        return $this->failValidationErrors($this->model->errors());
    }

    public function update($id = null)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!empty($data['extra'])) {
            $data['extra'] = json_encode($data['extra']);
        } else {
            $data['extra'] = null;
        }
        
        try {
            $lead = $this->model->find($id);
            if (!$lead) {
                return $this->failNotFound('Lead not found');
            }

            if ($this->model->update($id, $data)) {
                // Clear the cache after updating a lead
                $cache = service('cache');
                $cachedLeads = $cache->get($this->cacheKey);
                if ($cachedLeads) {
                    $cache->delete($this->cacheKey);
                }

                return $this->respond(['status' => 'success', 'message' => 'Lead updated successfully']);
            }
        } catch (\Exception $e) {
            return $this->fail('An unexpected error happened! Please try again or contact us.', 500);
        }

        return $this->failValidationErrors($this->model->errors());
    }

    public function delete($id = null)
    {
        try {
            $lead = $this->model->find($id);
            if (!$lead) {
                return $this->failNotFound('Lead not found');
            }
            
            if ($this->model->delete($id)) {
                // Clear the cache after deleting a lead
                $cache = service('cache');
                $cachedLeads = $cache->get($this->cacheKey);
                if ($cachedLeads) {
                    $cache->delete($this->cacheKey);
                }

                return $this->respondDeleted(['status' => 'success', 'message' => 'Lead deleted successfully']);
            }
        } catch (\Exception $e) {
            return $this->fail('An unexpected error happened! Please try again or contact us.', 500);
        }

        return $this->failNotFound('Lead not found');
    }
}
