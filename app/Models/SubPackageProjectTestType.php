<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SubPackageProjectTestType extends Model
{
     use HasFactory, SoftDeletes;

    protected $table = 'sub_package_project_test_types';

    protected $fillable = [
        'name',
    ];
    public function subPackageProjectTests()
{
    return $this->hasMany(SubPackageProjectTest::class, 'test_type_id');
}

}
