<?php


use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCertificatesRankLootFactor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whtools-certificates_rank_loot_factor', function (Blueprint $table) {
            $table->unsignedInteger('certID')->index();
            $table->integer('certRank');
            $table->double('factor');
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
        Schema::dropIfExists('whtools-certificates_rank_loot_factor');
    }
}