<?php
declare(strict_types=1);
namespace Root\Garden;

use Onesimus\Router\Router;

Router::register404Route('Root\Garden\Controllers\IndexController@error404');

Router::get('/', 'Root\Garden\Controllers\IndexController@index');
Router::post('/upload', 'Root\Garden\Controllers\ImageController@upload_image');

Router::get('/wishlist', 'Root\Garden\Controllers\SeedController@wishlist');
Router::get('/seeds', 'Root\Garden\Controllers\SeedController@seeds');
Router::get('/seeds/{id}', 'Root\Garden\Controllers\SeedController@seeds_view_get');
Router::post('/seeds', 'Root\Garden\Controllers\SeedController@seeds_post');
Router::get('/seeds/new', 'Root\Garden\Controllers\SeedController@seeds_new_get');
Router::post('/seeds/new', 'Root\Garden\Controllers\SeedController@seeds_new_post');
Router::get('/seeds/edit/{id}', 'Root\Garden\Controllers\SeedController@seeds_edit_get');
Router::post('/seeds/edit/{id}', 'Root\Garden\Controllers\SeedController@seeds_edit_post');

Router::get('/plantings', 'Root\Garden\Controllers\PlantingController@plantings');
Router::get('/plantings/{id}', 'Root\Garden\Controllers\PlantingController@plantings_view_get');
Router::get('/plantings/gallery/{id}', 'Root\Garden\Controllers\PlantingController@plantings_gallery_get');
Router::post('/plantings', 'Root\Garden\Controllers\PlantingController@plantings_post');
Router::get('/plantings/new', 'Root\Garden\Controllers\PlantingController@plantings_new_get');
Router::post('/plantings/new', 'Root\Garden\Controllers\PlantingController@plantings_new_post');
Router::get('/plantings/edit/{id}', 'Root\Garden\Controllers\PlantingController@plantings_edit_get');
Router::post('/plantings/edit/{id}', 'Root\Garden\Controllers\PlantingController@plantings_edit_post');

Router::get('/beds', 'Root\Garden\Controllers\BedController@beds');
Router::get('/beds/{id}', 'Root\Garden\Controllers\BedController@beds_view_get');
Router::post('/beds', 'Root\Garden\Controllers\BedController@beds_post');
Router::get('/beds/new', 'Root\Garden\Controllers\BedController@beds_new_get');
Router::post('/beds/new', 'Root\Garden\Controllers\BedController@beds_new_post');
Router::get('/beds/edit/{id}', 'Root\Garden\Controllers\BedController@beds_edit_get');
Router::post('/beds/edit/{id}', 'Root\Garden\Controllers\BedController@beds_edit_post');

Router::get('/logs', 'Root\Garden\Controllers\LogController@logs');
Router::get('/logs/{id}', 'Root\Garden\Controllers\LogController@logs_view_get');
Router::post('/logs', 'Root\Garden\Controllers\LogController@logs_post');
Router::get('/logs/new', 'Root\Garden\Controllers\LogController@logs_new_get');
Router::post('/logs/new', 'Root\Garden\Controllers\LogController@logs_new_post');
Router::get('/logs/edit/{id}', 'Root\Garden\Controllers\LogController@logs_edit_get');
Router::post('/logs/edit/{id}', 'Root\Garden\Controllers\LogController@logs_edit_post');
