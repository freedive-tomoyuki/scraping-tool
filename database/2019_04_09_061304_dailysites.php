<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Dailysites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('dailysites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('media_id')->unique();
            $table->string('site_name')->nullable();
            $table->string('imp')->nullable()->default('0');
            $table->string('click')->nullable()->default('0');
            $table->string('cv')->nullable()->default('0');
            $table->string('cvr')->nullable()->default('0');
            $table->string('ctr')->nullable()->default('0');
            $table->string('url')->nullable();
            $table->string('month')->nullable();
            $table->string('day')->nullable();
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
        Schema::dropIfExists('dailysites');
    }
}
