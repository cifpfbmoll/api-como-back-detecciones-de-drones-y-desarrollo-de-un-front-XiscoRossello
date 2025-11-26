<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ManufacturerSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'oui'        => '60:60:1F',
                'name'       => 'DJI Technology Co., Ltd.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'oui'        => '48:1C:B9',
                'name'       => 'DJI (Shenzhen DJI Sciences and Technologies)',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'oui'        => '34:D2:62',
                'name'       => 'DJI Innovation Technology',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'oui'        => '90:3A:E6',
                'name'       => 'Parrot SA',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'oui'        => 'A0:14:3D',
                'name'       => 'Parrot Drones SAS',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'oui'        => '00:12:1C',
                'name'       => 'Parrot S.A.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'oui'        => 'E0:B6:F5',
                'name'       => 'Yuneec International',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'oui'        => '24:62:AB',
                'name'       => 'Espressif Inc. (Common in custom drones)',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'oui'        => 'B8:27:EB',
                'name'       => 'Raspberry Pi Foundation (DIY Drones)',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'oui'        => '00:26:7E',
                'name'       => 'Parrot SA (Legacy)',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'oui'        => 'DC:A6:32',
                'name'       => 'Raspberry Pi Trading Ltd (DIY Drones)',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'oui'        => '3C:71:BF',
                'name'       => 'Espressif Systems (Shanghai)',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('manufacturers')->insertBatch($data);
    }
}
