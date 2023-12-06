<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function getImage($fileName)
    {
        $path = public_path('storage/images/' . $fileName);

        if (file_exists($path)) {
            $file = file_get_contents($path);
            $type = mime_content_type($path);

            return response($file)->header('Content-Type', $type);
        }

        return response()->json(['message' => 'File not found.'], 404);
    }
}
