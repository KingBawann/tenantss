<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Inventory actions log every stock movement per branch.
     * type: 'in'  = stock added (purchase received, manual adjustment up)
     *       'out' = stock removed (sale, manual adjustment down, damage, return)
     * This table serves as the audit trail for inventory changes.
     */
    public function up(): void
    {
        Schema::create('inventory_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('type'); // 'in' or 'out'
            $table->integer('quantity');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_actions');
    }
};
