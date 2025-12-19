<?php

// app/Models/Designation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
