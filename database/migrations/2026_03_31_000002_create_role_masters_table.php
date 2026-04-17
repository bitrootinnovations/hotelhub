<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleMastersTable extends Migration
{
    public function up()
    {
        Schema::create('role_masters', function (Blueprint $table) {
            $table->id('role_id');
            $table->string('role_name', 100)->unique();
            $table->text('description')->nullable();
            $table->tinyInteger('status_id')->default(1)->comment('1=Active, 0=Inactive');
            $table->timestamps();
            $table->softDeletes();
        });

        // Seed default Admin role
        DB::table('role_masters')->insert([
            'role_name'   => 'Admin',
            'description' => 'Super administrator with full access to all modules.',
            'status_id'   => 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('role_masters');
    }
}
