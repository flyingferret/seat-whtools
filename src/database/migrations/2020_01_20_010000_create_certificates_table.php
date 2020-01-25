<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whtools-certificates', function (Blueprint $table) {
            $table->increments('certID');
            $table->string('name');
            $table->string('description');
            $table->timestamps();
        });

        Schema::create('whtools-certificates_skills', function (Blueprint $table) {
            $table->integer('skillID');
            $table->unsignedInteger('certID')->index();
            $table->integer('requiredLvl');
            $table->integer('certRank');
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
        Schema::dropIfExists('whtools-certificates');
        Schema::dropIfExists('whtools-certificates_skills');
    }
}
