<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeMastersTable extends Migration
{
    public function up()
    {
        Schema::create('employee_masters', function (Blueprint $table) {
            $table->bigIncrements('employee_id');
            $table->string('employee_name', 150);
            $table->string('email_id', 150)->unique();
            $table->string('contact_number', 15);
            $table->unsignedBigInteger('role_id')->default(2);
            $table->string('designation', 100)->nullable();
            $table->string('department', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('profile_image')->nullable();
            $table->tinyInteger('status_id')->default(1); // 1=Active 0=Inactive
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('role_id')->references('role_id')->on('role_masters')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_masters');
    }
}
