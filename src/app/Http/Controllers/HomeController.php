<?php

namespace App\Http\Controllers;

use App\Models\Banner;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'banners' => Banner::where('activo', true)->orderBy('orden')->get(),
        ]);
    }
}
