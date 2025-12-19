<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'status',
    ];
    public function blocks() {
    return $this->hasMany(GeographyBlock::class);
}

}
