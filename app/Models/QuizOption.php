<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizOption extends Model
{
    use HasFactory;

    protected $hidden = array('quiz_id');

    protected  $fillable = [
        'quiz_id',
        'option_name',
        'is_correct',
        'deleted_by'
    ];
}
