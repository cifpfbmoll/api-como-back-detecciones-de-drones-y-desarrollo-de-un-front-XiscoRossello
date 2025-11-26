<?php

namespace App\Models;

use CodeIgniter\Model;

class DetectionModel extends Model
{
    protected $table            = 'detections';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['mac_address', 'manufacturer_id', 'rssi', 'sensor_location', 'detected_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = false; // No updated_at for detections

    // Validation
    protected $validationRules = [
        'mac_address'     => 'required|max_length[17]',
        'rssi'            => 'required|integer',
        'sensor_location' => 'required|max_length[255]',
        'detected_at'     => 'required',
    ];

    protected $validationMessages = [
        'mac_address' => [
            'required' => 'La dirección MAC es obligatoria.',
        ],
        'rssi' => [
            'required' => 'El valor RSSI es obligatorio.',
            'integer'  => 'El valor RSSI debe ser un número entero.',
        ],
        'sensor_location' => [
            'required' => 'La ubicación del sensor es obligatoria.',
        ],
        'detected_at' => [
            'required' => 'La fecha de detección es obligatoria.',
        ],
    ];

    /**
     * Get detections with manufacturer info
     */
    public function getWithManufacturer(int $limit = 20, int $offset = 0, array $filters = []): array
    {
        $builder = $this->select('detections.*, manufacturers.name as manufacturer_name, manufacturers.oui')
                        ->join('manufacturers', 'manufacturers.id = detections.manufacturer_id', 'left')
                        ->orderBy('detections.detected_at', 'DESC');

        if (!empty($filters['manufacturer_id'])) {
            $builder->where('detections.manufacturer_id', $filters['manufacturer_id']);
        }

        if (!empty($filters['location'])) {
            $builder->like('detections.sensor_location', $filters['location']);
        }

        return $builder->findAll($limit, $offset);
    }

    /**
     * Count total detections with filters
     */
    public function countWithFilters(array $filters = []): int
    {
        $builder = $this->builder();

        if (!empty($filters['manufacturer_id'])) {
            $builder->where('manufacturer_id', $filters['manufacturer_id']);
        }

        if (!empty($filters['location'])) {
            $builder->like('sensor_location', $filters['location']);
        }

        return $builder->countAllResults();
    }

    /**
     * Get latest detections
     */
    public function getLatest(int $limit = 5): array
    {
        return $this->select('detections.*, manufacturers.name as manufacturer_name, manufacturers.oui')
                    ->join('manufacturers', 'manufacturers.id = detections.manufacturer_id', 'left')
                    ->orderBy('detections.detected_at', 'DESC')
                    ->findAll($limit);
    }

    /**
     * Get statistics for dashboard
     */
    public function getStats(): array
    {
        $totalDetections = $this->countAll();
        $knownDrones = $this->where('manufacturer_id IS NOT NULL')->countAllResults();
        $unknownDevices = $this->where('manufacturer_id IS NULL')->countAllResults();

        // Get top manufacturer
        $topManufacturer = $this->select('manufacturers.name, COUNT(*) as count')
                                ->join('manufacturers', 'manufacturers.id = detections.manufacturer_id')
                                ->where('detections.manufacturer_id IS NOT NULL')
                                ->groupBy('detections.manufacturer_id')
                                ->orderBy('count', 'DESC')
                                ->first();

        return [
            'total_detections'      => $totalDetections,
            'known_drones_count'    => $knownDrones,
            'unknown_devices_count' => $unknownDevices,
            'top_manufacturer'      => $topManufacturer['name'] ?? null,
        ];
    }
}
