<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
    	'title',
    	'description',
    	'thumbnail',
    	'is_completed',
    	'user_id'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];
}