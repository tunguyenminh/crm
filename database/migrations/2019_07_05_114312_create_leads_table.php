<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reference_number', 50)->nullable()->default(NULL);
            $table->string('status', 20)->default('unactioned');
            $table->string('interested', 20)->nullable()->default(NULL);
            $table->boolean('appointment_booked')->default(false);
            $table->bigInteger('campaign_id')->unsigned()->nullable()->default(null);
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('email_template_id')->unsigned()->nullable()->default(null);
            $table->foreign('email_template_id')->references('id')->on('email_templates')->onUpdate('cascade')->onDelete('set null');
            $table->bigInteger('first_actioned_by')->unsigned()->nullable()->default(null);
            $table->foreign('first_actioned_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            $table->bigInteger('last_actioned_by')->unsigned()->nullable()->default(null);
            $table->foreign('last_actioned_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            $table->integer('time_taken')->nullable()->default(null);
            $table->string('hash', 60)->nullable()->default(null);
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
        Schema::dropIfExists('leads');
    }
}
