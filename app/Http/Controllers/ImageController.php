<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $file = $request->file('image')->store('public'); 
        return $file;
    }
}