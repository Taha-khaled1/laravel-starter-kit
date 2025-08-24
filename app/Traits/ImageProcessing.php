<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

trait ImageProcessing
{
    /**
     * Get the file extension of an image given its MIME type
     *
     * @param string $mime
     * @return string
     */
    /******  51393518-4b44-407d-8332-1ac1a19387a8  *******/
    public function get_mime($mime)
    {
        if ($mime == 'image/jpeg')
            $extension = '.jpg';
        elseif ($mime == 'image/png')
            $extension = '.png';
        elseif ($mime == 'image/svg')
            $extension = '.svg';
        elseif ($mime == 'image/gif')
            $extension = '.gif';
        elseif ($mime == 'image/svg+xml')
            $extension = '.svg';
        elseif ($mime == 'image/tiff')
            $extension = '.tiff';
        elseif ($mime == 'image/webp')
            $extension = '.webp';

        return $extension;
    }



    public function saveImage($image, $folder)
    {
        $img = Image::make($image);
        $extension = $this->get_mime($img->mime());
        $str_random = Str::random(8);
        $imgpath = $str_random . time() . $extension;

        // Create directory path directly in storage
        $directory = storage_path($folder);

        // Create directory if it doesn't exist
        if (!is_dir($directory)) {
            mkdir(
                $directory,
                0770,
                true
            );
        }

        // Save image directly to storage path
        $img->save($directory . '/' . $imgpath);

        return 'storage/' . $folder . '/' . $imgpath;
    }
    /**
     * Get the file extension of a media file (audio or video) given its MIME type
     *
     * @param string $mime
     * @return string
     */
    public function getMediaExtension($mime)
    {
        if (in_array($mime, ['audio/mpeg', 'audio/mp3'])) {
            $extension = '.mp3';
        } elseif ($mime == 'audio/wav') {
            $extension = '.wav';
        } elseif ($mime == 'audio/ogg') {
            $extension = '.ogg';
        } elseif (in_array($mime, ['audio/m4a', 'audio/x-m4a', 'audio/mp4'])) {
            $extension = '.m4a';
        } elseif (in_array($mime, ['audio/aac', 'audio/x-aac', 'audio/aacp'])) {
            $extension = '.aac';
        } elseif ($mime == 'audio/webm') {
            $extension = '.webm';
        } elseif ($mime == 'video/mp4') {
            $extension = '.mp4';
        } elseif ($mime == 'video/ogg') {
            $extension = '.ogv';
        } elseif ($mime == 'video/webm') {
            $extension = '.webm';
        } elseif ($mime == 'video/x-msvideo') {
            $extension = '.avi';
        } elseif ($mime == 'video/quicktime') {
            $extension = '.mov';
        } elseif ($mime == 'application/pdf') {
            $extension = '.pdf';
        } elseif ($mime == 'application/msword') {
            $extension = '.doc';
        } elseif ($mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            $extension = '.docx';
        } elseif ($mime == 'application/octet-stream') {
            // For octet-stream, we'll rely on the original file extension
            $extension = '';
        } else {
            $extension = '';
        }

        return $extension;
    }


    /**
     * Save an audio or video file to the specified storage folder
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder
     * @return string
     */
    public function saveMedia($file, $folder)
    {
        // Get the MIME type from the file instance
        $mime = $file->getMimeType();
        $extension = $this->getMediaExtension($mime);

        // Fallback: if the MIME type wasn't mapped, try using the original file extension
        if (empty($extension)) {
            $extension = '.' . $file->getClientOriginalExtension();
        }

        // Generate a unique file name
        $fileName = Str::random(8) . time() . $extension;

        // Define the directory path (using storage_path ensures the correct storage directory)
        $directory = storage_path($folder);

        // Create the directory if it does not exist
        if (!is_dir($directory)) {
            mkdir($directory, 0770, true);
        }

        // Move the file to the storage directory
        $file->move($directory, $fileName);

        // Return the file path relative to the storage folder (adjust as needed)
        return 'storage/' . $folder . '/' . $fileName;
    }

    public function saveVoiceMessage($file, $folder)
    {
        // Get the MIME type from the file instance
        $mime = $file->getMimeType();
        $originalExtension = strtolower($file->getClientOriginalExtension());

        // Log for debugging - remove this in production
        Log::info('Voice file upload debug', [
            'mime_type' => $mime,
            'original_extension' => $originalExtension,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize()
        ]);

        // Validate that it's an audio file
        $allowedAudioMimes = [
            'audio/mpeg',
            'audio/mp3',
            'audio/wav',
            'audio/ogg',
            'audio/m4a',
            'audio/aac',
            'audio/webm',
            'audio/x-m4a',
            'audio/mp4',
            'audio/x-aac',
            'audio/aacp',
            'application/octet-stream'
        ];

        $allowedExtensions = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'webm', 'mp4'];

        if (!in_array($mime, $allowedAudioMimes) && !in_array($originalExtension, $allowedExtensions)) {
            throw new \InvalidArgumentException('Invalid audio file format for voice message. Detected MIME: ' . $mime . ', Extension: ' . $originalExtension);
        }

        $extension = $this->getMediaExtension($mime);

        // Fallback: if the MIME type wasn't mapped, try using the original file extension
        if (empty($extension)) {
            $extension = '.' . $originalExtension;
        }

        // Generate a unique file name with voice prefix
        $fileName = 'voice_' . Str::random(8) . time() . $extension;

        // Define the directory path
        $directory = storage_path($folder);

        // Create the directory if it does not exist
        if (!is_dir($directory)) {
            mkdir($directory, 0770, true);
        }

        // Move the file to the storage directory
        $file->move($directory, $fileName);

        // Return the file path relative to the storage folder
        return 'storage/' . $folder . '/' . $fileName;
    }
    /**
     * Resizes an image to a given width and height, while maintaining the original aspect ratio.
     *
     * @param string $image The raw image data
     * @param int $width The desired width of the resized image
     * @param int $height The desired height of the resized image 
     * @param string $folder The folder to save the image in
     * @return string The path to the saved image
     */
    public function aspect4resize($image, $width, $height, $folder)
    {
        $img = Image::make($image);
        $extension = $this->get_mime($img->mime());
        $str_random = Str::random(8);

        $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Compress the image
        $img->encode(null, 75);  // 75% quality compression

        // Create full directory path
        $directory = storage_path($folder);

        // Create directory if it doesn't exist
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $imgpath = $str_random . time() . $extension;
        $img->save($directory . '/' . $imgpath);

        return $imgpath;
    }

    public function deleteImage($filePath)
    {
        if ($filePath) {
            if (is_file(Storage::disk('imagesfp')->path($filePath))) {
                if (file_exists(Storage::disk('imagesfp')->path($filePath))) {
                    unlink(Storage::disk('imagesfp')->path($filePath));
                }
            }
        }
    }
}
