<?php

namespace App\Http\Controllers;

use App\Models\Director;
use TMDB\ApiClient\TMDBController;
use App\Models\Movie;

class Controller {

    private $tmdbController;
    private $imagesBaseUrl;
    private $moviesBaseUrl;
    private $tmdbApiPaginatorSize;

    /**
     * @param integer $_paginatorSize
     * sets paginator size, number of movies returned by the api call
     */
    public function __construct($_paginatorSize)
    {
        $baseUrl = config('app.tmdb_api_url');
        $api_key = config('app.tmdb_api_key');

        $this->tmdbController = new TMDBController($baseUrl, $api_key);
        $this->tmdbApiPaginatorSize = $_paginatorSize;

        $this->imagesBaseUrl = config('app.tmdb_images_base_url');
        $this->moviesBaseUrl = config('app.tmdb_movies_base_url');
    }

    public function __invoke($cardinality) {
        $this->getTopMovies($cardinality);
    }

    /**
     * priv function to format movie title and return movie url
     * @param string $title
     * @param int $id
     * @return string
     */
    private function formatAndReturnMovieUrl($title, $id) {
        $tmdb_url_whitespcaes_replaced = str_replace(' ', '-', $title);
        $tmdb_url_specchars_replaced = preg_replace('/[^A-Za-z0-9\-]/', '-', $tmdb_url_whitespcaes_replaced);
        $tmdb_url_possible_double_hyphens_replaced = preg_replace('/-+/', '-', $tmdb_url_specchars_replaced); 
        $tmdb_url_lowercase = strtolower($tmdb_url_possible_double_hyphens_replaced);

        return $this->moviesBaseUrl.$id.'-'.$tmdb_url_lowercase;
    }

    /**
     * creating movie record and inserting it in movies table
     * @param mixed $request
     * @param mixed $details
     * expecting two arguments, one for the default movie data, another for further details about the movie
     */
    public function createMovie($request, $details) {

        $movie = Movie::create([
            'title' => $request['title'],
            'release_date' => $request['release_date'],
            'overview' => $request['overview'],
            'poster_url' => $this->imagesBaseUrl.$request['poster_path'],
            'tmdb_id' => $request['id'],
            'tmdb_vote_avg' => $request['vote_average'],
            'tmdb_vote_count' => $request['vote_count'],
            'tmdb_url' => $this->formatAndReturnMovieUrl($request['title'], $request['id']),
            'length' => $details['length'],
            'genres' => $details['genres']
        ]);

        return $movie;
    }

    /**
     * @param integer $movie_id
     * @param $director
     * the function expects an array for the possible use case where there is more than one director
     */
    public function createDirector($director_data) {

        $director = Director::where('tmdb_id', $director_data['id'])->first();

        if(!$director) {
            return Director::create([
                'name' => $director_data['name'],
                'tmdb_id' => $director_data['id'],
                'biography' => $director_data['biography'],
                'date_of_birth' => $director_data['birthday'],
            ]);
        }

        return $director;
    }

    /**
     * @param integer $cardinality
     * ask for any number of movies
     */
    public function getTopMovies($cardinality) {
        $number_of_pages = ceil($cardinality / $this->tmdbApiPaginatorSize);

        $reminder = $cardinality % $this->tmdbApiPaginatorSize;

        for($i = 1; $i <= $number_of_pages; $i++) {
            if($i == $number_of_pages) {
                $movies = $this->tmdbController->getTopRatedMovies($i);
                $this->updateOrCreateRecords(array_slice($movies, 0, $reminder));

                if(Movie::count() > $cardinality) {
                    $moviesToDelete = Movie::all()->sortBy('tmdb_vote_avg')->take(Movie::count() - $cardinality);
                    foreach($moviesToDelete as $movieToDelete) {
                        $movieToDelete->delete();
                    }
                }

                return;
            }
            
            $movies = $this->tmdbController->getTopRatedMovies($i);
            $this->updateOrCreateRecords($movies);
        }
    }

    /**
     * @param integer $tmdb_movie_id
     * @return mixed
     */
    public function getMovieDetails($tmdb_movie_id) {
        $details = $this->tmdbController->getGenresAndRuntime($tmdb_movie_id);
        return $details;
    }

    /**
     * @param integer $tmdb_movie_id
     * @return array - return details of directors of a given movie
     */
    public function getDirectors($tmdb_movie_id) {
        $directors = $this->tmdbController->getDirectors($tmdb_movie_id);
        $directors_details = [];

        foreach($directors as $director) {
            array_push($directors_details, $this->tmdbController->getPersonDetails($director));
        }

        return $directors_details;
    }

    /**
     * iterate through api response body recursively,
     * create movie if it does not exists,
     * update movie if it exists but its vote average or vote count has changed,
     * leave record alone if neither of the above
     * @param array $movies
     * @param integer $sizeOfArray
     * @param integer $index
     * @return function
     */
    public function updateOrCreateRecords($movies, $index = 0) {
        if($index == count($movies)) {
            return;
        }

        $movie = Movie::where('tmdb_id', $movies[$index]['id'])->first();

        if(!$movie) {
            $details = $this->getMovieDetails($movies[$index]['id']);
            $directors = $this->getDirectors($movies[$index]['id']);

            $movie = $this->createMovie($movies[$index], $details);

            if(count($directors) > 0) {
                foreach($directors as $director) {
                    $newDirector = $this->createDirector($director);
                    if($newDirector) {
                        $movie->directors()->attach($newDirector->tmdb_id);
                    }
                }
            }
        } 

        else {
            if($movie->tmdb_vote_avg != $movies[$index]['vote_average'] || $movie->tmdb_vote_count != $movies[$index]['vote_count']) {
                $movie->update([
                    'tmdb_vote_average' => $movies[$index]['vote_average'],
                    'vote_count' => $movies[$index]['vote_count']
                ]);
            };
        }

        return $this->updateOrCreateRecords($movies, ++$index);
    }

}