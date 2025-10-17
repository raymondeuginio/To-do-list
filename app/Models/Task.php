<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'notes',
        'due_date',
        'priority',
        'is_done',
    ];

    protected $casts = [
        'due_date' => 'date',
        'is_done' => 'boolean',
    ];
}
