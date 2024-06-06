<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleDetail extends Model
{
    protected $table = 'article_details';

    protected $hidden = array('article_id');

    protected $fillable = [
        'article_id','photo_path','detail_description'
    ];

    public $timestamps = true;

}
