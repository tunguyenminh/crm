<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('status')->nullable()->default(null); // Values will started, completed
            $table->dateTime('started_on')->nullable()->default(null);
            $table->dateTime('completed_on')->nullable()->default(null);
            $table->integer('total_leads')->nullable()->default(null);
            $table->integer('remaining_leads')->nullable()->default(null);
            $table->boolean('auto_reference')->default(true);
            $table->string('auto_reference_prefix', 10)->nullable()->default(null);
            $table->bigInteger('created_by')->unsigned()->nullable()->default(null);
            $table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('SET NULL');
            $table->bigInteger('form_id')->unsigned()->nullable()->default(null);
            $table->foreign('form_id')->references('id')->on('forms')->onUpdate('cascade')->onDelete('SET NULL');
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
        Schema::dropIfExists('campaigns');
    }
}
