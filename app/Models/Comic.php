<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comic extends Model
{
    protected $table = 'comics';

    protected $fillable = [
        'title','category_id','cover_photo','download_size',  'pdf_path','status','created_by','updated_by','deleted_by','published_by','published_at'
    ];

    public $timestamps = true;

}
