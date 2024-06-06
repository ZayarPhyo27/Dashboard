<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDownloadRecord extends Model
{
    protected $table = 'user_download_records';

    protected $fillable = [
        'user_id','content_type','content_id'
    ];

    public $timestamps = true;

}
