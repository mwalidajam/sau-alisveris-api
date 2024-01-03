<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Files;

class FilesController extends Controller
{
    static function upload($file)
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
