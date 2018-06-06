<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('fullName');
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->string('phone', 20)->unique();
            $table->longText('address', 300);
            $table->string('avatar')->nullable();
            $table->enum('type', ['user', 'admin', 'agency'])->default('user');
            $table->string('channel')->nullable();
            $table->boolean('isDeleted')->default(false);
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
