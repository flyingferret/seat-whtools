<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whtools-characterCertificates', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->bigInteger('character_id');
            $table->string('character_name');
            $table->integer('certID');
            $table->string('cert_name');
            $table->smallInteger('rank');
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
        Schema::dropIfExists('whtools-characterCertificates');
    }
}
