<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auth_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade'); // delete tokens if user deleted

            $table->string('token', 512)->unique(); // store JWT token
            $table->timestamp('generated_at')->useCurrent(); // token created time
            $table->timestamp('valid_until')->nullable(); // token expiry time
            $table->boolean('is_valid')->default(true); // flag for invalidation

            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_tokens');
    }
};
