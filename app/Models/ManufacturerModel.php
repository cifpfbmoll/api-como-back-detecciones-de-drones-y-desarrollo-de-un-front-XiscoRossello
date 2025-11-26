<?php

namespace App\Models;

use CodeIgniter\Model;

class ManufacturerModel extends Model
{
    protected $table            = 'manufacturers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['oui', 'name'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'oui'  => 'required|max_length[8]|is_unique[manufacturers.oui]',
        'name' => 'required|max_length[255]',
    ];

    protected $validationMessages = [
        'oui' => [
            'required'  => 'El OUI es obligatorio.',
            'is_unique' => 'Este OUI ya existe en la base de datos.',
        ],
        'name' => [
            'required' => 'El nombre del fabricante es obligatorio.',
        ],
    ];

    /**
     * Find manufacturer by OUI
     */
    public function findByOui(string $oui): ?array
    {
        return $this->where('oui', strtoupper($oui))->first();
    }
}
