<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocklvlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whtools_stocklvls', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->unsignedInteger('minLvl');
            
            $table->unsignedInteger('fitting_id');
            $table->foreign('fitting_id')->references('id')->on('seat_fitting')->onDelete('cascade');
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
        Schema::drop('whtools_stocklvls');
    }
}
