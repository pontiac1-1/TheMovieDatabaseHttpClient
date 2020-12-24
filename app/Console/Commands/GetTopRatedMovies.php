<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Controller;

class GetTopRatedMovies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tmdb:top {cardinality}';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to get or update database with the top 210 movies from TMDB Top Rated Movies list';
    
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
     * @return mixed
     */
    public function handle()
    {
        $cardinality = $this->argument('cardinality');
        $movieController = new Controller(20);
        $movieController->getTopMovies($cardinality);

        $this->info('Top '.$cardinality.' movies are up to date!');
    }
}
