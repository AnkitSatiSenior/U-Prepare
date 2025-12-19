<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    protected $fillable = [
        'img',
        'head',
        'subh',
        'btn_text',
        'link',
        'order',
        'status',
    ];
}
