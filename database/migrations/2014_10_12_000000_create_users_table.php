<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(0);
            $table->integer('type')->default(1);
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone_prefix')->nullable();
            $table->string('phone')->nullable();
            $table->string('password')->nullable();
            $table->string('avatar')->nullable();
            $table->string('timezone')->nullable();
            $table->string('lang')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->boolean('push_notifications')->default(1);
            $table->boolean('email_notifications')->default(1);
            $table->boolean('sms_notifications')->default(1);
            $table->string('languages')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verification_code')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
