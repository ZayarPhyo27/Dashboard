<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id','app_feedback','game_feedback', 'feedback_description'
    ];

    public $timestamps = true;

}
