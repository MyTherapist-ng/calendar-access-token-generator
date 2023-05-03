<?php

use App\Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/connect', function () {
    $client = (new Calendar())->getClient();
    $authUrl = $client->createAuthUrl();
    return redirect($authUrl);
});

Route::get('/store', function (Request $request) {
    $client = (new Calendar())->getClient();

    $authCode = $request->code;

    // Load previously authorized credentials from a file.
    $credentialsPath = storage_path('keys/client_secret_generated.json');

    // Exchange authorization code for an access token.
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

    // Store the credentials to disk.
    if (!file_exists(dirname($credentialsPath))) {
        mkdir(dirname($credentialsPath), 0700, true);
    }

    file_put_contents($credentialsPath, json_encode($accessToken));

    return dd(json_decode(file_get_contents(storage_path('keys/client_secret_generated.json')), true));
});
