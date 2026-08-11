<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('business_type')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('subscription_plan')->default('free');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tenants');
    }
};
