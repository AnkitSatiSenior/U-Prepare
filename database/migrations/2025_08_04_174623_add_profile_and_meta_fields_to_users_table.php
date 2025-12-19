<?php

// database/migrations/xxxx_xx_xx_add_profile_and_meta_fields_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('designation_id')->nullable()->constrained('designations')->nullOnDelete();

           
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('phone_no')->nullable()->index();
           
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('district')->nullable()->index();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['designation_id']);

            $table->dropColumn([
                'department_id',
                'designation_id',
                
                'gender',
                'phone_no',
                
                'status',
                'district',
            ]);

            $table->dropSoftDeletes();
        });
    }
};
