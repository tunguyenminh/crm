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
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name')->nullable()->default(null);
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('contact_number')->nullable()->default(null);
            $table->string('image')->nullable()->default(null);
            $table->string('skype_id', 50)->nullable()->default(null);
            $table->string('address')->nullable()->default(null);
            $table->string('country', 50)->nullable()->default(null);
            $table->string('zip_code', 50)->nullable()->default(null);

            $table->string('status')->default('enabled');
            $table->string('reset_code')->nullable()->default(null);

            $table->string('timezone', 50)->default('Asia/Kolkata');
            $table->string('date_format', 20)->default('d-m-Y');
            $table->string('date_picker_format', 20)->default('dd-mm-yyyy');
            $table->string('time_format', 20)->default('h:i a');

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
