<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    use HasFactory;
    protected $table = 'proyek';
    protected $primaryKey = 'id';
    protected $fillable = ['title','image', 'location', 'description'];

    public function getImage()
    {
        if (!$this->image) {
            return asset('/uploads/image_proyek/default.png');
        } else {
            return asset('/uploads/image_proyek/' . $this->image);
        }
    }
}
