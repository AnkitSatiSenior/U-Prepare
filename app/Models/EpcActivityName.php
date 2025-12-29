<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class EpcActivityName extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'epc_activity_names';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Relationships
     * Link to EPC entries or Already Define EPC table if needed.
     */
    public function alreadyDefineEpc()
    {
        return $this->hasMany(AlreadyDefineEpc::class, 'activity_id');
    }
}
