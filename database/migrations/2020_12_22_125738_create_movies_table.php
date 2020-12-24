<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id('tmdb_id');
            $table->string('title');
            $table->string('release_date');
            $table->text('overview');
            $table->string('poster_url');
            $table->float('tmdb_vote_avg');
            $table->integer('tmdb_vote_count');
            $table->string('tmdb_url');
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
        Schema::dropIfExists('movies');
    }
}
