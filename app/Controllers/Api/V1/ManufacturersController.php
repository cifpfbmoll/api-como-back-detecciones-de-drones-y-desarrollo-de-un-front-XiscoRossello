<?php

namespace App\Controllers\Api\V1;

use App\Models\ManufacturerModel;
use CodeIgniter\RESTful\ResourceController;

class ManufacturersController extends ResourceController
{
    protected $modelName = ManufacturerModel::class;
    protected $format    = 'json';

    /**
     * GET /api/v1/manufacturers
     * Lista de todos los fabricantes de drones
     */
    public function index()
    {
        $manufacturers = $this->model->orderBy('name', 'ASC')->findAll();

        return $this->respond([
            'status' => 200,
            'data'   => $manufacturers,
        ]);
    }
}
