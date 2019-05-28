<?php

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

use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

Route::get('/', function () {
    return view('welcome');
});


Route::any("download", function () {
    $url = "https://dog.ceo/api/breeds/image/random";

    $client = new Client();
    $response = $client->request('GET', $url);

    $content = $response->getBody();

    $data = json_decode($content, true);

    $path = $data["message"];
    $filename = basename($path);
    Image::make($path)->resize(1000,1000)->save(public_path('images/' . $filename));

    /*\App\Image::create([
        "name" => $filename
    ]);*/


});


Route::get("{width}/{height}" , function ($width, $height) {

    $files = [];
    $filesInFolder = File::files(public_path("images"));
    foreach($filesInFolder as $path) {
        $file = pathinfo($path);
        $files[] = $file['filename'].".jpg";
    }
    $k = array_rand($files);
    $image = $files[$k];

    /*$image = \App\Image::all()->random();
    $path = public_path("images/".$image->name);*/

    $path = public_path("images/".$image);

    $filename = basename($path);
    //Image::make($path)->resize($width,$height)->save(public_path('images/' . $filename));
    //return Image::make($path)->resize($width,$height)->response();
    return Image::make($path)->crop($width,$height)->response();
})->where(['width' => '[0-9]+', 'height' => '[0-9]+']);
