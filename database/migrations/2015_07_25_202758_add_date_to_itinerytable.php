<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateToItinerytable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itinerytable', function (Blueprint $table) {
            //
                        $table->string('fromdatetimeid', 10);
                        $table->string('todatetimeid', 10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('itinerytable', function (Blueprint $table) {
            //
        });
    }
}
