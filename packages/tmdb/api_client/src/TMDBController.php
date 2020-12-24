<?php

namespace TMDB\ApiClient;

use Illuminate\Support\Facades\Http;

class TMDBController {

    private $requestBaseUrl;
    private $api_key;

    public function __construct($baseUrl, $api_key)
    {
        $this->requestBaseUrl = $baseUrl;
        $this->api_key = $api_key;
    }

    /**
     * get top rated movies list from TMDB with chunks of 20
     * @param int $page
     * page to get movies from
     * @return array
     * returns array of movies from a given page
     */
    public function getTopRatedMovies($page) {
        $topRatedEndpoint = '/movie/top_rated';

        $response = Http::get($this->requestBaseUrl.$topRatedEndpoint, [
            'api_key' => $this->api_key,
            'page' => $page,
        ])['results'];
        
        return $response;
    }

    /**
     * get details of a movie by its TMDB id, needed to get runtime and genres of a given movie
     * @param int $id
     * tmdb id of movie
     * @return mixed
     * returns genres and runtime of movie
     */
    public function getGenresAndRuntime($id) {
        $getMovieDetailsEndpoint = '/movie/'.$id;

        $response = Http::get($this->requestBaseUrl.$getMovieDetailsEndpoint, [
            'api_key' => $this->api_key
        ])->json();

        $genreNames = [];

        foreach($response['genres'] as $genre) {
            array_push($genreNames, $genre['name']);
        }

        return [
            'length' =>  $response['runtime'],
            'genres' => $genreNames
        ];
    }

    /**
     * get credits (crew and cast) of a movie by its TMDB id, needed to get retrive the director of the movie if listed
     * @param int $id
     * tmdb id of movie to get directors for
     * @return array
     * array of directors
     */
    public function getDirectors($id) {
        $getMovieCreditsEndpoint = '/movie/'.$id.'/credits'; 

        $response = Http::get($this->requestBaseUrl.$getMovieCreditsEndpoint, [
            'api_key' => $this->api_key
        ])->json();

        $crew = $response['crew'];
        $directors = [];

        foreach($crew as $crew_member) {
            if(str_contains(strtolower($crew_member['job']), 'director')) {
                array_push($directors, $crew_member['id']);
            }
        }

        return $directors;
    }

    /**
     * get details of a person by their TMDB id, needed to retrive further detail on the driector of a given movive
     * @param int $id
     * tmdb id of person to get details of
     * @return mixed
     * person details
     */
    public function getPersonDetails($id) {
        $getPersonDetailsEndpoint = '/person/'.$id;

        $response = Http::get($this->requestBaseUrl.$getPersonDetailsEndpoint, [
            'api_key' => $this->api_key
        ])->json();


        $data = [
            'id' => $response['id'], 
            'name' => $response['name'],
            'biography' => $response['biography'],
            'birthday' => $response['birthday']
        ];

        return $data;
    }
}