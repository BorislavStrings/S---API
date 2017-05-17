<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    protected $table = 'files';
    protected $fillable = ['mime', 'filename', 'original_filename', 'fileindex', 'external'];

    public function getFilenameAttribute($value)
    {
        $path = 'http://127.0.0.1/';
        return $path . $value;
    }
}
