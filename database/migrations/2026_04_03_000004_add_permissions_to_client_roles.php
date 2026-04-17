<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionsToClientRoles extends Migration
{
    public function up()
    {
        Schema::table('client_roles', function (Blueprint $table) {
            $table->tinyInteger('can_take_orders')    ->default(1)->after('status_id');
            $table->tinyInteger('can_checkout')       ->default(0)->after('can_take_orders');
            $table->tinyInteger('can_manage_menu')    ->default(0)->after('can_checkout');
            $table->tinyInteger('can_manage_employees')->default(0)->after('can_manage_menu');
            $table->tinyInteger('can_view_reports')   ->default(0)->after('can_manage_employees');
        });

        // Manager gets all permissions by default
        \DB::table('client_roles')
            ->where('role_name', 'Manager')
            ->update([
                'can_take_orders'     => 1,
                'can_checkout'        => 1,
                'can_manage_menu'     => 1,
                'can_manage_employees'=> 0, // only client owner manages employees
                'can_view_reports'    => 1,
            ]);
    }

    public function down()
    {
        Schema::table('client_roles', function (Blueprint $table) {
            $table->dropColumn([
                'can_take_orders', 'can_checkout',
                'can_manage_menu', 'can_manage_employees', 'can_view_reports',
            ]);
        });
    }
}
