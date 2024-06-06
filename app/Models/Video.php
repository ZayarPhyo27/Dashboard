<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    protected $table = 'videos';

    protected $fillable = [
        'title','category_id','cover_photo','download_size','video_path','duration','status','deleted_by','published_by','published_at','created_by','updated_by'
    ];

    public $timestamps = true;

}
