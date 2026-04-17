<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SeedAdminUser extends Migration
{
    public function up()
    {
        DB::table('users')->insertOrIgnore([
            'role_id'    => 1,
            'name'       => 'Admin',
            'email'      => 'admin@hotelhub.com',
            'password'   => Hash::make('admin@123'),
            'status_id'  => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        DB::table('users')->where('email', 'admin@hotelhub.com')->delete();
    }
}
