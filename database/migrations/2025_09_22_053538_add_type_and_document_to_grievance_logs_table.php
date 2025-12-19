<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('grievance_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('grievance_logs', 'type')) {
                $table->enum('type', ['preliminary', 'final', 'log'])->default('log')->after('user_id');
            }
            if (!Schema::hasColumn('grievance_logs', 'document')) {
                $table->string('document')->nullable()->after('remark');
            }
        });
    }

    public function down()
    {
        Schema::table('grievance_logs', function (Blueprint $table) {
            $table->dropColumn(['type', 'document']);
        });
    }
};
