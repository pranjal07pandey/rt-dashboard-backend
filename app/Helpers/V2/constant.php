<?php



function RESPONSEDATA($response){
    if(@$response['data']){
        return response()->json(['data' => $response['data']], $response['status']);
    }else{
        return response()->json(['message' => $response['message']], $response['status']);
    }
};





