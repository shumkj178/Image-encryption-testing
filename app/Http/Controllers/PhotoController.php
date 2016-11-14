<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Record;
use finfo;
use Guzzle\Tests\Plugin\Redirect;
use App\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
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
     * Store a newly uploaded resource in public folder.
     *
     * @return Response
     */
    public function store(Request $request){
        $image = new Image();
        $record = new Record();
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required'
        ]);
        $image->title = $request->title;
        $image->description = $request->description;
        if($request->hasFile('image')) {
            $file = Input::file('image');
            //getting timestamp
//            $timestamp = str_replace([' ', ':'], '-', Carbon::now()->toDateTimeString());

//            $name = $timestamp. '-' .$file->getClientOriginalName();

//            $type = Input::file('image')->extension();

//            $image->filePath = $name;

//            $img_data = file_get_contents($file);
//            $base64 = base64_encode($img_data);
//            $image->src = 'data:image/' . $type . ';base64,' . $base64;
            $data = DB::table('images')->max('id');
            $id = $data +1;
            $name = 'uploaded-id-' . $id;

            $record->image_id = $id;
            $record->time = Carbon::now()->toTimeString();
            $record->ip_address = $request->ip();
            $record->logs = 'Image ' . $image->title . ' id-' . $id . ' uploaded';

            $file->move(public_path().'/images/', $name);
        }
        $image->save();
        $record->save();
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

    public function encryptImage($id, Request $request){
        $image = Image::find($id);
//        $img_string = explode(',', $image->src);
//        $data = base64_decode($img_string[1]);
        $data = file_get_contents(public_path() . '/images/' . 'uploaded-id-' . $id);
        $encrypted = Crypt::encrypt($data);
        $filename = 'encrypted-id-' . $image->id;
        file_put_contents(public_path(). '/encrypted/' . $filename, $encrypted);
        $record = new Record();
        $record->image_id = $id;
        $record->time = Carbon::now()->toTimeString();
        $record->ip_address = $request->ip();
        $record->logs = 'Image ' . $image->title . ' id-' . $id . ' encrypted';
        $record->save();
    }

    public function showSpec($id, Request $request){

        //refer to http://stackoverflow.com/questions/34624118/working-with-encrypted-files-in-laravel-how-to-download-decrypted-file
        //make changes on file get contents
        $image = Image::find($id);
        $imageEncrypted = file_get_contents(public_path() . '/encrypted/' . 'encrypted-id-' . $id);
        $decryptedContents = Crypt::decrypt($imageEncrypted);

        $record = new Record();
        $record->image_id = $id;
        $record->time = Carbon::now()->toTimeString();
        $record->ip_address = $request->ip();
        $record->logs = 'Image ' . $image->title . ' id-' . $id . ' viewed';
        $record->save();

        return response()->make($decryptedContents, 200, array(
            //return the content
            'Content-Type' => (new finfo(FILEINFO_MIME))->buffer($decryptedContents),
            //let user choose to either view content or save content
            'Content-Disposition' => 'attachment; filename="' . pathinfo(public_path() . '/encrypted/' . 'encrypted-id-' . $id, PATHINFO_BASENAME) . '"'
        ));
    }

    public function showView($id, Request $request){
        $image = Image::find($id);
        $imageEncrypted = file_get_contents(public_path() . '/encrypted/' . 'encrypted-id-' . $id);
        $decryptedContents = Crypt::decrypt($imageEncrypted);

        $record = new Record();
        $record->image_id = $id;
        $record->time = Carbon::now()->toTimeString();
        $record->ip_address = $request->ip();
        $record->logs = 'Image ' . $image->title . ' id-' . $id . ' viewed';
        $record->save();

        return response()->make($decryptedContents, 200, array(
            'Content-Type' => (new finfo(FILEINFO_MIME))->buffer($decryptedContents),
        ));
    }

    //testing purpose
    public function printIP(Request $request){
        // #1
//        $ipAddress = $_SERVER['REMOTE_ADDR'];
//        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
//            $ipAddress = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
//        }
//        return $ipAddress;

        //#2
        $ipAddress = $request->ip();
        return 'IP Address : '. $ipAddress;
    }

    //testing purpose
    public function logging(){
        $log = str_replace([' ', ':'], '-', Carbon::now()->toDateTimeString());
        $log2 = Carbon::now()->toTimeString();
        $data = DB::table('images')->max('id');
        echo $data +1 . '<br>';
        echo $log . '<br>';
        echo $log2 . '<br>';
    }
}