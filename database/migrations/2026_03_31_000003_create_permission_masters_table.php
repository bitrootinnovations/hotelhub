<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionMastersTable extends Migration
{
    public function up()
    {
        Schema::create('permission_masters', function (Blueprint $table) {
            $table->id('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->string('menu_name', 100);
            $table->tinyInteger('can_view')->default(0);
            $table->tinyInteger('can_add')->default(0);
            $table->tinyInteger('can_edit')->default(0);
            $table->tinyInteger('can_delete')->default(0);
            $table->timestamps();

            $table->foreign('role_id')->references('role_id')->on('role_masters')->onDelete('cascade');
            $table->unique(['role_id', 'menu_name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('permission_masters');
    }
}
