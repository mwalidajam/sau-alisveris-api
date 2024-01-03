<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\FilesController;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = [
        'name',
        'details',
        'image_id',
    ];

    public function image()
    {
        return $this->belongsTo(Files::class, 'image_id');
    }

    public function update_image($image)
    {
        try {
            $image = FilesController::upload($image);
            $this->image_id = $image->id;
            $this->save();
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error uploading file',
                'error' => $th->getMessage(),
            ]);
        }
    }
}
