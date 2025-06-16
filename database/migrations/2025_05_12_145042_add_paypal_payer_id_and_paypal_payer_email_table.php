<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'paypal_payer_id')) {
                $table->string('paypal_payer_id', 50)->nullable()->after('paypal_order_id');
            }

            if (!Schema::hasColumn('payments', 'paypal_payer_email')) {
                $table->string('paypal_payer_email', 255)->nullable()->after('paypal_payer_id');
            }
        });
    }

    public function down(): void
    {
        //
    }
};
