<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasS3Image;

class SubPackageProjectTestReport extends Model
{
    use HasFactory, SoftDeletes, HasS3Image;

    // ✅ ARCHITECTURE FIX: Tell the HasS3Image trait which column holds the S3 key
    protected string $imageColumn = 'file';

    protected $fillable = [
        'test_id',
        'report',
        'file',
        'remark',
        'approved_by',
    ];

    // ✅ Append the URL dynamically for JSON responses
    protected $appends = ['url'];

    /**
     * Resolves the S3 URL using the logic from HasS3Image.
     */
    public function getUrlAttribute(): string
    {
        return $this->image_url; 
    }

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