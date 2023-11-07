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
        Schema::table('users', function (Blueprint $table) {
            $table->string('cashier_stripe_id')->nullable()->index();
            $table->string('cashier_pm_type')->nullable();
            $table->string('cashier_pm_last_four', 4)->nullable();
            $table->timestamp('cashier_trial_ends_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'cashier_stripe_id',
                'cashier_pm_type',
                'cashier_pm_last_four',
                'cashier_trial_ends_at',
            ]);
        });
    }
};
