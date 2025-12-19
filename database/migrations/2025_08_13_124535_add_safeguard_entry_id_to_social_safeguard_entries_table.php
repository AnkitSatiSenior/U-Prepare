<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('social_safeguard_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('safeguard_entry_id')->nullable()->after('id');

            // Add foreign key constraint
            $table->foreign('safeguard_entry_id')
                  ->references('id')
                  ->on('safeguard_entries')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('social_safeguard_entries', function (Blueprint $table) {
            $table->dropForeign(['safeguard_entry_id']);
            $table->dropColumn('safeguard_entry_id');
        });
    }
};
