<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model {
    //use SoftDeletes;

    /**
     * mass assignable attributes
     * 
     * @var array
     */
    protected $fillable = [
        'title', 'release_date', 'overview', 'poster_url', 'tmdb_id', 'tmdb_vote_avg', 'tmdb_vote_count', 'tmdb_url', 'length', 'genres'
    ];

    protected $casts = [
        'genres' => 'array'
    ];

    protected $primaryKey = 'tmdb_id';

    /**
     * BelongsToMany relation with Director model
     * @return mixed
     */
    public function directors() {
        return $this->belongsToMany(Director::class, 'movie_directors');
    }
}