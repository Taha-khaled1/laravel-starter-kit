<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ShouldQueueWithoutChain;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersImport implements ToModel, WithMapping, WithHeadingRow, ShouldQueueWithoutChain, WithChunkReading
{
    public function chunkSize(): int
    {
        return 100;
    }
    public function map($row): array
    {
        return [
            'name' => $row[10] ?? null,
            'phone' => $row[11] ?? null,
            'identity_id' => $row[12] ?? null,
            'resrvation_id' => $row[13] ?? null,
            'resrvation_status' => $row[0] ?? null,
            'packge' => $row[4] ?? null,
            'city' => $row[3] ?? null,
            'birth_date' => $row[1] ?? null,
            'birth_date_hijri' => $row[9] ?? null,
            'gender' => isset($row[8]) && $row[8] == 'ذكر' ? 'male' : 'female',
            'nationality' => $row[7] ?? null,
            'password' => bcrypt('password123'),
        ];
    }

    public function model(array $row)
    {
        // Check if user with identity_id already exists
        $existingUser = User::where('identity_id', $row['identity_id'])->first();

        // If user exists, return null to skip creation
        if ($existingUser) {
            return null;
        }

        // Create and return new user
        return new User($row);
    }
}
