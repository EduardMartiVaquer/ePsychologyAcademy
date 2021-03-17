<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_events', function (Blueprint $table) {
            $table->id();
            $table->integer('course')->nullable();
            $table->integer('subject')->nullable();
            $table->dateTime('from');
            $table->dateTime('to');
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->string('opentok_session')->nullable();
            $table->longText('opentok_token')->nullable();
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
        Schema::dropIfExists('class_events');
    }
}
