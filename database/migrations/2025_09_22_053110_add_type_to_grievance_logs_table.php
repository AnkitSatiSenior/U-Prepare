<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grievance_logs', function (Blueprint $table) {
            $table->string('type')->default('log')->after('user_id'); // preliminary | final | log
        });
    }

    public function down(): void
    {
        Schema::table('grievance_logs', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
