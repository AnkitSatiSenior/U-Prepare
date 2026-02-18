<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tender extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title_en',
        'title_hi',
        'description_en',
        'description_hi',
        'file',
        'open_date',
        'close_date',
        'ip_address',
    ];

    /**
     * The attributes that should be cast.
     * This converts the database string to a Carbon Date object.
     */
    protected $casts = [
        'open_date' => 'datetime',
        'close_date' => 'datetime',
    ];
}