<?php

use Dingo\Api\Routing\Router;
use Illuminate\Http\Request;
use App\Games;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
 * Welcome route - link to any public API documentation here
 */
Route::get('/', function () {
    echo 'Welcome to our API';
});


Route::middleware('throttle:30,1')->get('/listEvoplay',function() {
$Games = DB::table('gamelist')->get();
return $Games;
});

/** @var \Dingo\Api\Routing\Router $api */
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {


    $api->group(['prefix' => 'callback'], function (Router $api) {
            $api->any('/rise/balance', 'App\Http\Controllers\GameControllers\RiseController@balance');
            $api->any('/rise/bet', 'App\Http\Controllers\GameControllers\RiseController@bet');
            $api->any('/c2/endpoint', 'App\Http\Controllers\GameControllers\C2GamingController@endpoint');
            $api->any('/mascot/endpoint', 'App\Http\Controllers\GameControllers\C2GamingController@endpoint');
            $api->any('/evoplay/endpoint', 'App\Http\Controllers\GameControllers\EvoplayController@endpoint');

    });

    $api->group(['prefix' => 'v2'], function (Router $api) {
            $api->any('/createSession', 'App\Http\Controllers\GameControllers\SessionController@createSession');
            $api->any('/listGames', function() { $Games = DB::table('gamelist')->get(); return $Games; });
    });


    $api->group(['prefix' => 'session'], function (Router $api) {
            $api->any('/real/mascot/{playerId}/{gameId}/{casino_id}/{bankgroup}/{statichost}', 'App\Http\Controllers\GameControllers\C2GamingController@MascotRealmoney');
            $api->any('/demo/mascot/{game}', 'App\Http\Controllers\GameControllers\C2GamingController@MascotDemo');
            $api->any('/real/c2/{playerId}/{gameId}/{casino_id}/{bankgroup}/{statichost}', 'App\Http\Controllers\GameControllers\C2GamingController@C2GamingRealmoney');
            $api->any('/demo/c2/{game}', 'App\Http\Controllers\GameControllers\C2GamingController@C2GamingDemo');
            $api->any('/real/rise/live', 'App\Http\Controllers\GameControllers\RiseController@createLive');
            $api->any('/real/rise/slots', 'App\Http\Controllers\GameControllers\RiseController@createSlots');
            $api->any('/real/evoplay/{playerId}/{gameId}/{casino_id}/{mode}', 'App\Http\Controllers\GameControllers\EvoplayController@createSlots');       
            $api->any('/demo/evoplay/{playerId}/{gameId}/{casino_id}/{mode}', 'App\Http\Controllers\GameControllers\EvoplayController@createSlots');       

    });

    $api->group(['prefix' => 'freespins'], function (Router $api) {
            $api->any('/{playerId}/{game_id}/{casino_id}/{spins}/{spinsvalue}', 'App\Http\Controllers\GameControllers\EvoplayController@createFreeSlots');
 
    });

    $api->group(['prefix' => 'staging'], function (Router $api) {
            $api->any('/evoplay/list', 'App\Http\Controllers\GameControllers\EvoplayController@list');           
    });

    /*
     * Authenticated routes
    $api->group(['middleware' => ['api.auth']], function (Router $api) {
        /*
         * Authentication
        $api->group(['prefix' => 'auth'], function (Router $api) {
            $api->group(['prefix' => 'jwt'], function (Router $api) {
                $api->get('/refresh', 'App\Http\Controllers\Auth\AuthController@refresh');
                $api->delete('/token', 'App\Http\Controllers\Auth\AuthController@logout');
            });

            $api->get('/me', 'App\Http\Controllers\Auth\AuthController@getUser');
        });

        /*
         * Users
        $api->group(['prefix' => 'users', 'middleware' => 'check_role:admin'], function (Router $api) {
            $api->get('/', 'App\Http\Controllers\UserController@getAll');
            $api->get('/{uuid}', 'App\Http\Controllers\UserController@get');
            $api->post('/', 'App\Http\Controllers\UserController@post');
            $api->put('/{uuid}', 'App\Http\Controllers\UserController@put');
            $api->patch('/{uuid}', 'App\Http\Controllers\UserController@patch');
            $api->delete('/{uuid}', 'App\Http\Controllers\UserController@delete');
        });

        /*
         * Roles
        $api->group(['prefix' => 'roles'], function (Router $api) {
            $api->get('/', 'App\Http\Controllers\RoleController@getAll');
        });
    });         */

});
