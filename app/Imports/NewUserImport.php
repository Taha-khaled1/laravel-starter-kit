<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ShouldQueueWithoutChain;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;

class NewUserImport implements ToModel, WithMapping, WithHeadingRow, ShouldQueueWithoutChain, WithChunkReading
{
    public function chunkSize(): int
    {
        return 100;
    }

    public function map($row): array
    {
        return [
            'name' => $row['asm_alhag'] ?? null, // done
            'phone' => ($row['rkm_algoal'] ?? null) != '-' ? $row['rkm_algoal'] : null, // done
            'identity_id' => ($row['rkm_alhoy'] ?? null) != '-' ? $row['rkm_alhoy'] : null, // done
            'type_of_transportation' => $row['noaa_almoaslat'] ?? null, // done
            'resrvation_id' => $row['rkm_alhgz'] ?? null, // done
            'license_number' => $row['rkm_altrkhys'] ?? null, // done
            'packge' => $row['noaa_albak'] ?? null,  // done
            'birth_date' => ($row['tarykh_almylad'] ?? null) != '-' ? $row['tarykh_almylad'] : null, // done
            'gender' => ($row['algns'] ?? null) == 'ذكر' ? 'male' : 'female', // done
            'nationality' => $row['algnsy'] ?? null, // done
            'payment_mechanism' => $row['aly_aldfaa'] ?? null,
            'booking_by' => $row['alhgz_aan_tryk'] ?? null,
            'condition' => $row['alhal'] ?? null, // done
            'company_name' => $row['asm_alshrkh'] ?? null, // done
            'password' => bcrypt('password123'), // done
            // Fields not in Excel that will remain null:
            // image, email, license_number, status, age, 
            // resrvation_data, city, city_id, birth_date_hijri,
            // type (defaults to "user"), group_id, etc.
        ];
    }

    public function model(array $row)
    {
        // Skip if no valid identity_id
        if (empty($row['identity_id']) || $row['identity_id'] == '-') {
            return null;
        }

        // Check if user already exists
        $existingUser = User::where('identity_id', $row['identity_id'])->first();
        if ($existingUser) {
            return null;
        }

        return new User($row);
    }
}
