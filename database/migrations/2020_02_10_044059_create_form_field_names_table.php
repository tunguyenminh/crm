<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormFieldNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_field_names', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name');
            $table->text('first_name');
            $table->text('last_name');
            $table->text('email');
            $table->text('phone');
            $table->timestamps();
        });

        $fieldNameArray = new \App\Models\FormFieldName();
        $fieldNameArray->name = 'Name,name';
        $fieldNameArray->first_name = 'First Name,first name,First name,first Name,firstname,first_name';
        $fieldNameArray->last_name = 'Last Name,last name,Last name,last Name,lastname,last_name';
        $fieldNameArray->email = 'Email,email,Email Address,emaill address,Email address,email Address,email_address';
        $fieldNameArray->phone = 'Phone,phone,Phone Number,phone number,Phone No.,cell phone number,Cell Phone Number,contact number,Contact Number,Contact No.,Mobile,mobile,Mobile No, mobile no,Mobile Number,mobile number,Customer Number,customer number,Customer number,customer Number';
        $fieldNameArray->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_field_names');
    }
}
