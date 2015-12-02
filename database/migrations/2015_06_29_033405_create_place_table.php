<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('place', function (Blueprint $table) {
            $table->increments('placeid');
            $table->text('placename');
            $table->string('lat');
            $table->string('long');
            $table->string('placetypes')->nullable();
            $table->string('formattedaddress')->nullable();
            $table->string('imageurl')->nullable();
            $table->string('openinghours')->nullable();
            $table->string('rating')->nullable();
            $table->string('vicinity')->nullable();
            $table->string('formattedphone')->nullable();
            $table->string('internationalphone')->nullable();
            $table->string('review')->nullable();
            $table->string('website')->nullable();
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
        Schema::drop('place');
    }
}
