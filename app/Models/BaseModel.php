<?php

namespace App\Models;

use Aws\Exception\AwsException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BaseModel extends Model
{
    use HasFactory;

    public function urlImageS3($url)
    {
        if ($url) {
            try {
                return Storage::disk('s3')->url($url);
            } catch (AwsException $e) {
                return $e->getAwsErrorMessage();
            }
        }

        return '';
    }
}
