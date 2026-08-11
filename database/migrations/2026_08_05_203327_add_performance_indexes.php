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
        Schema::table('sales', function (Blueprint $table) {
            $table->index('created_at');
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->index('date');
        });
        Schema::table('inventory_actions', function (Blueprint $table) {
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex(['date']);
        });
        Schema::table('inventory_actions', function (Blueprint $table) {
            $table->dropIndex(['date']);
        });
    }
};
