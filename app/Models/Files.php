<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    use HasFactory;

    protected $table = 'files';

    protected $fillable = [
        'name',
        'path',
        'extension',
        'mime_type',
        'size',
    ];

    public function upload($file)
    {
        try {
            $file->store('public');
            $file = new Files([
                'name' => $file->getClientOriginalName(),
                'path' => $file->hashName(),
                'extension' => $file->getClientOriginalExtension(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
            $file->save();
            return $file;
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error uploading file',
                'error' => $th->getMessage(),
            ]);
        }
    }
}
