<?php

namespace App\Controllers\Api\V1;

use App\Models\DetectionModel;
use CodeIgniter\RESTful\ResourceController;

class StatsController extends ResourceController
{
    protected $format = 'json';

    /**
     * GET /api/v1/stats
     * Estadísticas para el dashboard
     */
    public function index()
    {
        $detectionModel = new DetectionModel();
        $stats = $detectionModel->getStats();

        return $this->respond([
            'status' => 200,
            'data'   => $stats,
        ]);
    }
}
