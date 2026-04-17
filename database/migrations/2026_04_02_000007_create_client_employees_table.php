<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientEmployeesTable extends Migration
{
    public function up()
    {
        Schema::create('client_employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('client_role_id')->nullable();
            $table->string('name', 150);
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->string('phone', 15)->nullable();
            $table->string('profile_image')->nullable();
            $table->tinyInteger('status_id')->default(1)->comment('1=Active 0=Inactive');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for fast client-wise lookups
            $table->index(['client_id', 'status_id'], 'ce_client_status_idx');
            $table->index('client_role_id',            'ce_role_idx');

            $table->foreign('client_id')     ->references('client_id')->on('client_masters')->onDelete('cascade');
            $table->foreign('client_role_id')->references('id')       ->on('client_roles')  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('client_employees');
    }
}
