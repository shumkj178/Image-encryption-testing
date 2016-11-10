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

Route::get('/', function () {
    return view('welcome');
});

Route::get('imageUploadForm', 'PhotoController@upload' );

Route::post('imageUploadForm', 'PhotoController@store' );

Route::get('showLists', 'PhotoController@show' );

Route::get('encrypt', function () {
    $work = 'love';
    echo $work . '<br>';
    $encrypted = Crypt::encrypt($work);
    echo $encrypted .'<br>';
    $decrypted = Crypt::decrypt($encrypted);
    echo $decrypted . '<br>';
});

//Route::get('image/{id}', function ($id) {
//    $image = App\Image::find($id);
//    echo $image->title . '<br>';
//    echo $image->description . '<br>';
//    echo $image->filePath . '<br>';
//});

Route::get('imageTest1', function () {
    //return the content of the image with unreadable words
    $file = file_get_contents('../public/images/2016-11-08-06-06-21-zubat hola.jpg', FILE_USE_INCLUDE_PATH);
    echo $file;
});

Route::get('imageTest2', function () {
    $im = file_get_contents("../public/images/2016-11-08-06-27-42-418369.png");
    header("Content-Type: image/png");

    echo $im;
});

Route::get('imageTest/{id}',function($id) {
    $fileName = App\Image::find($id);
    $file = File::mimeType('../public/images/' . $fileName->filePath);
    $image = file_get_contents('../public/images/' . $fileName->filePath);
    header("Content-Type: $file");
    echo $image;
});