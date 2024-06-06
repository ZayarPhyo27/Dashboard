<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleQuiz extends Model
{
    protected $table = 'article_quiz';

    protected $fillable = [
        'article_id','quiz_id'
    ];

    public $timestamps = true;

}
