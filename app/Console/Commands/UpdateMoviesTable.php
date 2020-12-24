<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Controller;
use App\Models\Movie;

class UpdateMoviesTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tmdb:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the movies table according to its size with TMDB Api';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cardinality = Movie::count();
        $movieController = new Controller(20);
        $movieController->getTopMovies($cardinality);

        $this->info('Top '.$cardinality.' movies are up to date!');
    }
}
