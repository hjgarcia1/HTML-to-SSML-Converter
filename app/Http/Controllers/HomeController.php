<?php

namespace App\Http\Controllers;

use App\Ssml;

class HomeController extends Controller
{
    public function index()
    {
        $ssmls = Ssml::all();
        return view('home', compact('ssmls'));
    }
}
