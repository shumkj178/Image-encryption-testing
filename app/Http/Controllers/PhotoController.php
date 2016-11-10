<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use finfo;
use Guzzle\Tests\Plugin\Redirect;
use App\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{

    /**
     * Show the form for uploading a new resource.
     *
     * @return Response
     */
    public function upload(){
        return view('imageupload');
    }

    /**
     * Store a newly uploaded resource in storage.
     *
     * @return Response
     */
    public function store(Request $request){
        $image = new Image();
        $this->validate($request, [
            'title' => 'required',
            'image' => 'required'
        ]);
        $image->title = $request->title;
        $image->description = $request->description;
        if($request->hasFile('image')) {
            $file = Input::file('image');
            //getting timestamp
            $timestamp = str_replace([' ', ':'], '-', Carbon::now()->toDateTimeString());

            $name = $timestamp. '-' .$file->getClientOriginalName();

            $type = Input::file('image')->extension();

            $image->filePath = $name;

            $img_data = file_get_contents($file);
            $base64 = base64_encode($img_data);
            $image->src = 'data:image/' . $type . ';base64,' . $base64;


            $file->move(public_path().'/images/', $name);

        }
        $image->save();
//        return $this->create()->with('success', 'Image Uploaded Successfully');
        return $this->show();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function show(){
        $images = Image::all();
        return view('showLists', compact('images'));
    }

    public function encryptImage($id){
        $image = Image::find($id);
        $img_string = explode(',', $image->src);
        $data = base64_decode($img_string[1]);
        $encrypted = Crypt::encrypt($data);
        $filename = 'encrypted-id-' . $image->id;
        file_put_contents(public_path(). '/encrypted/' . $filename, $encrypted);

        //Another way
//        $image = Image::find($id);
//        //use Illuminate\Support\Facades\File;
//        $file = File::mimeType(public_path(). '/images/' . $image->filePath);
//        $imageEncrypted = file_get_contents(public_path(). '/images/' . $image->filePath);
//        header("Content-Type: $file");
//        $filename = 'encrypted-id-' . $image->id . $file;
//        dd($filename);
//        $imageEncrypted = file_put_contents(public_path(),'/encrypted/',$filename);
    }

    public function showSpec($id){
//        $imageEncrypted = file_get_contents(public_path() . '/encrypted/' . 'encrypted-id-' . $id);
//        $decrypted = Crypt::decrypt($imageEncrypted);
//        $file = File::mimeType(public_path() . '/encrypted/' . 'encrypted-id-' . $id);
//        header("Content-Type: $file");
//        $data = array(
//            'var1' => $image
//        );
//        $encryptedContents = Storage::get(public_path() . '/encrypted/' . 'encrypted-id-' . $id);

        //refer to http://stackoverflow.com/questions/34624118/working-with-encrypted-files-in-laravel-how-to-download-decrypted-file
        //make changes on file get contents
        $imageEncrypted = file_get_contents(public_path() . '/encrypted/' . 'encrypted-id-' . $id);
        $decryptedContents = Crypt::decrypt($imageEncrypted);

        return response()->make($decryptedContents, 200, array(
            'Content-Type' => (new finfo(FILEINFO_MIME))->buffer($decryptedContents),
            'Content-Disposition' => 'attachment; filename="' . pathinfo(public_path() . '/encrypted/' . 'encrypted-id-' . $id, PATHINFO_BASENAME) . '"'
        ));

        echo $decrypted;
    }
}