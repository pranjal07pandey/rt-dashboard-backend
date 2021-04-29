<?php

namespace App\Helpers\V2;
use File;
use Storage;

class AmazoneBucket
{

    public static function url(){
        if(parse_url($_SERVER['SERVER_NAME'])['path'] != "127.0.0.1"){
            return "https://test-record-time.s3-ap-southeast-2.amazonaws.com/";
        }else{
            return "http://localhost:8000/";
        }
    }

    public static function fileExist($path){
        if(parse_url($_SERVER['SERVER_NAME'])['path'] != "127.0.0.1"){
            return Storage::disk('s3')->has($path);
        }else{
            return File::exists($path);
        }
    }
}