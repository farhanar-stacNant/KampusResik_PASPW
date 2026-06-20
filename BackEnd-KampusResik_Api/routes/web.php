<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

Route::get('/', function () {
    return view('welcome');
});

Route::get('storage/sampah/{filename}', function ($filename) {
    $path = 'public/sampah/' . $filename;
    if (!Storage::exists($path)) {
        abort(404);
    }
    
    $file = Storage::get($path);
    $type = Storage::mimeType($path);
    
    return Response::make($file, 200)->header("Content-Type", $type);
});
