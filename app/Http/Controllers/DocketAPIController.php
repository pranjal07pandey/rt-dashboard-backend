<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocketAPIController extends Controller
{
    /**
     *
     */
    public function docket(){
        $jsonData[]   =   array("field_number"=> 1,"type"=> "Location",
                            "label_title"=> "Job Location",
                            "description_text"=> "Please fill out your location",
                            "allow_gps"=> 0,
                            "prefill_items"=> array("location1", "location2")
                            );
        $jsonData[]     =    array("field_number"   =>  2,
                            "type"=> "Description",
                            "label_title"=> "Job Description",
                            "description_text"=> "Please fill out your job details",
                            "prefill_items"=> array("Brick laying", "Scafolding")
                            );
        $jsonData[]     =    array("field_number"=> 3,
                            "type"=> "Text",
                            "label_title"=> "Order Number",
                            "description_text"=> "",
                            "prefill_items"=> array("xyz123", "xyd115")
                            );
        $jsonData[]     =   array("field_number"=> 4,
                            "type"=> "Signature",
                            "label_title"=> "Foreman Signature",
                            "description_text"=> "Don’t forget foreman signature!",
                            "prefill_items"=> array()
                            );
        $jsonData[]       =   array("field_number"=> 5,
                            "type"  =>   "Image",
                            "label_title"   =>   "Proof of Job",
                            "description_text"  =>   "Take photos of your work!",
                            "num_images_allowed"    =>   5,
                            "prefill_items" => array("img1.png")
                            );
        return response()->json($jsonData);
    }
}
