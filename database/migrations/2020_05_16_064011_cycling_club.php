<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CyclingClub extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cycling_club', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('club_name')->unique();
            $table->string('bio')->nullable();
            $table->string('city');
            $table->string('county')->nullable();
            $table->string('country');
            $table->string('country_short');
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('preferred_style')->nullable();
            $table->string('profile_picture')->nullable();
            $table->boolean('is_active')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
