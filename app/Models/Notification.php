<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notification';
    protected  $fillable = [
        'title',
        'description',
        'status',
        'deleted_by',
        'created_by',
        'updated_by',
        'pushed_by',
        'pushed_at',
    ];
}
