<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeService
{
    public function generateJobApplicationQr(string $data): string
    {
        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $data,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        );

        $result = $builder->build();

        $directory = storage_path('qrcodes/job_applications');
        if (!file_exists($directory)) {
            mkdir($directory, 0770, true);
        }

        $filename = 'job_app_' . md5($data) . '.png';
        $path = "qrcodes/job_applications/{$filename}";

        $result->saveToFile(storage_path($path));

        return $path;
    }
}
