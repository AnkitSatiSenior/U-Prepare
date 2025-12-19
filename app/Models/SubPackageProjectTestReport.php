<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubPackageProjectTestReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'test_id',
        'report',
        'file',
        'remark',
        'approved_by',
    ];

    /**
     * Get the test associated with this report.
     */
    public function test()
    {
        return $this->belongsTo(SubPackageProjectTest::class, 'test_id');
    }

    /**
     * Get the user who approved this report.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
