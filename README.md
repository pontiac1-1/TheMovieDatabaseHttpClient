# TheMovieDatabaseHttpClient
Http Client to get TMDb's Top Rated Movies list and some further details on directors

 ## Start it up by:

###### run docker-compose.yaml
> docker-compose up -d

###### run the application
> php artisan serve --host="0.0.0.0" 

###### run migrations
> php artisan migrate

###### get top 210 movies by artisan
> php artisan tmdb:get 210

###### start scheduler worker to update database
> php artisan schedlue:work

###### or update manually
> php artisan tmdb:update
