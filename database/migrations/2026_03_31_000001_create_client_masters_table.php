<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientMastersTable extends Migration
{
    public function up()
    {
        Schema::create('client_masters', function (Blueprint $table) {
            $table->id('client_id');
            $table->string('client_name', 150);
            $table->string('image')->nullable()->comment('Profile image path');
            $table->text('address');
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('contact_number', 15);
            $table->string('email_id', 150)->unique();
            $table->string('aadhar_image')->nullable()->comment('Aadhar card image path');
            $table->string('gst_number', 20)->nullable()->comment('GST registration number');
            $table->tinyInteger('status_id')->default(1)->comment('1=Active, 2=Inactive, 3=Suspended, 4=Trial');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('client_masters');
    }
}
