<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Director extends Model {
    protected $table = 'directors';
    /**
     * mass assignable attributes
     * 
     * @var array
     */
    protected $fillable = [
        'name', 'tmdb_id', 'biography',
    ];

    protected $attributes = [
        'date_of_birth' => "",
    ];

}