<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('opening_float', 10, 2)->default(0);
            $table->decimal('closing_float', 10, 2)->nullable();
            $table->decimal('expected_cash', 10, 2)->nullable(); // calculated at close
            $table->decimal('cash_variance', 10, 2)->nullable(); // closing_float - expected_cash
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('notes')->nullable();
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        // Add shift_id FK to sales table
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('shift_id')->nullable()->after('branch_id')->constrained('shifts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeignIfExists(['shift_id']);
            $table->dropColumnIfExists('shift_id');
        });
        Schema::dropIfExists('shifts');
    }
};
