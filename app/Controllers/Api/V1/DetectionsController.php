<?php

namespace App\Controllers\Api\V1;

use App\Models\DetectionModel;
use App\Models\ManufacturerModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class DetectionsController extends ResourceController
{
    protected $modelName = DetectionModel::class;
    protected $format    = 'json';

    /**
     * POST /api/v1/detections
     * Registra una nueva detección
     */
    public function create()
    {
        $json = $this->request->getJSON(true);

        // Validation rules
        $rules = [
            'mac'             => 'required|regex_match[/^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$/]',
            'rssi'            => 'required|integer',
            'sensor_location' => 'required|max_length[255]',
            'timestamp'       => 'required|valid_date[Y-m-d\TH:i:s\Z]|valid_date[Y-m-d\TH:i:sP]|valid_date[Y-m-d H:i:s]',
        ];

        $messages = [
            'mac' => [
                'required'    => 'El campo MAC es obligatorio.',
                'regex_match' => 'El formato de la dirección MAC no es válido. Use el formato XX:XX:XX:XX:XX:XX.',
            ],
            'rssi' => [
                'required' => 'El campo RSSI es obligatorio.',
                'integer'  => 'El valor RSSI debe ser un número entero.',
            ],
            'sensor_location' => [
                'required'   => 'El campo sensor_location es obligatorio.',
                'max_length' => 'La ubicación del sensor no puede exceder 255 caracteres.',
            ],
            'timestamp' => [
                'required'   => 'El campo timestamp es obligatorio.',
                'valid_date' => 'El formato del timestamp no es válido. Use formato ISO 8601.',
            ],
        ];

        // Custom validation for timestamp (allow multiple ISO 8601 formats)
        $timestampValid = false;
        if (isset($json['timestamp'])) {
            $timestamp = $json['timestamp'];
            // Try different ISO 8601 formats
            $formats = ['Y-m-d\TH:i:s\Z', 'Y-m-d\TH:i:sP', 'Y-m-d\TH:i:s.u\Z', 'Y-m-d\TH:i:s'];
            foreach ($formats as $format) {
                $dt = \DateTime::createFromFormat($format, $timestamp);
                if ($dt !== false) {
                    $timestampValid = true;
                    break;
                }
            }
        }

        // Validate required fields
        $errors = [];
        
        if (empty($json['mac'])) {
            $errors['mac'] = $messages['mac']['required'];
        } elseif (!preg_match('/^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$/', $json['mac'])) {
            $errors['mac'] = $messages['mac']['regex_match'];
        }

        if (!isset($json['rssi'])) {
            $errors['rssi'] = $messages['rssi']['required'];
        } elseif (!is_int($json['rssi']) && !ctype_digit(strval($json['rssi'])) && !preg_match('/^-?\d+$/', strval($json['rssi']))) {
            $errors['rssi'] = $messages['rssi']['integer'];
        }

        if (empty($json['sensor_location'])) {
            $errors['sensor_location'] = $messages['sensor_location']['required'];
        } elseif (strlen($json['sensor_location']) > 255) {
            $errors['sensor_location'] = $messages['sensor_location']['max_length'];
        }

        if (empty($json['timestamp'])) {
            $errors['timestamp'] = $messages['timestamp']['required'];
        } elseif (!$timestampValid) {
            $errors['timestamp'] = $messages['timestamp']['valid_date'];
        }

        if (!empty($errors)) {
            return $this->respond([
                'status'  => 400,
                'error'   => 'Bad Request',
                'messages' => $errors,
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Extract OUI (first 3 octets of MAC address)
        $macAddress = strtoupper($json['mac']);
        $oui = substr($macAddress, 0, 8); // Format: XX:XX:XX

        // Look for manufacturer by OUI
        $manufacturerModel = new ManufacturerModel();
        $manufacturer = $manufacturerModel->findByOui($oui);

        // Parse timestamp
        $detectedAt = date('Y-m-d H:i:s', strtotime($json['timestamp']));

        // Prepare detection data
        $detectionData = [
            'mac_address'     => $macAddress,
            'rssi'            => intval($json['rssi']),
            'sensor_location' => $json['sensor_location'],
            'detected_at'     => $detectedAt,
            'created_at'      => date('Y-m-d H:i:s'),
        ];
        
        // Only add manufacturer_id if found
        if ($manufacturer) {
            $detectionData['manufacturer_id'] = intval($manufacturer['id']);
        }

        // Use db builder directly to avoid CI4 model issues
        $db = \Config\Database::connect();
        $builder = $db->table('detections');
        $builder->insert($detectionData);
        $detectionId = $db->insertID();

        if (!$detectionId) {
            return $this->respond([
                'status'  => 500,
                'error'   => 'Internal Server Error',
                'messages' => ['database' => 'Error al guardar la detección.'],
            ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Get the created detection
        $detection = $this->model->find($detectionId);
        $detection['manufacturer_name'] = $manufacturer ? $manufacturer['name'] : null;

        return $this->respond([
            'status'  => 201,
            'message' => 'Detección registrada correctamente.',
            'data'    => $detection,
        ], ResponseInterface::HTTP_CREATED);
    }

    /**
     * GET /api/v1/detections
     * Lista paginada de detecciones
     */
    public function index()
    {
        $page = (int) ($this->request->getGet('page') ?? 1);
        $limit = (int) ($this->request->getGet('limit') ?? 20);
        $manufacturerId = $this->request->getGet('manufacturer_id');
        $location = $this->request->getGet('location');

        // Ensure valid pagination values
        $page = max(1, $page);
        $limit = max(1, min(100, $limit)); // Max 100 per page
        $offset = ($page - 1) * $limit;

        // Build filters
        $filters = [];
        if ($manufacturerId !== null) {
            $filters['manufacturer_id'] = (int) $manufacturerId;
        }
        if ($location !== null) {
            $filters['location'] = $location;
        }

        // Get detections
        $detections = $this->model->getWithManufacturer($limit, $offset, $filters);
        $total = $this->model->countWithFilters($filters);

        return $this->respond([
            'status' => 200,
            'data'   => $detections,
            'pagination' => [
                'current_page' => $page,
                'per_page'     => $limit,
                'total'        => $total,
                'total_pages'  => (int) ceil($total / $limit),
            ],
        ]);
    }

    /**
     * GET /api/v1/detections/latest
     * Últimas 5 detecciones
     */
    public function latest()
    {
        $detections = $this->model->getLatest(5);

        return $this->respond([
            'status' => 200,
            'data'   => $detections,
        ]);
    }
}
