<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkService extends Model
{
    use HasFactory;

    protected $table = 'work_service';

    // Allow mass assignment for these fields
    protected $fillable = [
        'name',
        'department_id',
    ];

    /**
     * Get the department that owns the work service.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
