<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_of_purchase', function (Blueprint $table) {
            $table->string('uuid')->unique()->primary();
            $table->string('client_uuid');
            $table->array('items');
            $table->string('date_schedule');
            $table->string('form_purchase');
            $table->string('address_send');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('list_of_purchase');
    }
};
