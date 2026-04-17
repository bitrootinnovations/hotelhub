<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlanTypeToClientMasters extends Migration
{
    public function up()
    {
        Schema::table('client_masters', function (Blueprint $table) {
            $table->enum('plan_type', ['Basic', 'Premium'])
                  ->default('Basic')
                  ->after('subscription_end_date')
                  ->comment('Basic = mobile app only; Premium = mobile + web portal');
        });
    }

    public function down()
    {
        Schema::table('client_masters', function (Blueprint $table) {
            $table->dropColumn('plan_type');
        });
    }
}
