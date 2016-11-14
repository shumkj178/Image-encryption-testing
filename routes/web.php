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

Route::get('imageTest1', function () {
    //return the content of the image with unreadable words
    $file = file_get_contents('', FILE_USE_INCLUDE_PATH);
    echo $file;
});

Route::get('imageTest2', function () {
    //return an image
    $im = file_get_contents('');
    header("Content-Type: image/png");

    echo $im;
});

Route::get('image/{id}',function($id) {
    //return desired image
    $fileName = App\Image::find($id);
    $file = File::mimeType(public_path() . '/images/' . 'uploaded-id-' . $fileName->id);
    $image = file_get_contents(public_path() . '/images/' . 'uploaded-id-' . $id);
    header("Content-Type: $file");
    echo $image;
});

//To test out the encryption
Route::get('imageEncrypt/{id}', function($id) {
    $fileName = App\Image::find($id);
    $file = File::mimeType('../public/images/' . $fileName->id);
    $image = file_get_contents('../public/images/' . $fileName->id);
    header("Content-Type: $file");
    $encrypted = Crypt::encrypt($image);
    $decrypted = Crypt::decrypt($encrypted);
    echo $decrypted;
});

//get the file from database and encrypt then save into encrypted folder
Route::get('encrypt/{id}', 'PhotoController@encryptImage');

//read the file from encrypted folder and decrypt
//include let user choose to save or view file
Route::get('showSpec/{id}', 'PhotoController@showSpec');

//read the file, just view
Route::get('showView/{id}', 'PhotoController@showView');

//another way of viewing the file
Route::get('showImage/{id}', function ($id) {
    return "Image $id :- <img src=\"/showView/$id\" />";
});

//testing purpose
Route::get('ip', 'PhotoController@logging');