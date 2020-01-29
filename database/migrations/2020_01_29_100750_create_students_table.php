<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('student_number')->unsigned();
            $table->string('first_name');
            $table->string('prefixes', 30)->nullable();
            $table->string('last_name');
            $table->string('gender',1)->default('M');
            $table->string('email')->nullable();
            $table->bigInteger('group_id')->unsigned();
            $table->string('card_one_uid')->nullable();
            $table->string('card_two_uid')->nullable();
            $table->string('photo')->default('https://img.icons8.com/dotty/80/000000/profile-face.png');
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
        Schema::dropIfExists('students');
    }
}
