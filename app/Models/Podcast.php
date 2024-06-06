<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Podcast extends Model
{
    protected $table = 'podcasts';

    protected $fillable = [
        'title','category_id','cover_photo', 'audio_path','duration', 'download_size', 'status','deleted_by','created_by','published_by','published_at',
    ];

    public $timestamps = true;

}
