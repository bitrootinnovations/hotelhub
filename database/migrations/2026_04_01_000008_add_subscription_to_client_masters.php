<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubscriptionToClientMasters extends Migration
{
    public function up()
    {
        Schema::table('client_masters', function (Blueprint $table) {
            $table->enum('subscription_type', ['Monthly', 'Quarterly', 'Yearly'])->nullable()->after('gst_number');
            $table->decimal('subscription_price', 10, 2)->nullable()->after('subscription_type');
            $table->date('subscription_start_date')->nullable()->after('subscription_price');
            $table->date('subscription_end_date')->nullable()->after('subscription_start_date');
        });
    }

    public function down()
    {
        Schema::table('client_masters', function (Blueprint $table) {
            $table->dropColumn(['subscription_type', 'subscription_price', 'subscription_start_date', 'subscription_end_date']);
        });
    }
}
