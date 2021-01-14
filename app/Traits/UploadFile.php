<?php

namespace App\Traits;

use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Storage;

trait UploadFile
{
    public function storeToS3($path, $file)
    {
        try {
            return Storage::disk('s3')->put($path, $file, 'public');
        } catch (AwsException $e) {
            return $e->getAwsErrorMessage();
        }
    }

    public function getS3Url($path)
    {
        try {
            return Storage::disk('s3')->url($path);
        } catch (AwsException $e) {
            return $e->getAwsErrorMessage();
        }
    }

    public function removeS3File($url, $folder)
    {
        $file_id = $this->getS3Id($url);
        try {
            return Storage::disk('s3')->delete($folder . '/' . $file_id);
        } catch (AwsException $e) {
            return $e->getAwsErrorMessage();
        }
    }

    public function getS3Id($url)
    {
        $explode_url = explode('/', $url);
        return end($explode_url);
    }
}
