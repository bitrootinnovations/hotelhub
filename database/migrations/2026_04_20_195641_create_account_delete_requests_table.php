<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountDeleteRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_delete_requests', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 150);
            $table->string('email', 150);
            $table->string('phone', 20)->nullable();
            $table->enum('reason', [
                'no_longer_using',
                'privacy_concerns',
                'switching_service',
                'data_concerns',
                'other',
            ])->default('other');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'processed', 'rejected'])->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_delete_requests');
    }
}
