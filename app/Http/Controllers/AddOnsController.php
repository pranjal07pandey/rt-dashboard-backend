<?php

namespace App\Http\Controllers;

use App\Addons;
use Illuminate\Http\Request;

class AddOnsController extends Controller
{
    public function index(){
        $addons     =    Addons::get();
        return view('dashboard.company.addons.index',compact('addons'));
    }
}
