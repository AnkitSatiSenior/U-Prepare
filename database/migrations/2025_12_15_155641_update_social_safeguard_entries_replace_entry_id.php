<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('social_safeguard_entries', function (Blueprint $table) {

            // 1️⃣ Drop old column and foreign key if it exists
            if (Schema::hasColumn('social_safeguard_entries', 'safeguard_entry_id')) {
                $table->dropForeign(['safeguard_entry_id']);
                $table->dropColumn('safeguard_entry_id');
            }

            // 2️⃣ Add new column only if it doesn't exist
            if (!Schema::hasColumn('social_safeguard_entries', 'already_define_safeguard_entry_id')) {
                $table->foreignId('already_define_safeguard_entry_id')
                    ->after('id')
                    ->constrained('already_define_safeguard_entries')
                    ->cascadeOnDelete()
                    ->name('social_safeguard_entries_already_fk'); // short name
            }
        });
    }

    public function down(): void
    {
        Schema::table('social_safeguard_entries', function (Blueprint $table) {

            // Drop new FK and column if exists
            if (Schema::hasColumn('social_safeguard_entries', 'already_define_safeguard_entry_id')) {
                $table->dropForeign('social_safeguard_entries_already_fk');
                $table->dropColumn('already_define_safeguard_entry_id');
            }

            // Recreate old column if needed
            if (!Schema::hasColumn('social_safeguard_entries', 'safeguard_entry_id')) {
                $table->foreignId('safeguard_entry_id')
                    ->nullable()
                    ->constrained('safeguard_entries')
                    ->nullOnDelete()
                    ->name('social_safeguard_entries_old_fk'); // optional short name
            }
        });
    }
};
