<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientRolesTable extends Migration
{
    public function up()
    {
        Schema::create('client_roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->string('role_name', 100);
            $table->tinyInteger('status_id')->default(1)->comment('1=Active 0=Inactive');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['client_id', 'status_id'], 'cr_client_status_idx');
            $table->unique(['client_id', 'role_name'], 'cr_client_role_unique');

            $table->foreign('client_id')->references('client_id')->on('client_masters')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('client_roles');
    }
}
